<?php

header('Content-Type: application/json');

require 'connect.php';
require 'function.php';

$method=$_SERVER['REQUEST_METHOD'];

$q = $_GET['q'];
$params=explode('/',$q);

//die(print_r($params));
$fir_type=$params[0];
$fir_id=$params[1];
$sec_typ=$params[2];
$sec_id=$params[3];


switch($method){
    case 'GET':
    if ($fir_type === 'promo') {
        if (isset($id)) {
            getPromotion($connect, $fir_id);
        } else {
            getPromotions($connect);
        }
    }
    
    break;
    case 'POST':
        if ($fir_type === 'promo') {
            addPromotion($connect,$_POST);
        }
        break;
    case 'PUT';
    if ($fir_type === 'promo') {
        if (isset($fir_id)) {
        $data = file_get_contents('php://input');
        $data = json_decode($data,true);
        updatePromo($connect,$fir_id,$data); 
        }
    }
        break;
    case 'DELETE':
        if ($fir_type === 'promo') {
            if (isset($fir_id)) {
            $data = file_get_contents('php://input');
            $data = json_decode($data,true);
            deletePromo($connect,$fir_id); 
            }
        }
        break;
}
