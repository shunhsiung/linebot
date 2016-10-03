<?php
namespace app\MyBot;

class User 
{
	public $Id;
	public $displayName;
	public $pictureUrl;
	public $statusMessage;

	function __construct($response) {
		if ($response->isSucceeded()) {
			$profile = $response->getJSONDecodedBody();
			$this->Id = $profile['userId'];
			$this->displayName = $profile['displayName'];
			$this->pictureUrl = $profile['pictureUrl'];
			$this->statusMessage = $profile['statusMessage'];
		} 		
	}

	function pushMessage($bot, $logger, $message) {

		$textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($message);	

		$response = $bot->pushMessage($this->Id,$textMessageBuilder);

		$logger->info(sprintf("%s: %s %s => %s",$response->getHTTPStatus(),$response->getRawBody(),$message,$this->Id));
	}
}
