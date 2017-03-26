<?php
    $ip = htmlspecialchars($_POST["ip"]);
    $port = htmlspecialchars($_POST["port"]);
    $id = htmlspecialchars($_POST["id"]);

    if(!isset($_POST['ip'])){
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
    }
    $file = file_get_contents("game_servers.json");
    $data = json_decode($file, true);

    $positionOfServerToRemove = -1;

    unset($file);//prevent memory leaks for large json.

    $addToList = true;
    // Remove if found server else some other lobby might have removed it
    $i = 0;
    foreach ($data['game'] as &$value) {
        if($ip == $value['ip'] && $port == $value['port'] && $id == $value['id']){
           $positionOfServerToRemove = $i;
        }
        $i++;
    }
    if($positionOfServerToRemove >= 0){
        unset($data['game'][$positionOfServerToRemove]); // remove item at index 0
        $newListOfServers = array_values($data['game']); // 'reindex' array
        $data['game'] = $newListOfServers;

        //save the file
        file_put_contents('game_servers.json', json_encode($data, JSON_PRETTY_PRINT));
        unset($data);//release memory
        unset($newListOfServers);//release memory
        echo '{"message":"Server successfully removed from list of game servers","code": 200"}';
    }
?>