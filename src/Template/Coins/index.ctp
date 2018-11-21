<script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
<script src="https://code.highcharts.com/stock/highstock.js"></script>
<script src="https://code.highcharts.com/stock/modules/drag-panes.js"></script>
<script src="https://code.highcharts.com/stock/modules/exporting.js"></script>
<link rel="stylesheet" type="text/css" href="/css/style.css">


<style>

.viewport{
    overflow-y: auto !important;
}
.thumb{
    display:none;
}
</style>

<script>
var chart;
function getHistory(code){
    $.get( "/coins/getHistory?code="+code, function( data ) {
            // split the data set into ohlc and volume
                    data = data.data;
                    var ohlc = [],
                    volume = [],
                    dataLength = data.length,
                    // set the allowed units for data grouping
                    groupingUnits = [
                        [
                        'week', // unit name
                        [1] // allowed multiples
                        ],
                        [
                        'month', [1, 2, 3, 4, 6]
                        ]
                    ],

                    i = 0;
                    for (i; i < dataLength; i += 1) {
                    ohlc.push([
                        data[i][0], // the date
                        data[i][3], // open
                        data[i][4], // high
                        data[i][5], // low
                        data[i][6] // close
                    ]);

                    volume.push([
                        data[i][0], // the date
                        data[i][2] // the volume
                    ]);
                    }

                    // create the chart
                    chart = Highcharts.stockChart('container', {

                    rangeSelector: {
                        selected: 1
                    },

                    title: {
                        text: (code == 4496 ? "AIB/USD" : "BTC/USD")+ ' Historical'
                    },

                    yAxis: [{
                        labels: {
                        align: 'right',
                        x: -3
                        },
                        title: {
                        text: 'OHLC'
                        },
                        height: '60%',
                        lineWidth: 2,
                        resize: {
                        enabled: true
                        }
                    }, {
                        labels: {
                        align: 'right',
                        x: -3
                        },
                        title: {
                        text: 'Volume'
                        },
                        top: '65%',
                        height: '35%',
                        offset: 0,
                        lineWidth: 2
                    }],

                    tooltip: {
                        split: true
                    },

                    series: [{
                        type: 'candlestick',
                        name: 'AAPL',
                        data: ohlc,
                        dataGrouping: {
                        units: groupingUnits
                        }
                    }, {
                        type: 'column',
                        name: 'Volume',
                        data: volume,
                        yAxis: 1,
                        dataGrouping: {
                        units: groupingUnits
                        }
                    }]
                    });
                    

    });
}
function updateHistory(code){
    $.get( "/coins/getHistory?code="+code, function( data ) {
            // split the data set into ohlc and volume
                    data = data.data;
                    var ohlc = [],
                    volume = [],
                    dataLength = chart.series[0].data.length,
                    // set the allowed units for data grouping
                    groupingUnits = [
                        [
                        'week', // unit name
                        [1] // allowed multiples
                        ],
                        [
                        'month', [1, 2, 3, 4, 6]
                        ]
                    ],

                    i = 0;
                    if (chart.series[0].data[dataLength-1].x == data.slice(-1)[0][0] ){
                        chart.series[0].removePoint(dataLength-1, false, false);
                        chart.series[1].removePoint(dataLength-1, false, false);
                        chart.series[0].addPoint([                       
                                data.slice(-1)[0][0], // the date
                                data.slice(-1)[0][3], // open
                                data.slice(-1)[0][4], // high
                                data.slice(-1)[0][5], // low
                                data.slice(-1)[0][6], // low
                        ], true, false);
                        
                        chart.series[1].addPoint([                       
                            data.slice(-1)[0][0], // the date
                            data.slice(-1)[0][2], // open
                        ], true, false);
                    } else{
                        chart.series[0].addPoint([                       
                                data.slice(-1)[0][0], // the date
                                data.slice(-1)[0][3], // open
                                data.slice(-1)[0][4], // high
                                data.slice(-1)[0][5], // low
                                data.slice(-1)[0][6], // low
                        ], true, false);
                        
                        chart.series[1].addPoint([                       
                            data.slice(-1)[0][0], // the date
                            data.slice(-1)[0][2], // open
                        ], true, false);
                    }


    });
}
function getdata(code){
    $.get( "/coins/getdata?code="+code, function( data ) {
        var last = data.info.last;
        var high = data.info.high24;
        var low = data.info.low24;
        var vol2 = data.info.vol2
        var bestbuy = data.info.bestbuy;
        var bestsell = data.info.bestsell;
        var sellord = data.sellord;
        var buyord = data.buyord;
        $('#sellord_table').empty();
        $('#buyord_table').empty();
        for (let sell of sellord){
            var content = '<tr class="clRow " a="15.48387096" p="0.04444000" ac="15.48387096" tc="0.68810322" title="Total '+title+': '+sell.a +', Total USD: '+sell.t +'">\
                    <td width="35%" class="first">'+sell.p +'</td>\
                    <td width="38%">'+sell.a +'</td>\
                    <td width="27%">'+sell.t +'</td>\
                    </tr>'
            $('#sellord_table').append(content);
        }

        for (let buy of buyord){
            var content = '<tr class="clRow " a="15.48387096" p="0.04444000" ac="15.48387096" tc="0.68810322" title="Total '+title+': '+buy.a +', Total USD: '+buy.t +'">\
                    <td width="35%" class="first">'+buy.p +'</td>\
                    <td width="38%">'+buy.a +'</td>\
                    <td width="27%">'+buy.t +'</td>\
                    </tr>'
            $('#buyord_table').append(content);
        }

        
        $('#label_last').text(last);
        $('#label_high24').text(high);
        $('#label_low24').text(low);
        $('#label_vol24').text(vol2+" USD");
        $('#label_bestbuy').text(bestbuy);
        $('#label_bestsell').text(bestsell);
        $('.clBuyForm input[name="price"]').val(bestbuy);
        $('.clSellForm input[name="price"]').val(bestsell);

        $('.clBuyForm input[name="total"]').val($('.clBuyForm input[name="amount"]').val()*bestbuy);
        $('.clSellForm input[name="total"]').val($('.clSellForm input[name="amount"]').val()*bestsell);
        $('.clBuyForm input[name="fee"]').val($('.clBuyForm input[name="total"]').val()*0.002);
        $('.clSellForm input[name="fee"]').val($('.clSellForm input[name="total"]').val()*0.002);
        $('.clBuyForm input[name="totalfee"]').val($('.clBuyForm input[name="total"]').val()*1.002);
        $('.clSellForm input[name="totalfee"]').val($('.clSellForm input[name="total"]').val()*1.002);
    });
}
$(document).ready(function() {
    var type =location.search.replace('?type=','');
    var pair = 0;
    title = "BTC";
    $('.c_1').text(type.replace('-','/'));
    switch (type){
        case "AIB-USD":
          pair = 4496
          title = "AIB";
            break;
        case "BTC-USD":
           pair = 50001
           title = "BTC";
            break;
        efault:
           pair = 50001
           title = "BTC";
    }
    getHistory(pair);
    getdata(pair);

    $('input[name="amount"] ').on("change paste keyup", function() {
        getdata(pair);
    });

    
    $('.clBuyForm input[name="amount"] ').next().text(title);
    $('.clSellForm input[name="amount"] ').next().text(title);
    $('.c-type').text(title);
    $('.clBuyForm input[name="amount"]').val(1);
    $('.clSellForm input[name="amount"]').val(1);

    setInterval(function(){ getdata(pair);updateHistory(pair); }, 5000);
    



});
</script>


