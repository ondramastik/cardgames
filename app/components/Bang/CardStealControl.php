<?php

namespace App\Components\Bang;


use App\Models\Bang\GameGovernance;
use App\Models\Bang\Handlers\CardSteal;
use App\Models\Bang\PlayerUtils;
use Nette\Application\UI\Control;

class CardStealControl extends Control {
	
	/** @var GameGovernance */
	private $gameGovernance;
	
	/** @var CardSteal */
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
		$this->getTemplate()->setFile(__DIR__ . '/../../templates/Bang/handlers/cardsteal.latte');
		
		$this->getTemplate()->game = $this->gameGovernance->getGame();
		
		$this->getTemplate()->render();
	}

	/**
	 * @param string|null $cardIdentifier
	 */
	public function handleSteal(string $cardIdentifier = null) {
		if(PlayerUtils::equals($this->gameGovernance->getActingPlayer(), $this->gameGovernance->getGame()->getActivePlayer())) {
			$card = null;
			
			foreach ($this->gameGovernance->getGame()->getCardsDeck()->getTopPlayedCard()->getTargetPlayer()->getTable() as $blueCard) {
				if($blueCard->getIdentifier() === $cardIdentifier) {
					$card = $blueCard;
				}
			}
			
			$this->handler->steal($this->gameGovernance, $card);
			
			$this->gameGovernance->getGame()->setHandler(null);
			$this->getPresenter()->redrawControl('handlers');
		}
	}
	
}