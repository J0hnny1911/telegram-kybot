<?php
class sendto {

	public $name 	= 'ecko';
	public $version = '0.01';
	public $creator = 'kenny';
	public $desc	= 'Shits everything you say right back at you.';
	
	public function handle ($recv, $replyobj) {
		array_shift($recv);
		$recver = $recv[0];
		array_shift($recv);
		$recv = implode(" ", $recv);
		shell_exec('echo "msg '.$recver.' '. $recv .'" | nc -q 5 127.0.0.1 3232');
	}
	
}

$this->registerModule('sendto');
$this->registerCommand('sendto','sendto');
?>