<div class="center_box" id="data-pjax-container">
  <div class="top_center">
    <ul class="top_center_list">
      <li class="c_1">AIB <span>/</span> USD</li>
      <li class="c_2">Last: <span class="label-type1" id="label_last">0</span></li>
      <li class="c_3">24High: <span id="label_high24">0</span></li>
      <li class="c_4">24Low: <span id="label_low24">0</span></li>
      <li class="c_5">24V: <span id="label_vol24">0 USD</span></li>
    </ul>

    <div id="container" style="height: 400px; min-width: 310px"></div>


    <style>
      .chart_container a {
        text-decoration: none;
        font-weight: normal;
        color: #444;
        background-color: #F7F7F7;
        padding: 3px;
      }

      .chart_container a:hover {
        background-color: #e7f0f9;
      }

      .chart_container a.active {
        color: #fff;
        background-color: #0AADEF;
      }

    </style>
  </div>
  <div class="col_1">
    <div class="buy_box fild_box">
      <div class="inset clBuyForm">
        <input type="hidden" name="order_type" value="1">
        <input type="hidden" name="fee_type" value="1">
        <div class="meta">
          <div class="all_title title">BUY</div>
          <div class="sm" id="label_bestbuy">0.04444000</div>
        </div>
        <div class="line_first">
          <span class="c1">Balance:</span>
          <a href="javascript:void(0)" class="c2 clBuyBalance"><span id="label_buy_balance">0.00000000</span> USD</a>
        </div>
        <div class="line"><span>Amount:</span>
          <div class="poles">
            <input name="amount" maxlength="25" type="text" value="0"><span class="currency">AIB</span></div>
        </div>
        <div class="line"><span>Price:</span>
          <div class="poles">
            <input name="price" maxlength="25" type="text" value="0.04444000"><span class="currency">USD</span></div>
        </div>
        <div class="line"><span>Total:</span>
          <div class="poles">
            <input name="total" maxlength="25" type="text" value="0"><span class="currency">USD</span></div>
        </div>
        <div class="line"><span>Fee (0.2%):</span>
          <div class="poles">
            <input name="fee" maxlength="25" type="text" value="0.00008888" disabled=""><span class="currency">USD</span></div>
        </div>
        <div class="line"><span>Total+Fee:</span>
          <div class="poles">
            <input name="totalfee" maxlength="25" type="text" value="0.04452888" disabled=""><span class="currency">USD</span></div>
        </div>
        <input type="button" class="clCreateOrder" origin="Buy" value="Buy">
      </div>
    </div>
    <div class="sell_orders_box">
      <div class="all_title title">Sell order</div>
      <div class="result">
        <table class="sell_orders" width="100%">
          <thead>
            <tr>
              <th width="35%" class="first">Price</th>
              <th width="38%" class="c-type">AIB</th>
              <th width="27%">USD</th>
            </tr>
          </thead>
        </table>
      </div>
      <div class="scrolling" id="scrollbar3">
        <div class="scrollbar" style="height: 120px;">
          <div class="track" style="height: 120px;">
            <div class="thumb" style="top: 0px; height: 14.4px;"></div>
          </div>
        </div>
        <div class="viewport">
          <div class="overview" style="top: 0px;">
            <table class="sell_orders" width="100%">
                <tbody id="sellord_table">
                </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col_2">
    <div class="sell_box fild_box">
      <div class="inset clSellForm" style="position:relative;">
        <input type="hidden" name="order_type" value="2">
        <input type="hidden" name="fee_type" value="2">
        <div class="meta">
          <div class="all_title title">SELL</div>
          <div class="sm" id="label_bestsell">0.01480100</div>
        </div>
        <div class="line_first">
          <span class="c1">Balance:</span>
          <a href="javascript:void(0)" class="c2 clSellBalance"><span id="label_sell_balance">0.00000000</span> <span class="c-type">AIB<span></a>
        </div>
        <div class="line"><span>Amount:</span>
          <div class="poles">
            <input name="amount" maxlength="25" type="text" value="0"><span class="currency">AIB</span></div>
        </div>
        <div class="line"><span>Price:</span>
          <div class="poles">
            <input name="price" maxlength="25" type="text" value="0.01480100"><span class="currency">USD</span></div>
        </div>
        <div class="line"><span>Total:</span>
          <div class="poles">
            <input name="total" maxlength="25" type="text" value="0"><span class="currency">USD</span></div>
        </div>
        <div class="line"><span>Fee (0.2%):</span>
          <div class="poles">
            <input name="fee" maxlength="25" type="text" value="0.00002960" disabled=""><span class="currency">USD</span></div>
        </div>
        <div class="line"><span>Total-Fee:</span>
          <div class="poles">
            <input name="totalfee" maxlength="25" type="text" value="0.01477140" disabled=""><span class="currency">USD</span></div>
        </div>
        <input type="button" class="clCreateOrder" origin="Sell" value="Sell">
      </div>
    </div>
    <div class="buy_orders_box">
      <div class="all_title title">Buy order</div>
      <div class="result">
        <table class="sell_orders" width="100%">
          <thead>
            <tr>
              <th width="35%" class="first">Price</th>
              <th width="38%" class="c-type">AIB</th>
              <th width="27%">USD</th>
            </tr>
          </thead>
        </table>
      </div>
      <div class="scrolling" id="scrollbar4">
        <div class="scrollbar" style="height: 120px;">
          <div class="track" style="height: 120px;">
            <div class="thumb" style="top: 0px; height: 22.5px;"></div>
          </div>
        </div>
        <div class="viewport">
          <div class="overview" style="top: 0px;">
            <table class="sell_orders" width="100%">
                <tbody id="buyord_table">
                </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col_3">
    <div class="trade_history myord0" id="thc">
      <div class="all_title title">Trade History</div>
      <div class="result myord">
        <table width="100%" class="trade_history_table">
          <thead>
            <tr>
              <th width="27%" class="first">Time</th>
              <th width="14%"></th>
              <th width="30%">Price</th>
              <th width="29%">
                <table>
                  <thead>
                    <tr>
                      <th width="75%"  class="c-type">AIB</th>
                      <th width="25%">[C]</th>
                    </tr>
                  </thead>
                </table>
              </th>
            </tr>
          </thead>
        </table>
      </div>
      <div class="scrolling myord" id="scrollbar5">
        <div class="scrollbar" style="height: 0px;">
          <div class="track" style="height: 0px;">
            <div class="thumb"></div>
          </div>
        </div>
        <div class="viewport">
          <div class="overview" style="top: 0px;">
            <table width="100%" class="trade_history_table">
              <tbody id="myord_table">
                <tr>
                  <td class="first" colspan="4">No open orders.</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <div class="result">
        <table width="100%" class="trade_history_table">
          <thead>
            <tr>
              <th width="27%" class="first">Time</th>
              <th width="14%"></th>
              <th width="30%">Price</th>
              <th width="29%" class="c-type">AIB</th>
            </tr>
          </thead>
        </table>
      </div>
      <div class="scrolling" id="scrollbar6">
        <div class="scrollbar" style="height: 390px;">
          <div class="track" style="height: 390px;">
            <div class="thumb" style="top: 0px; height: 223.676px;"></div>
          </div>
        </div>
        <div class="viewport">
          <div class="overview" style="top: 0px;">
            <table width="100%" class="trade_history_table">
            <tbody id="microhistory_table">
            <tr class="green">
                  <td width="27%" class="first" title="2018-01-27 00:01:45">Nodata</td>
                  <td width="14%">BUY</td>
                  <td width="30%">Nodata</td>
                  <td width="29%">Nodata</td>
                </tr>
            </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="clear"></div>
</div>
