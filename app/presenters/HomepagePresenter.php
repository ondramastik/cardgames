<?php

namespace App\Presenters;


class HomepagePresenter extends BasePresenter {
	
	public function actionDefault() {
		if($nickname = $this->getRequest()->getPost("nickname")) {
			$this->sessionSection->nickname = $nickname;
		}
	}
	
}
