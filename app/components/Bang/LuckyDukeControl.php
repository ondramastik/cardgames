<?php

namespace App\Components\Bang;


use App\Models\Bang\GameGovernance;
use App\Models\Bang\Handlers\LuckyDuke;
use Nette\Application\UI\Control;

class LuckyDukeControl extends Control {
	
	/** @var GameGovernance */
	private $gameGovernance;
	
	/** @var LuckyDuke */
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
		$this->getTemplate()->setFile(__DIR__ . '/../../templates/Bang/handlers/luckyduke.latte');
		
		$this->getTemplate()->eligibleCards = $this->handler->getEligibleCards($this->gameGovernance);
		$this->getTemplate()->cards = $this->handler->getCards();
		$this->getTemplate()->blueCard = $this->handler->getBlueCard();
		
		$this->getTemplate()->render();
	}
	
	/**
	 * @param string $cardIdentifier
	 * @throws \ReflectionException
	 */
	public function handleChooseBlueCard(string $cardIdentifier) {
		$card = null;
		
		foreach ($this->gameGovernance->getActingPlayer()->getTable() as $blueCard) {
			if($blueCard->getIdentifier() === $cardIdentifier) {
				$card = $blueCard;
				break;
			}
		}
		
		if($this->gameGovernance->getActingPlayer()->getNickname()
			=== $this->gameGovernance->getGame()->getActivePlayer()->getNickname()
			|| $this->gameGovernance->getActingPlayer()->getNickname()
			=== $this->gameGovernance->getGame()->getPlayerToRespond()->getNickname()
			&& $this->handler->getBlueCard() === null && $card !== null) {
			
			$this->handler->chooseBlueCard($this->gameGovernance, $card);
			
			$this->redrawControl('lucky-duke');
		}
	}
	
	/**
	 * @param string $cardIdentifier
	 * @throws \ReflectionException
	 */
	public function handleChooseCard(string $cardIdentifier) {
		$card = null;
		
		foreach ($this->handler->getCards() as $possibleCard) {
			if($possibleCard->getIdentifier() === $cardIdentifier) {
				$card = $possibleCard;
				break;
			}
		}
		
		if($card !== null && $this->handler->chooseCard($this->gameGovernance, $card)) {
			$this->gameGovernance->getGame()->setHandler(null);
			$this->getPresenter()->redrawControl('handlers');
		}
	}
	
	public function handleCancel() {
		if($this->handler->getCards() === null) {
			$this->gameGovernance->getGame()->setHandler(null);
			$this->getPresenter()->redrawControl('handlers');
		}
	}
	
}