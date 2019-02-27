<?php

namespace App\Components\Bang;


use App\Models\Bang\GameGovernance;
use Nette\Application\UI\Control;
use App\Models\Bang\Handlers;

class EmporioControl extends Control {
	
	/** @var Handlers\Emporio */
	private $handler;
	
	/** @var GameGovernance */
	private $gameGovernance;
	
	/**
	 * EmporioControl constructor.
	 * @param GameGovernance $gameGovernance
	 */
	public function __construct(GameGovernance $gameGovernance) {
		parent::__construct();
		$this->handler = $gameGovernance->getGame()->getHandler();
		$this->gameGovernance = $gameGovernance;
	}
	
	public function render() {
		$this->getTemplate()->setFile(__DIR__ . '/../../templates/Bang/handlers/emporio.latte');
		
		$this->getTemplate()->cards = $this->handler->getCards();
		$this->getTemplate()->playerOnTurn = $this->handler->getPlayerOnTurn();
		
		$this->getTemplate()->render();
	}

	/**
	 * @param string $cardIdentifier
	 */
	public function handleChooseCard(string $cardIdentifier) {
		foreach ($this->handler->getCards() as $card) {
			if($card->getIdentifier() === $cardIdentifier) {
				$this->handler->choseCard($this->gameGovernance, $card);
				$this->redrawControl('emporio');
			}
		}
	}
}