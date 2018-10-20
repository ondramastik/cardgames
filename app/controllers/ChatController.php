<?php

namespace App\Controllers;

use IPub;
use Nette\Utils\Json;

class ChatController extends IPub\WebSockets\Application\Controller\Controller {

    /**
     * @param $event
     * @param IPub\WebSocketsWAMP\Entities\Topics\ITopic $topic
     * @param IPub\WebSockets\Entities\Clients\IClient $client
     * @throws \Nette\Utils\JsonException
     */
    public function actionPublish(array $event, IPub\WebSocketsWAMP\Entities\Topics\ITopic $topic, IPub\WebSockets\Entities\Clients\IClient $client) {
        $message = new \App\Models\Chat\Message($event['sender'], new \DateTime(), $event['text']);

        /** @var IPub\WebSocketsWAMP\Entities\Clients\IClient $otherClient */
        foreach ($topic as $otherClient) {
            if ($otherClient->getId() === $client->getId()) {
                continue;
            }

            $otherClient->send(Json::encode([IPub\WebSocketsWAMP\Application\Application::MSG_EVENT, $topic->getId(), $message->create()]));
        }

        $message->setOtherSender(false);

        $client->send(Json::encode([IPub\WebSocketsWAMP\Application\Application::MSG_EVENT, $topic->getId(), $message->create()]));
    }

}
