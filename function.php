<?php
function addPromotion($connect, $data){

    $name = $data["name"];
    $description = $data["description"];

    mysqli_query($connect, "INSERT INTO `promo`(`id`, `name`, `description`) VALUES (NULL,'$name','$description')");
    
    http_response_code(201);
    
    $res=[
        "status"=> true,
        "promotion_id"=> mysqli_insert_id($connect)
    ];
    
    echo json_encode($res);

}


function getPromotions($connect){
    $promotions = mysqli_query($connect, "SELECT * FROM `promo`");

    $promotionList = [];

    while ($promotion = mysqli_fetch_assoc($promotions)) {
        $promotionList[] = $promotion;
    }

    echo json_encode($promotionList);
}



function getPromotion($connect, $id){
    $promotion = mysqli_query($connect, "SELECT p.id, p.name, p.description,
                                             pr.id AS prize_id, pr.description AS prize_description,
                                             pa.id AS participant_id, pa.name AS participant_name
                                      FROM `promo` p
                                      LEFT JOIN `prize` pr ON p.id = pr.promo_id
                                      LEFT JOIN `participant` pa ON p.id = pa.promo_id
                                      WHERE p.id='$id'");

    if(mysqli_num_rows($promotion) < 1)
    {
        http_response_code(404);
        $res=[
            "status"=> false,
            "msg"=> "Promotion not found"
        ];
        echo json_encode($res);
    }
    else{
        $data = array();
        while ($row = mysqli_fetch_assoc($promotion)) {
            $promotion_id = $row['id'];
            $promotion_name = $row['name'];
            $promotion_description = $row['description'];

            $prize_id = $row['prize_id'];
            $prize_description = $row['prize_description'];

            $participant_id = $row['participant_id'];
            $participant_name = $row['participant_name'];

            if (!isset($data['id'])) {
                $data['id'] = $promotion_id;
                $data['name'] = $promotion_name;
                $data['description'] = $promotion_description;
                $data['prizes'] = array();
                $data['participants'] = array();
            }

            if ($prize_id) {
                $data['prizes'][] = array(
                    'id' => $prize_id,
                    'description' => $prize_description
                );
            }

            if ($participant_id) {
                $data['participants'][] = array(
                    'id' => $participant_id,
                    'name' => $participant_name
                );
            }
        }
        echo json_encode($data);
    }
}

function updatePromo($connect, $id, $data){

    $name = $data['name'];
    $description = $data['description'];
    
    mysqli_query($connect,"UPDATE `promo` SET `name`='$name',`description`='$description' WHERE id = '$id'");
   
    http_response_code(200);

    $res=[
        "status"=> true,
        "msg"=> "promo is upd"
    ];

    echo json_encode($res);
}

function deletePromo($connect, $id){

    
    mysqli_query($connect,"DELETE FROM `promo` WHERE id = '$id'");
   
    http_response_code(200);

    $res=[
        "status"=> true,
        "msg"=> "promo is deleted"
    ];

    echo json_encode($res);
}

function addParticipant($connect, $promoId, $data){
    $name = $data['name'];
    mysqli_query($connect, "INSERT INTO `participant`(`id`, `name`, `promo_id`) VALUES (NULL,'$name','$promoId')");
    http_response_code(201);
    $res=[
        "status"=> true,
        "participant_id"=> mysqli_insert_id($connect)
    ];
    echo json_encode($res);
}

function deleteParticipant($connect, $promoId, $participantId){
    mysqli_query($connect,"DELETE FROM `participant` WHERE id = '$participantId' AND promo_id = '$promoId'");
    http_response_code(200);
    $res=[
        "status"=> true,
        "msg"=> "participant is deleted"
    ];
    echo json_encode($res);
}

function addPrize($connect, $promoId, $data){
    $description = $data['description'];
    mysqli_query($connect, "INSERT INTO `prize`(`id`, `description`, `promo_id`) VALUES (NULL,'$description','$promoId')");
    http_response_code(201);
    $res=[
        "status"=> true,
        "prize_id"=> mysqli_insert_id($connect)
    ];
    echo json_encode($res);
}

function deletePrize($connect, $promoId, $prizeId){
    mysqli_query($connect,"DELETE FROM `prize` WHERE id = '$prizeId' AND promo_id = '$promoId'");
    http_response_code(200);
    $res=[
        "status"=> true,
        "msg"=> "prize is deleted"
    ];
    echo json_encode($res);
}

function conductRaffle($connect, $promoId){
    $participants = mysqli_query($connect, "SELECT * FROM `participant` WHERE promo_id = '$promoId'");
    $prizes = mysqli_query($connect, "SELECT * FROM `prize` WHERE promo_id = '$promoId'");
    $participantCount = mysqli_num_rows($participants);
    $prizeCount = mysqli_num_rows($prizes);
    if($participantCount == $prizeCount && $participantCount > 0) {
        $winners = [];
        
        // Получаем всех участников
        $allParticipants = [];
        while ($participant = mysqli_fetch_assoc($participants)) {
            $allParticipants[] = $participant;
        }
        
        // Перемешиваем участников случайным образом
        shuffle($allParticipants);
        
        // Получаем все призы
        $allPrizes = [];
        while ($prize = mysqli_fetch_assoc($prizes)) {
            $allPrizes[] = $prize;
        }

        // Сопоставляем каждого перемешанного участника с призом
        foreach ($allParticipants as $index => $participant) {
            $winner = $participant;
            $prize = $allPrizes[$index % $prizeCount];
            
            // Записываем победителя в базу данных
            mysqli_query($connect, "INSERT INTO `result` (`winner`, `prize`) VALUES ('$winner[id]', '$prize[id]')");

            // Формируем результат розыгрыша для ответа
            $result = [
                "winner"=> $winner,
                "prize"=> $prize
            ];

            $winners[] = $result;
        }

        echo json_encode($winners);
    } else {
        http_response_code(409);
        $res = [
            "status"=> false,
            "msg"=> "Конфликт: количество участников и призов должно быть равным и больше 0."
        ];
        echo json_encode($res);
    }
}
