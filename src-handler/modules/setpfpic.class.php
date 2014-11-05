<?php
class setpfpic {

	public $name 	= 'dogrio';
	public $version = '0.01';
	public $creator = 'kenny';
	public $desc	= 'Answer is cat.';
	
	public function handle ($recv, $replyobj) {
		array_shift($recv);
		$mesg = implode(' ',$recv);
		
		$replyobj->reply('txt','OK!');
		$replyobj->reply('pfpic',$mesg);
	}
	
}

$this->registerModule('setpfpic');
$this->registerCommand('setpfpic','setpfpic');
?>
