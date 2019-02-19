<?php

namespace App\Components\Bang;


use App\Models\Bang\Card;
use App\Models\Bang\GameGovernance;
use App\Models\Bang\Handlers\SidKetchum;
use Nette\Application\UI\Control;

class SidKetchumControl extends Control {
	
	/** @var GameGovernance */
	private $gameGovernance;
	
	/** @var SidKetchum */
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
		$this->getTemplate()->setFile(__DIR__ . '/../../templates/Bang/handlers/sidketchum.latte');
		
		$cards = array_filter($this->gameGovernance->getActingPlayer()->getHand(), function (Card $card) {
			return (!$this->handler->getFirstCard() || $this->handler->getFirstCard()->getIdentifier() !== $card->getIdentifier())
				&& (!$this->handler->getSecondCard() || $this->handler->getSecondCard()->getIdentifier() !== $card->getIdentifier());
		});
		
		$this->getTemplate()->cards = $cards;
		$this->getTemplate()->handler = $this->handler;
		
		$this->getTemplate()->render();
	}
	
	/**
	 * @param string $cardIdentifier
	 */
	public function handleChooseCard(string $cardIdentifier) {
		foreach ($this->gameGovernance->getGame()->getActivePlayer()->getHand() as $card) {
			if($card->getIdentifier() === $cardIdentifier) {
				if(!$this->handler->getFirstCard()) {
					$this->handler->setFirstCard($card);
				} else if(!$this->handler->getSecondCard()) {
					$this->handler->setSecondCard($card);
				}
				$this->redrawControl('sid-ketchum');
			}
		}
	}
	
	public function handleFinish() {
		if($this->gameGovernance->getActingPlayer()->getNickname()
			=== $this->gameGovernance->getGame()->getActivePlayer()->getNickname()) {
			$this->handler->finish($this->gameGovernance);
			$this->gameGovernance->getGame()->setHandler(null);
			$this->getPresenter()->redrawControl('handlers');
		}
	}
	
	public function handleCancel() {
		if($this->gameGovernance->getActingPlayer()->getNickname()
			=== $this->gameGovernance->getGame()->getActivePlayer()->getNickname()) {
			$this->gameGovernance->getGame()->setHandler(null);
			$this->getPresenter()->redrawControl('handlers');
		}
	}
	
	public function handleCancelFirstCard() {
		if($this->gameGovernance->getActingPlayer()->getNickname()
			=== $this->gameGovernance->getGame()->getActivePlayer()->getNickname()) {
			$this->handler->setFirstCard(null);
			$this->redrawControl('sid-ketchum');
		}
	}
	
	public function handleCancelSecondCard() {
		if($this->gameGovernance->getActingPlayer()->getNickname()
			=== $this->gameGovernance->getGame()->getActivePlayer()->getNickname()) {
			$this->handler->setSecondCard(null);
			$this->redrawControl('sid-ketchum');
		}
	}
	
}