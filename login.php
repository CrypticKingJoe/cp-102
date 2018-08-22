<?php 
require 'db.php';
include ('db.php');
ob_start();
session_start();
include_once("include/securimage/securimage.php");
$sec = new Securimage();


if(empty($_SESSION["logged_in"]) || $_SESSION["logged_in"] == 0 ){

$variable = <<<Bdy

<!doctype html>
<html lang="en"><head>
    <meta charset="utf-8">
    <title>Access</title>
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
    
<form method="post" id="login" action="login.php">
        <div class="dialog">
    <div class="panel panel-default">
        <p class="panel-heading no-collapse">Sign In</p>
        <div class="panel-body">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="user" value="root" class="form-control span12">
                </div>
                <div class="form-group">
                <label>Password</label>
                    <input type="password" name="pass" class="form-controlspan12 form-control">
                </div>
				<div class="form-group">
                <label>Captcha</label>
                    <input type="text" name="captcha_code" class="form-control span12">
					<img id="captcha" src="include/securimage/securimage_show.php" alt="CAPTCHA Image" class="captcha"/>
                </div>
				<input type="submit" value="Submit" class="btn btn-primary pull-right"/>
                <label class="remember-me"><input type="checkbox" name="remember" value="1"> Remember (MD5 cookies)</label>
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
echo $variable;
}else{
    header("location: cp.php");
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') 
{	if(isset($_POST['captcha_code'])){

		$captcha_code = $_POST['captcha_code'];
		if ($sec->check($captcha_code) != false)
		{
			require 'auth.php';
		}else{
             echo '<center><p style="color:red;margin:auto;">ERROR: Invalid Captcha!</p></center>';
             $query = $mysqli->query("INSERT INTO logs (username, ipaddress, action, date) VALUES ('<i>Unknown</i>','".$_SERVER['REMOTE_ADDR']."','Invalid captcha code','".time()."');") or die("Error" .$mysqli->error);
		}
	}else{
             echo '<center><p style="color:red;margin:auto;">ERROR: Invalid Captcha!</p></center>';
             $query = $mysqli->query("INSERT INTO logs (username, ipaddress, action, date) VALUES ('<i>Unknown</i>','".$_SERVER['REMOTE_ADDR']."','Invalid captcha code','".time()."');") or die("Error" .$mysqli->error);
             
		}
    
}

?>