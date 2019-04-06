<?php

namespace App\Controllers;

use App\Models\Lobby\LobbyAction;
use IPub;
use Nette\Utils\Json;

class LobbyController extends IPub\WebSockets\Application\Controller\Controller {

	/**
	 * @param array $data
	 * @param IPub\WebSocketsWAMP\Entities\Topics\ITopic $topic
	 * @throws \Nette\Utils\JsonException
	 */
	public function actionPush(array $data, IPub\WebSocketsWAMP\Entities\Topics\ITopic $topic) {
		if ($data['action'] === LobbyAction::START) {
			$message = new LobbyAction($data['action'], $data['gameType']);
		} else {
			$message = new LobbyAction($data['action']);
		}

		/** @var IPub\WebSocketsWAMP\Entities\Clients\IClient $client */
		foreach ($topic as $client) {
			$client->send(Json::encode([IPub\WebSocketsWAMP\Application\Application::MSG_EVENT, $topic->getId(), $message->create()]));
		}
	}

}