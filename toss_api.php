<?php
header('Access-Control-Allow-Origin: *'); 

$servername = "localhost";
$username = "cafesqir_root";
$password = "iHNqXYexdFBg";
$db = "cafesqir_spy";

$conn = new mysqli($servername, $username, $password, $db);
$conn-> set_charset("utf8");

if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
}

if(isset($_GET['player_count'])){
    $player_count =  $_GET['player_count'];
    if($player_count>=4){
        $words_string = file_get_contents("words.json");
        $words_json = json_decode($words_string, true);
        $randomIndex = array_rand($words_json);
        $randomWord = $words_json[$randomIndex];
        $gameCode = gen_uid($l=10);
        while(isGameCodeTaken($gameCode, $conn)){
            $gameCode = gen_uid($l=10);
        }
        $sql = "INSERT INTO game (code, nightname, player_count) VALUES ('$gameCode','$randomWord','$player_count');";
	    runQuery($sql, $conn);
        $indices = range(1, $player_count);
        shuffle($indices);
        $sql = "INSERT INTO player (game, position, is_taken, is_spy) VALUES ('$gameCode',$indices[0],false, true);";
        runQuery($sql , $conn);
        unset($indices[0]);
        foreach($indices as $index){
            $sql = "INSERT INTO player (game, position, is_taken, is_spy) VALUES ('$gameCode',$index,false, false);";
            runQuery($sql , $conn);
        }
        
        takeRole($gameCode, 1, $conn);
        showRole($gameCode, 1, $conn);
        echo "||| $gameCode";
        
    }
}

if(isset($_GET['join_game'])){
    $gameCode = $_GET['join_game'];
    $position = getFreePositionOfGame($gameCode, $conn);
    if($position == false) {
        echo 'بازی پر شده';
        return;
    }
    takeRole($gameCode, $position, $conn);
    showRole($gameCode, $position, $conn);
    $playerCount = getPlayerCount($gameCode, $conn);
    echo "||| $playerCount";
}

function gen_uid($l=10){
    return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyz"), 0, $l);
}

function isGameCodeTaken($code, $conn){
    if($result = $conn->query("SELECT code FROM game where code=`$code` LIMIT 1"))
	        return $result->num_rows >0;
}

function getFreePositionOfGame($gameCode, $conn){
    if($result = $conn->query("SELECT player.position FROM player WHERE player.game='$gameCode' AND player.is_taken=false LIMIT 1"))
        if($result->num_rows >0){
            $row = $result->fetch_assoc();
            return $row["position"];
        } else return false;
        
    return false;
}

function runQuery($sql , $conn){
    if ($conn->query($sql) === TRUE) {
// 		echo "New record created successfully";
	} else {
	   // echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

function showRole($gameCode, $position, $conn){
    if($result = $conn->query("SELECT player.is_spy, game.nightname FROM game JOIN player ON player.game=game.code WHERE player.game='$gameCode' AND player.position=$position LIMIT 1"))
        if($result->num_rows >0){
            $row = $result->fetch_assoc();
            if($row["is_spy"]) echo 'spy';
            else echo $row["nightname"];
        }
}

function takeRole($gameCode, $position, $conn){
    $sql = "UPDATE player SET player.is_taken=true WHERE player.game='$gameCode' AND player.position=$position";
    runQuery($sql, $conn);
}

function getPlayerCount($gameCode, $conn){
    if($result = $conn->query("SELECT game.player_count FROM game WHERE game.code='$gameCode' LIMIT 1"))
        if($result->num_rows >0){
            $row = $result->fetch_assoc();
            return $row["player_count"];
        }
        
    return false;
}













?>
