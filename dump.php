<?php
require 'db.php';
include ('db.php');

$expire = 365*24*3600;
ini_set('session.gc_maxlifetime', $expire);
session_start();

// Check if user is logged in using the session variable
if(empty($_SESSION["logged_in"])){
    if(empty($_SESSION["user"])){
        header("location: error.php");
    }else{
        setcookie(session_name(),session_id(),0);
        // Makes it easier to read
        $user = $_SESSION['user'];
        $active = $_SESSION['active'];
        }
}
else
{
    setcookie(session_name(),session_id(),time()+$expire);
    // Makes it easier to read
    $user = $_SESSION['user'];
    $active = $_SESSION['active'];
}
if($_GET && isset($_GET['all'])){
    if (!file_exists('dump')) {
        mkdir('dump', 0777, true);
    }

    $result = $mysqli->query("SELECT * FROM bots");
    $filepath = "dump/CP102_DBDUMP.csv";

    $out = fopen($filepath, 'w');
    while ($row = $result->fetch_row()) {
            fputcsv($out,$row);
    }
    fclose($out);

    header("location:list.php?csv=All");    
}

if($_GET && isset($_GET['id'])){
        if (!file_exists('dump')) {
            mkdir('dump', 0777, true);
        }

        $botid = $_GET['id'];
        $result = $mysqli->query("SELECT * FROM bots WHERE bot_id='$botid'");
        $filepath = "dump/".$botid.".csv";

        $out = fopen($filepath, 'w');
        while ($row = $result->fetch_row()) {
                fputcsv($out,$row);
        }
        fclose($out);

        header("location:details.php?csv=$botid");    
}




?>