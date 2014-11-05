<?php
class puush2tg {

	public $name 	= 'dogrio';
	public $version = '0.01';
	public $creator = 'kenny';
	public $desc	= 'Answer is cat.';
	
	public function handleSC ($recv, $replyobj) {
		//array_shift($recv);
		$mesg = implode(' ',$recv);
		
		$replyobj->reply('imgget',$mesg);
		$replyobj->reply('txt','Uploading puu.sh link to Telegram ...');
	}
	
}

$this->registerStringCheck('http:\/\/puu.sh','puush2tg');
?>
