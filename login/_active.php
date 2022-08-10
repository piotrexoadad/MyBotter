<?php

$query = "SELECT * FROM users WHERE id={$_SESSION['SESS_MEMBER_ID']}";
$result = mysql_query($query);
$row = mysql_fetch_array($result, MYSQL_ASSOC);
$months = $row['months'];
$active = $row['active'];
$userip = $row['userip'];
$time = time();

if ($months !== lifetime) {
	if (($active == 'activated') AND ($months < $time)) {
		$active = 'Your login is deactivated.<br />REASON: Payment is overdue. Please pay your bill.';
		$query = "UPDATE users SET active='$active' WHERE id={$_SESSION['SESS_MEMBER_ID']}";
		$result = mysql_query($query);
	}
}

if (($userip != $_SERVER['REMOTE_ADDR']) AND ($active == 'activated')){
	$active = 'Your login is deactivated.<br />REASON: Suspicious account activity. Contact the administrator.';
	$query = "UPDATE users SET active='$active' WHERE id={$_SESSION['SESS_MEMBER_ID']}";
	$result = mysql_query($query);
}
	
if ($active !== 'activated'){
	echo $active;
	exit();
}

?>