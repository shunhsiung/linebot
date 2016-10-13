<?php

/**
 * Copyright 2016 LINE Corporation
 *
 * LINE Corporation licenses this file to you under the Apache License,
 * version 2.0 (the "License"); you may not use this file except in compliance
 * with the License. You may obtain a copy of the License at:
 *
 *   https://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations
 * under the License.
 */

namespace app\MyBot;

use app\MyBot\User;
use app\MyBot\Message;
use plugins\Oursweb\Oursweb;
use plugins\YouBike\GeoYouBike;
use LINE\LINEBot;
use LINE\LINEBot\Constant\HTTPHeader;
use LINE\LINEBot\Event\MessageEvent;
use LINE\LINEBot\Event\MessageEvent\TextMessage;
use LINE\LINEBot\Event\MessageEvent\LocationMessage;
use LINE\LINEBot\Event\PostbackEvent;
use LINE\LINEBot\Exception\InvalidEventRequestException;
use LINE\LINEBot\Exception\InvalidSignatureException;
use LINE\LINEBot\Exception\UnknownEventTypeException;
use LINE\LINEBot\Exception\UnknownMessageTypeException;

class Route
{
    public function register(\Slim\App $app)
    {

        $app->post('/callback', function (\Slim\Http\Request $req, \Slim\Http\Response $res) {
            /** @var \LINE\LINEBot $bot */
            $bot = $this->bot;
            /** @var \Monolog\Logger $logger */
            $logger = $this->logger;

            $signature = $req->getHeader(HTTPHeader::LINE_SIGNATURE);
            if (empty($signature)) {
                return $res->withStatus(400, 'Bad Request');
            }

            // Check request with signature and parse request
            try {
                $events = $bot->parseEventRequest($req->getBody(), $signature[0]);
            } catch (InvalidSignatureException $e) {
                return $res->withStatus(400, 'Invalid signature');
            } catch (UnknownEventTypeException $e) {
                return $res->withStatus(400, 'Unknown event type has come');
            } catch (UnknownMessageTypeException $e) {
                return $res->withStatus(400, 'Unknown message type has come');
            } catch (InvalidEventRequestException $e) {
                return $res->withStatus(400, "Invalid event request");
            }

            foreach ($events as $event) {
				if ($event instanceof MessageEvent) {
					if ($event instanceof TextMessage) {
						$client_user = new User($bot->getProfile($event->getUserId()));

						$client_message = new Message($event->getText(), $client_user);

						$replyText = $client_message->get_reply_message();
						if (is_object($replyText)) {
							$resp = $bot->replyMessage($event->getReplyToken(), $replyText);
							$logger->info('Object : ' . serialize($replyText));
						} else {
							$logger->info('Reply text: ' . $replyText);
							$resp = $bot->replyText($event->getReplyToken(), $replyText);
						}
						$logger->info($resp->getHTTPStatus() . ': ' . $resp->getRawBody());


					} elseif ($event instanceof LocationMessage) {
						$lat = $event->getLatitude();
						$lng = $event->getLongitude();
						$replyToken = $event->getReplyToken();
						
						$rootDir = dirname(dirname(__DIR__));
						$fp = sprintf("%s/plugins/YouBike/search_youbike.php",$rootDir);
						$cmd = sprintf("php %s %s %s %d",$fp,$lng,$lat,700);
						exec($cmd,$output,$return_var);
						$msg = "";
						foreach ($output as $o_a) {
							$msg .= sprintf("%s\n",$o_a);
						}
/*
						$geo = new GeoYouBike();
						$msg = $geo->get_lnglat($lng,$lat);	
*/
						$resp = $bot->replyText($replyToken, sprintf("目前附近YouBike點有\n%s",$msg));
//                        $oursweb = new Oursweb();
//                        $church_list_str = $oursweb->get_church_list($lat,$lng);
//						$resp = $bot->replyText($replyToken, sprintf("目前附近教會有\n%s",$church_list_str));
							
					} else {
						$logger->info('Non text message has com');
						continue;
					}
				} elseif ($event instanceof PostbackEvent) {
						$data = $event->getPostbackData();	
						$userId = $event->getUserId();	
						$replyToken = $event->getReplyToken();
						$resp = $bot->replyText($replyToken, sprintf("你選擇答案是 %s",$data));
				} else {
					$logger->info(serialize($event));
					$logger->info('Unknown event type has come');
					continue;
				}

            }

            $res->write('OK');
            return $res;
        });
    }
}
