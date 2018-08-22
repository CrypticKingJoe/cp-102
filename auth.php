<?php
require 'db.php';
include ('db.php');

$user1 = $mysqli->escape_string($_POST['user']);
$result = $mysqli->query("SELECT * FROM accounts WHERE user='$user1'");
if ( $result->num_rows == 0 ){
echo '<center><p style="color:red;margin:auto;">ERROR: Invalid credentials!</p></center>';
$query = $mysqli->query("INSERT INTO logs (username, ipaddress, action, date) VALUES ('<i>Unknown</i>','".$_SERVER['REMOTE_ADDR']."',Login failed','".time()."');") or die("Error" .$mysqli->error);

}
else {
    $user = $result->fetch_assoc();
    if ( password_verify($_POST['pass'], $user['password']) ) {
        if($_POST["remember"]=='1' || $_POST["remember"]=='on')
        {
            $expire = 365*24*3600;
            ini_set('session.gc_maxlifetime', $expire);
            session_start();
            setcookie(session_name(),session_id(),time()+$expire);

            $hour = time() + 3600 * 24 * 30;
            setcookie('user', $login, $hour);
            setcookie('pass', $password, $hour);
            $_SESSION['logged_in'] = true;
            $_SESSION['active'] = $user['active'];
            $_SESSION['user'] = $user['user'];
            $_SESSION['role'] = $user['role'];
        }else{
            $_SESSION['active'] = $user['active'];
            $_SESSION['user'] = $user['user'];
            $_SESSION['role'] = $user['role'];
        }

        $lastact =  date('Y-m-d H:i:s');
        $buffer_user = $user['user'];
        $query = $mysqli->query("UPDATE accounts SET lastactivity='$lastact' WHERE user='$buffer_user'") or die("Error" .$mysqli->error);
        $query = $mysqli->query("UPDATE accounts SET active='1' WHERE user='$buffer_user'") or die("Error" .$mysqli->error);
        
        header("location: cp.php");
        $query = $mysqli->query("INSERT INTO logs (username, ipaddress, action, date) VALUES ('$buffer_user','".$_SERVER['REMOTE_ADDR']."','Logged in','".time()."');") or die("Error" .$mysqli->error);
        
    }
    else {
        echo '<center><p style="color:red;margin:auto;">ERROR: Invalid credentials!</p></center>';
        $query = $mysqli->query("INSERT INTO logs (username, ipaddress, action, date) VALUES ('<i>Unknown</i>','".$_SERVER['REMOTE_ADDR']."','Login failed','".time()."');") or die("Error" .$mysqli->error);
        
    }
}

