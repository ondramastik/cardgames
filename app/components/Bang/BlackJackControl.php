<?php

namespace App\Components\Bang;


use App\Models\Bang\Card;
use App\Models\Bang\GameGovernance;
use App\Models\Bang\Handlers\BlackJack;
use App\Models\Bang\Handlers\SidKetchum;
use Nette\Application\UI\Control;

class BlackJackControl extends Control {
	
	/** @var GameGovernance */
	private $gameGovernance;
	
	/** @var BlackJack */
	private $handler;
	
	/**
	 * SidKetchumControl constructor.
	 * @param GameGovernance $gameGovernance
	 */
	public function __construct(GameGovernance $gameGovernance) {
		parent::__construct();
		$this->gameGovernance = $gameGovernance;
		$this->handler = $this->gameGovernance->getGame()->getHandler();
	}
	
	public function render() {
		$this->getTemplate()->setFile(__DIR__ . '/../../templates/Bang/handlers/blackjack.latte');
		
		$this->getTemplate()->secondCard = $this->handler->getSecondCard();
		$this->getTemplate()->actingPlayer = $this->gameGovernance->getActingPlayer();
		$this->getTemplate()->game = $this->gameGovernance->getGame();
		
		$this->getTemplate()->render();
	}
	
	public function handleConfirm() {
		if($this->gameGovernance->getActingPlayer()->getNickname()
			=== $this->gameGovernance->getGame()->getActivePlayer()->getNickname()) {
			$this->handler->confirmSecondCard($this->gameGovernance);
			$this->getPresenter()->redrawControl('handlers');
		}
	}
	
	public function handleDecline() {
		if($this->gameGovernance->getActingPlayer()->getNickname()
			=== $this->gameGovernance->getGame()->getActivePlayer()->getNickname()) {
			$this->handler->declineSecondCard($this->gameGovernance);
			$this->getPresenter()->redrawControl('handlers');
		}
	}
	
}