<?php

namespace App\Presenters;

use App\Forms;
use Nette\Application\UI\Form;

final class SignPresenter extends BasePresenter {
    
    /** @var Forms\SignFormFactory @inject */
    public $signFormFactory;

    protected function createComponentSignInForm() : Form {
        return $this->signFormFactory->createSignIn(function () {
        	$this->flashMessage('Byli jste úšpěšně přihlášeni', 'info');
			$this->redirect('Homepage:');
		});
    }

    protected function createComponentSignUpForm() : Form {
        return $this->signFormFactory->createSignUp(function () {
			$this->flashMessage('Registrace proběhla úspěšně. Nyní se prosím přihlašte', 'success');
			$this->redirect('in');
		});
    }

    public function actionOut() : void {
        $this->getUser()->logout();
        $this->flashMessage('Byl jste odhlášen');
        $this->redirect('Lobby:');
    }
    
}
