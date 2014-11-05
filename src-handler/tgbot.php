<?php
class main {
	public $commands = array();
	public $modules = array();
	public $stringchecks = array();
	
	private $numModules = NULL;
	private $numCommands = NULL;
	
	public $lastSender = NULL;
	
	public $mysql;
	
	private $dbDriver;
	private $dbHost;
	private $dbPort;
	private $dbName;
	private $dbUser;
	private $dbPswd;
	
	public function __construct() {
		require_once(dirname(__FILE__) . "/../config/dbconf.php");
		$this->mysql = new PDO($dbDriver.':host='.$dbHost.';port='.$dbPort.';dbname='.$dbName, $dbUser, $dbPswd);
	}
	
	// allows modules to register themselves
	public function registerModule ($module) {
		$this->numModules++;
		$this->modules[$this->numModules] = $module;
	}
	
	// allows modules to register new commands
	public function registerCommand ($cmd, $handler) {
		$this->commands['#'.$cmd] = $handler;
	}
	
	// alllows modules to receive the entire string should no registered command be able to use it
	public function registerStringCheck ($string, $handler) {
		$this->stringchecks[$string] = $handler;
	}

	// handle received commands
	public function handle ($recv) {
			$handler = @$this->commands[$recv[0]]; // find out handler for command
			print_r($recv);
			if(!empty($handler)) {
				$hdl = new $handler;
				echo "delegate";
				$hdl->handle($recv,$this); // delegate handling to command
			} else {
				$handled = false;
				foreach($this->stringchecks as $string => $handler) {
					if(!empty($handler) && preg_match("/$string/i", implode(" ",$recv))) {
						$handled = true;
						$hdl = new $handler;
						$hdl->handleSC($recv,$this);
					}
				}
				//if (!$handled) $this->reply('txt', "Sorry, I don't know this command.");
			}
			unset($hdl);
			echo "handle";
		//	die();
		//}
	}
	
	public function loadModules () {
		foreach (glob("modules/*.php") as $filename) {
			include $filename;
		}
	}
	
	public function getCmd () {
		reget:
		$stmt = $this->mysql->prepare("SELECT * FROM mesg WHERE status = '0' LIMIT 1;");
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		print_r($result);
		
		if(!isset($result['mesg'])) {
			sleep(1);
			goto reget;
		}
		
		$stmt = $this->mysql->prepare("UPDATE `mesg` SET `status` = :status WHERE (`id` = :id)");
		$stmt->bindValue(':status', 1, PDO::PARAM_INT);
		$stmt->bindValue(':id', $result['id'], PDO::PARAM_INT);
		$stmt->execute();
		
		if(isset($result['cid'])) {
			$this->lastSender = 'chat#'. $result['cid'];
		} else {
			$this->lastSender = 'user#'. $result['uid'];
		}
		
		echo "get";
		
		return explode(" ",$result['mesg']);
	}
	
	// send replies to sender
	public function reply ($type, $mesg) {
		switch ($type) {
			case 'imgstatic':  // Static image
				//shell_exec('expects/tgsendimg.xp '.$this->lastSender.' '.$image);
				break;
				
			case 'imgget': // Image from URL
				//send "send_photo $user $file\r"
				//shell_exec('wget --quiet -O /tmp/img.jpg '.$mesg);
				//shell_exec('expects/tgsendimg.xp '.$this->lastSender.' /tmp/img.jpg');
				//shell_exec('rm /tmp/img.jpg');
				$tempfile = "/tmp/mesg". rand(1,100000) .".jpg";
				shell_exec('wget --quiet -O '.$tempfile.' '.$mesg);
				shell_exec('echo "send_photo '.$this->lastSender.' '. $tempfile .'" | nc -q 5 127.0.0.1 3232');
				unlink($tempfile);
				echo "out";
				break;
				
			case 'txt': // Standard message
				//$mesg = preg_replace( "/\r|\n/", " ", $mesg);
				//shell_exec('expects/tgsendmsg.xp '.$this->lastSender.' "'.$mesg.'"');
				shell_exec('echo "msg '.$this->lastSender.'  '. $mesg .'" | nc -q 5 127.0.0.1 3232');
				echo "out";
				break;
				
			case 'mltxt': // Standard message
				$tempfile = "/tmp/mesg". rand(1,100000) .".txt";
				//$mesg = preg_replace( "/\r|\n/", " ", $mesg);
				file_put_contents($tempfile, " ".$mesg);
				//shell_exec('expects/tgsendmsg_ml.xp '.$this->lastSender.' /tmp/mesg.txt');
				//unlink("/tmp/mesg.txt");
				shell_exec('echo "send_text '.$this->lastSender.' '. $tempfile .'" | nc -q 5 127.0.0.1 3232');
				unlink($tempfile);
				echo "out";
				break;
				
			case 'pfpic': // Image from URL
				$tempfile = "/tmp/mesg". rand(1,100000) .".jpg";
				shell_exec('wget --quiet -O '.$tempfile.' '.$mesg);
				shell_exec('echo "set_profile_photo '. $tempfile .'" | nc -q 5 127.0.0.1 3232');
				unlink($tempfile);
				echo "out";
				break;
				
			default:
				echo "Illegal.";
				break;
		}
	}
	
	// MySQL connection
	public function exeQ ($dbcon, $query, $qMap=NULL) {
			$qt = $dbcon->prepare($query);
			$qt->execute($qMap);
			$retval = $qt->fetchAll(PDO::FETCH_ASSOC);
			return $retval;
	}
	
}

$bot = new main();
$bot->loadModules();

//print_r($bot->commands);
//print_r($bot->modules);

// Main loop
while (1) {
	$recv = $bot->getCmd();
	$bot->handle($recv);
}
?>
