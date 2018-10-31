<?php

namespace App\Components\Bang;


use App\Models\Bang\Card;
use App\Models\Bang\GameGovernance;
use App\Models\Bang\Handlers\KitCarlson;
use App\Models\Bang\Handlers\SidKetchum;
use Nette\Application\UI\Control;

class KitCarlsonControl extends Control {
	
	/** @var GameGovernance */
	private $gameGovernance;
	
	/** @var KitCarlson */
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
		$this->getTemplate()->setFile(__DIR__ . '/../../templates/Bang/handlers/kitcarlson.latte');
		
		$this->getTemplate()->cards = $this->handler->getCards();
		
		$this->getTemplate()->render();
	}
	
	/**
	 * @param string $cardIdentifier
	 * @throws \ReflectionException
	 */
	public function handleChooseUnwantedCard(string $cardIdentifier) {
		if($this->gameGovernance->getActingPlayer()->getNickname()
			=== $this->gameGovernance->getGame()->getActivePlayer()->getNickname()) {
			
			$chosenCard = null;
			foreach ($this->handler->getCards() as $card) {
				if($card->getIdentifier() === $cardIdentifier) {
					$chosenCard = $card;
				}
			}
			
			if($chosenCard === null) {
				return;
			}
			
			$this->handler->choseUnwantedCard($this->gameGovernance, $chosenCard);
			
			$this->gameGovernance->getGame()->setHandler(null);
			$this->getPresenter()->redrawControl('handlers');
		}
	}
	
}