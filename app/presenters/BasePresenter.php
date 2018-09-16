<?php

namespace App\Presenters;

use Nette\Application\UI\Presenter;
use Nette\Http\SessionSection;

class BasePresenter extends Presenter {
	
	/** @var SessionSection */
	protected $sessionSection;
	
	/** @var string */
	protected $nickname;
	
	/**
	 * BasePresenter constructor.
	 * @param \Nette\Http\Session $session
	 */
	public function __construct(\Nette\Http\Session $session) {
		parent::__construct();
		
		$this->sessionSection = $session->getSection('user');
		$this->nickname = $this->sessionSection->nickname;
	}
	
	public function beforeRender() {
		parent::beforeRender();
		$this->getTemplate()->nickname = $this->nickname;
	}
	
}
