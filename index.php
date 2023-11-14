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
$sec_type=$params[2];
$sec_id=$params[3];

//die(print_r($_POST));

switch($method){
    case 'GET':
        if ($fir_type === 'promo') {
            if (isset($fir_id)) {
                getPromotion($connect, $fir_id);
            } else {
                getPromotions($connect);
            }
        }
        break;
    case 'POST':
        if ($fir_type === 'promo'&&$sec_type===null)
        {
            addPromotion($connect, $_POST);
        } 
        elseif ($fir_type === 'promo' && $sec_type === 'participant' && isset($fir_id)) 
        {
            addParticipant($connect, $fir_id, $_POST);
        } 
        elseif ($fir_type === 'promo' && $sec_type === 'prize') 
        {
            addPrize($connect, $fir_id, $_POST);
        }
        elseif ($fir_type === 'promo' && $sec_type === 'raffle' && isset($fir_id)) {
            conductRaffle($connect, $fir_id);
        }
        break;
    case 'PUT':
        if ($fir_type === 'promo' && isset($fir_id)) 
        {
            $data = file_get_contents('php://input');
            $data = json_decode($data, true);
            updatePromo($connect, $fir_id, $data);
        }
        break;
    case 'DELETE':
        if ($fir_type === 'promo'&&$sec_type===null ) {
            if (isset($fir_id))
            {
                $data = file_get_contents('php://input');
                $data = json_decode($data,true);
                deletePromo($connect,$fir_id); 
            }

        } 
        elseif ($fir_type === 'promo' && $sec_type === 'participant' && isset($sec_id)) 
        {
            $data = file_get_contents('php://input');
            $data = json_decode($data,true);
            deleteParticipant($connect, $fir_id, $sec_id);
        } 
        elseif ($fir_type === 'promo' && $sec_type === 'prize' && isset($sec_id)) 
        {
            deletePrize($connect, $fir_id, $sec_id);
        }
        break;
}


