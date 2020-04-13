<?php
header('Access-Control-Allow-Origin: *'); 

if(isset($_GET['player_count'])){
    $player_count =  $_GET['player_count'];
    if($player_count>=4){
        $words_string = file_get_contents("words.json");
        $words_json = json_decode($words_string, true);
        $randomIndex = array_rand($words_json);
        $randomWord = $words_json[$randomIndex];
        $_SESSION['randomWord'] = $randomWord;
        $_SESSION['player_count'] = $player_count;
    }
}


//echo "<p>$randomWord</p>";

echo $_SESSION['randomWord']." ".$_SESSION['player_count'];

?>
