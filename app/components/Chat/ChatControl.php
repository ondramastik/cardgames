<?php

use Nette\Application\UI\Control;

class ChatControl extends Control {
	
	/** @var string */
	private $lobbyId;
	
	/**
	 * ChatControl constructor.
	 * @param string $lobbyId
	 */
	public function __construct(string $lobbyId) {
		parent::__construct();
		$this->lobbyId = $lobbyId;
	}
	
	
	public function render() {
		$this->getTemplate()->setFile(__DIR__ . '/../../templates/Chat/chat.latte');
		
		$this->getTemplate()->lobbyId = $this->lobbyId;
		
		$this->getTemplate()->render();
	}
	
}