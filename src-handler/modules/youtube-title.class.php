<?php
class youtubetitle {

	public $name 	= 'dogrio';
	public $version = '0.01';
	public $creator = 'kenny';
	public $desc	= 'Answer is cat.';
	
	public function handleSC ($recv, $replyobj) {
		//array_shift($recv);
		$mesg = implode(' ',$recv);
		
		preg_match("#(?<=v=)[a-zA-Z0-9-]+(?=&)|(?<=v\/)[^&\n]+(?=\?)|(?<=v=)[^&\n]+|(?<=youtu.be/)[^&\n]+#", $mesg, $thisMatch);
		$ytid = $thisMatch[0];
		
		$url = "http://gdata.youtube.com/feeds/api/videos/". $ytid;
		$youtube = simplexml_load_file('http://gdata.youtube.com/feeds/api/videos/'.$ytid.'?v=2');
		$title = $youtube->title;
		
		if(!empty($title)) {
			$replyobj->reply('imgget','https://i.ytimg.com/vi/'.$ytid.'/0.jpg');
			sleep(1);
			$replyobj->reply('txt','⚠️ [YouTube] '.$title);
		}
	}
	
}

$this->registerStringCheck('http:\/\/www.youtube.com','youtubetitle');
$this->registerStringCheck('https:\/\/www.youtube.com','youtubetitle');
$this->registerStringCheck('http:\/\/youtube.com','youtubetitle');
$this->registerStringCheck('https:\/\/youtube.com','youtubetitle');
$this->registerStringCheck('http:\/\/youtu.be','youtubetitle');
$this->registerStringCheck('https:\/\/youtu.be','youtubetitle');
?>
