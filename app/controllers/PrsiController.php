<?php

namespace App\Controllers;

use IPub;

class PrsiController extends IPub\WebSockets\Application\Controller\Controller {
	
	public function actionSubscribe(IPub\WebSockets\Entities\Clients\IClient $client, IPub\WebSocketsWAMP\Entities\Topics\ITopic $topic, string $gameId) {
		//$topic->broadcast($client->getId() .' joined: '. $gameId, [$client->getId()]);
	}
	
	public function actionUnsubscribe(IPub\WebSockets\Entities\Clients\IClient $client, IPub\WebSocketsWAMP\Entities\Topics\ITopic $topic, string $gameId) {
		//$topic->broadcast($client->getId() .' left: '. $gameId, [$client->getId()]);
	}
	
	public function actionPublish($event, IPub\WebSocketsWAMP\Entities\Topics\ITopic $topic, IPub\WebSockets\Entities\Clients\IClient $client) {
		$topic->broadcast("someone played {$client->getId()}", [$client->getId()]);
	}
	
}