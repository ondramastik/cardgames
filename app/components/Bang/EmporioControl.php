<?php

namespace App\Components\Bang;


use Nette\Application\UI\Control;
use App\Models\Bang\Handlers;

class EmporioControl extends Control {
	
	/** @var Handlers\Emporio */
	private $handler;
	
	/**
	 * EmporioControl constructor.
	 * @param Handlers\Emporio $handler
	 */
	public function __construct(Handlers\Emporio $handler) {
		parent::__construct();
		$this->handler = $handler;
	}
	
	public function render() {
		$this->getTemplate()->setFile(__DIR__ . '/../../templates/Bang/handlers/emporio.latte');
		
		$this->getTemplate()->cards = $this->handler->getCards();
		
		$this->getTemplate()->render();
	}
	
	/**
	 * @param string $cardIdentifier
	 * @throws \ReflectionException
	 */
	public function handleChooseCard(string $cardIdentifier) {
		foreach ($this->handler->getCards() as $card) {
			if($card->getIdentifier() === $cardIdentifier) {
				$this->handler->choseCard($card);
			}
		}
	}
}