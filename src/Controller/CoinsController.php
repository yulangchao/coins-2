<?php
// src/Controller/ArticlesController.php

namespace App\Controller;
use Cake\Error\Debugger;

class CoinsController extends AppController
{
    public function index()
    {

    }


    public function getHistory()
    {
        $this->autoRender = false;
        $data = array("method" => "chart", "pair_id" => $this->request->query['code'],"mode"=>24);                                                                    
        $data_string = json_encode($data);                                                                                   
                                                                                                                             
        $ch = curl_init('https://yobit.net/ajax/system_chart.php');                                                                      
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);                                                                  
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
        curl_setopt($ch, CURLOPT_HTTPHEADER, array());                                                                           
        $result = curl_exec($ch);

        $this->response->type('json');
        $this->response->body($result);
        return $this->response;

    }

    public function getdata()
    {

        $this->autoRender = false;
        $data = array("method" => "chart", "pair_id" => $this->request->query['code'],"mode"=>24);                                                                    
        $data_string = json_encode($data);                                                                                   
                                                                                                                             
        $ch = curl_init('https://yobit.net/ajax/system_status_data.php');                                                                      
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);                                                                  
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
        curl_setopt($ch, CURLOPT_HTTPHEADER, array());                                                                                                                   
                                                                                                                             
        $result = curl_exec($ch);

        $this->response->type('json');
        $this->response->body($result);
        return $this->response;

    }
}
