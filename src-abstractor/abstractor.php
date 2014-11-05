<?php
function nlr2db ($nlr, $dbcon) {
	finduid:
	$ugid = explode("#",$nlr[0]); // Split first variable to find out if it's a group or direct message

	switch($ugid[0]) {
		case 'chat':
			//return 0; //// DBG
			$cid = $ugid[1];
			break;
		case 'user':
			$uid = $ugid[1];
			break;
		default:
			echo " ERR:MESG_IDENTIFIER_UNKNOWN ";
			return 0;
			break;
	}

	array_shift($nlr); // Remove parsed ID
	if(isset($cid) && !isset($uid)) goto finduid;

	array_shift($nlr); // Remove '>>>'

	$stmt = $dbcon->prepare("INSERT INTO `mesg` (`cid`, `uid`, `mesg`, `status`) VALUES (:cid, :uid, :mesg, :status)");
	@$stmt->bindValue(':cid', $cid, PDO::PARAM_INT);
	@$stmt->bindValue(':uid', $uid, PDO::PARAM_INT);
	$stmt->bindValue(':mesg', implode(" ",$nlr), PDO::PARAM_STR);
	$stmt->bindValue(':status', 0, PDO::PARAM_INT);

	return $stmt->execute();
}

require_once(dirname(__FILE__) . "/../config/dbconf.php");
$mysql = new PDO($dbDriver.':host='.$dbHost.';port='.$dbPort.';dbname='.$dbName, $dbUser, $dbPswd);

$cmd = "/opt/tg-bin/telegram-cli -k /opt/tg-bin/server.pub -I -R -C -P 3232";

$descriptorspec = array(
   0 => array("pipe", "r"),   // stdin is a pipe that the child will read from
   1 => array("pipe", "w"),   // stdout is a pipe that the child will write to
   2 => array("pipe", "w")    // stderr is a pipe that the child will write to
);

flush();

$process = proc_open($cmd, $descriptorspec, $pipes, realpath('./'), array());

if (is_resource($process)) {
    while ($s = fgets($pipes[1])) {
        if (stripos($s, ">>>") !== false) {
		$s = trim($s);
		$nlr = explode(" ",$s);

		array_shift($nlr); // Remove timestamp
		array_shift($nlr); // Remove empty space

		print_r($nlr);
		nlr2db($nlr, $mysql);
	}
        flush();
    }
}
?>
