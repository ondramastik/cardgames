<?php

namespace App\Controllers;

use IPub;

class PrsiController extends IPub\WebSockets\Application\Controller\Controller {

    public function actionPublish($event, IPub\WebSocketsWAMP\Entities\Topics\ITopic $topic, IPub\WebSockets\Entities\Clients\IClient $client) {
        $topic->broadcast("someone played {$client->getId()}", [$client->getId()]);
    }

}