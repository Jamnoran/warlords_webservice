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
    $file = file_get_contents("lobbys.json");
    $data = json_decode($file, true);

    $positionOfLobbyToRemove = -1;

    unset($file);//prevent memory leaks for large json.

    $addToList = true;
    // Remove if found server else some other lobby might have removed it
    $i = 0;
    foreach ($data['lobby'] as &$value) {
        if($ip == $value['ip'] && $port == $value['port'] && $id == $value['id']){
           $positionOfLobbyToRemove = $i;
        }
        $i++;
    }
    if($positionOfLobbyToRemove >= 0){
        unset($data['lobby'][$positionOfLobbyToRemove]); // remove item at index 0
        $newListOfLobbys = array_values($data['lobby']); // 'reindex' array
        $data['lobby'] = $newListOfLobbys;

        //save the file
        file_put_contents('lobbys.json', json_encode($data, JSON_PRETTY_PRINT));
        unset($data);//release memory
        unset($newListOfLobbys);//release memory
        echo '{"message":"Lobby successfully removed from list of lobby\'s","code": 200"}';
    }
?>