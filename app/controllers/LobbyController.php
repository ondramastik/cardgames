<?php

namespace App\Controllers;

use App\Models\Lobby\LobbyAction;
use IPub;
use Nette\Utils\Json;

class LobbyController extends IPub\WebSockets\Application\Controller\Controller {
	
	/**
	 * @param $event
	 * @param IPub\WebSocketsWAMP\Entities\Topics\ITopic $topic
	 * @param IPub\WebSockets\Entities\Clients\IClient $client
	 * @throws \Nette\Utils\JsonException
	 */
	public function actionPublish(array $event, IPub\WebSocketsWAMP\Entities\Topics\ITopic $topic, IPub\WebSockets\Entities\Clients\IClient $client) {
		if($event['action'] === LobbyAction::START) {
			$message = new LobbyAction($event['action'], $event['gameType']);
		} else {
			$message = new LobbyAction($event['action']);
		}
		
		/** @var IPub\WebSocketsWAMP\Entities\Clients\IClient $otherClient */
		foreach ($topic as $otherClient) {
			if ($otherClient->getId() !== $client->getId()) {
				$otherClient->send(Json::encode([IPub\WebSocketsWAMP\Application\Application::MSG_EVENT, $topic->getId(), $message->create()]));
			}
		}
	}
	
}