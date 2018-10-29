<?php

namespace App\Components\Chat;


use App\Models\Lobby\Log\Log;
use Nette\Application\UI\Control;

class LogControl extends Control {
	
	/** @var Log */
	private $log;
	
	/**
	 * LogControl constructor.
	 * @param Log $log
	 */
	public function __construct(Log $log) {
		parent::__construct();
		$this->log = $log;
	}
	
	public function render() {
		$this->getTemplate()->setFile(__DIR__ . '/../../templates/Log/log.latte');
		
		$this->getTemplate()->events = $this->log->getEvents();
		
		$this->getTemplate()->render();
	}
	
}