<?php


namespace App\Models\Lobby;

use IPub\WebSocketsZMQ\Pusher\Pusher;

class LobbyPusher {

	/** @var Pusher */
	private $pusher;

	/**
	 * LobbyPusher constructor.
	 * @param Pusher $pusher
	 */
	public function __construct(Pusher $pusher) {
		$this->pusher = $pusher;
	}

	public function pushMemberHasLeft(Lobby $lobby) {
		$this->push($lobby, LobbyAction::LEAVE);
	}

	public function pushMemberHasJoined(Lobby $lobby) {
		$this->push($lobby, LobbyAction::JOIN);
	}

	public function pushLobbyCancelled(Lobby $lobby) {
		$this->push($lobby, LobbyAction::CANCEL);
	}

	public function pushGameStarted(Lobby $lobby, int $gameType) {
		$this->pusher->push(["action" => LobbyAction::START, "gameType" => $gameType],
			"Lobby:", ["lobbyId" => strval($lobby->getId())]);
	}

	private function push(Lobby $lobby, string $lobbyAction) {
		$this->pusher->push(["action" => $lobbyAction], "Lobby:", ["lobbyId" => strval($lobby->getId())]);
	}

}
