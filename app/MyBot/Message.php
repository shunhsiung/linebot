<?php
namespace app\MyBot;

class Message 
{
	private $message; 
	private $user;
	function __construct ( $msg , $user) {
		$this->message = $msg;
		$this->user = $user;
	}

	public function get_reply_message() {
		$msg = "";
		if (preg_match("/@time|@now/",$this->message)) {
			$msg = date('Y-m-d H:i:s');
		} elseif (preg_match("/@today/",$this->message)) {
			$fday = date('m月d日');
			$msg = sprintf("https://zh.wikipedia.org/zh-tw/%s",urlencode($fday));
		} elseif (preg_match("/^@image$/",$this->message)) {

			$msg = $this->gen_image_message($ourl,$purl);	

		} elseif (preg_match("/@location/",$this->message)) {
			$msg = $this->gen_location_message ($title, $address, $lat , $lng );
		} elseif (preg_match("/^@yes$/",$this->message)) {
			
			$actions = array();
			array_push($actions , array('我是','Yes'));
			array_push($actions , array('我不是','No'));

			$msg = $this->gen_confirm_message ('Confirm Message','你是工程師嘛？',$actions);

		} elseif (preg_match("/^@buttons$/",$this->message)) {

			$actions = array();
			array_push($actions , array('postback','剪刀','剪刀'));
			array_push($actions , array('postback','石頭','石頭'));
			array_push($actions , array('postback','布','布'));
//			array_push($actions , array('uri','button4','http://www.google.com'));
			$msg = $this->gen_buttons_message ('Button Message','https://bot.ssh.tw/imgs/14.jpg','遊戲','請猜拳',$actions);
		} else {
			$msg = sprintf("Hello %s\n%s",$this->user->displayName,$this->message);
		}

		return $msg;
	}

	function gen_image_message ( $originalContentUrl , $previewImageUrl )
	{
		return new \LINE\LINEBot\MessageBuilder\ImageMessageBuilder($originalContentUrl,$previewImageUrl);	
	}

	function gen_location_message ($title , $address, $lat , $lng ) 
	{
		return new \LINE\LINEBot\MessageBuilder\LocationMessageBuilder($title,$address,$lat,$lng);
	}

	function gen_confirm_message ($altText , $text , array $act_a) 
	{
		// Only Support Line iOS/Android version 6.7.0 
		$actions = array();
		foreach ($act_a as $act ) {
			array_push($actions , new \LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder($act[0],$act[1]));
		}

		$ctb = new \LINE\LINEBot\MessageBuilder\TemplateBuilder\ConfirmTemplateBuilder($text,$actions);

		return new \LINE\LINEBot\MessageBuilder\TemplateMessageBuilder($altText,$ctb);	
	}

	function gen_buttons_message($altText , $imageurl , $title , $text , array $act_a )
	{
		$actions = array();
		foreach ($act_a as $act ) {
			if (preg_match("/postback/",$act[0])) {
				array_push($actions , new \LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder($act[1],$act[2]));	
			} elseif (preg_match("/uri/",$act[0])) {
				array_push($actions , new \LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder($act[1],$act[2]));	
			}
		}

		$btb = new \LINE\LINEBot\MessageBuilder\TemplateBuilder\ButtonTemplateBuilder($title,$text,$imageurl,$actions);

		return new \LINE\LINEBot\MessageBuilder\TemplateMessageBuilder($altText,$btb);	
		
	}
}
