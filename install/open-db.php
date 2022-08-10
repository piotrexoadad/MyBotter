<?
error_reporting(0);
// Make a MySQL Connection
$cid = @mysql_connect($host,$dbuser,$dbpwd);
if (!$cid) { 
	print "ERROR: " . mysql_error() . "\n ";   
	$workingCon = false;
}else{
	$cidCon = @mysql_select_db("$dbname");
	if (!$cidCon) {
		print "ERROR SELECTING DATABASE";
		$workingCon = false;
	}else{
		$workingCon = true;
	}
}

?>