<?php

namespace App\Forms;

use Nette;
use Nette\Application\UI\Form;

class FormFactory {
    use Nette\SmartObject;

    public function create(): Form {
        $form = new Form;

        return $form;
    }
}
