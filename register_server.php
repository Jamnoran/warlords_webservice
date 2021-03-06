<?php
    $ip = htmlspecialchars($_POST["ip"]);
    $port = htmlspecialchars($_POST["port"]);
    $version = htmlspecialchars($_POST["version"]);

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

    unset($file);//prevent memory leaks for large json.

    $addToList = true;
    // Don't add if this ip + port is already in list
    foreach ($data['game'] as &$value) {
        if($ip == $value['ip'] && $port == $value['port']){
            $id = $value['id'];
            echo '{"message":"Game server already in list","code": 200, "id":"'.$id.'"}';
            $addToList = false;
        }
    }
    if($port == null || $version == null){
        $addToList = false;
    }
    if($addToList){
        //insert data here
        $id = uniqid();
        $new_server['id'] = $id;
        $new_server['ip'] = $ip;
        $new_server['port'] = $port;
        $new_server['version'] = $version;
        array_push( $data['game'], $new_server );

        //save the file
        file_put_contents('game_servers.json', json_encode($data, JSON_PRETTY_PRINT));
        unset($data);//release memory
        echo '{"message":"Game server successfully added to list of game servers","code": 200, "id":"'.$id.'"}';
    }
?>