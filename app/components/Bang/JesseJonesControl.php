<?php

namespace App\Components\Bang;


use App\Models\Bang\GameGovernance;
use App\Models\Bang\Handlers\JesseJones;
use App\Models\Bang\PlayerUtils;
use Nette\Application\UI\Control;

class JesseJonesControl extends Control {
	
	/** @var GameGovernance */
	private $gameGovernance;
	
	/** @var JesseJones */
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
		$this->getTemplate()->setFile(__DIR__ . '/../../templates/Bang/handlers/jessejones.latte');
		
		$this->getTemplate()->game = $this->gameGovernance->getGame();
		
		$this->getTemplate()->render();
	}
	
	/**
	 * @param string $nickname
	 */
	public function handleSteal(string $nickname) {
		if(PlayerUtils::equals($this->gameGovernance->getActingPlayer(), $this->gameGovernance->getGame()->getActivePlayer())) {
			$player = $this->gameGovernance->getGame()->getPlayer($nickname);
			
			if($player) {
				$this->handler->steal($this->gameGovernance, $player);
				
				$this->gameGovernance->getGame()->setHandler(null);
				$this->getPresenter()->redrawControl('handlers');
			}
		}
	}
	
	public function handleCancel() {
		if(PlayerUtils::equals($this->gameGovernance->getActingPlayer(), $this->gameGovernance->getGame()->getActivePlayer())) {
			$this->gameGovernance->getGame()->setHandler(null);
			$this->getPresenter()->redrawControl('handlers');
		}
	}
	
}