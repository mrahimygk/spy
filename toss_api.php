<?php
header('Access-Control-Allow-Origin: *'); 

if(isset($_GET['player_count'])){
    $player_count =  $_GET['player_count'];
    if($player_count>=4){
        $words_string = file_get_contents("words.json");
        $words_json = json_decode($words_string, true);
        $randomIndex = array_rand($words_json);
        $randomWord = $words_json[$randomIndex];
        $_SESSION['random_word'] = $randomWord;
        $_SESSION['player_count'] = $player_count;
        $indices = range(1, $player_count);
        shuffle($indices);
        $_SESSION['role_'.$indices[0]] = 'spy';
        unset($indices[0]);
        foreach ($indices as $index) {
            $_SESSION['role_'.$index] = $randomWord;
        }
        echo $_SESSION['role_1'];
    }
}

if(isset($_GET['toss_roles_index'])){
    $index = $_GET['toss_roles_index'];
    if(!isset($_SESSION['player_count'])) die();
    if(!isset($_SESSION['random_word'])) die();
    echo $_SESSION['role_'.$index];
}

//echo "<p>$randomWord</p>";

// echo $_SESSION['randomWord']." ".$_SESSION['player_count'];
// print_r($_SESSION);

?>
