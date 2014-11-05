<?php
class imget {

	public $name 	= 'dogrio';
	public $version = '0.01';
	public $creator = 'kenny';
	public $desc	= 'Answer is cat.';
	
	public function handle ($recv, $replyobj) {
		array_shift($recv);
		$mesg = implode(' ',$recv);
		
		$replyobj->reply('imgget',$mesg);
	}
	
}

$this->registerModule('imget');
$this->registerCommand('imget','imget');
?>
