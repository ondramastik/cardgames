<?php
declare(strict_types=1);

namespace App\Forms;

use App\Models;
use App\Models\Repository\UserRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManager;
use Nette\Application\UI\Form;
use Nette\Security;

final class SignFormFactory {
    public const PASSWORD_MIN_LENGTH = 7;

    /** @var FormFactory */
    private $factory;

    /** @var Security\User */
    private $user;

    /** @var EntityManager */
    private $entityManager;

    public function __construct(FormFactory $factory, Security\User $user, EntityManager $entityManager) {
        $this->factory = $factory;
        $this->user = $user;
        $this->entityManager = $entityManager;
    }

    public function createSignIn(callable $success): Form {
        $form = $this->factory->create();
        $form->addText('nickname', 'Přezdívka:')
            ->setRequired('Zadejte přezdívku.');

        $form->addPassword('password', 'Heslo:')
            ->setRequired('Zadejte heslo.');

        $remember = $form->addCheckbox('remember', 'Zůstat přihlášen');
        $remember->getLabelPrototype()
            ->appendAttribute('class', 'form-check-label');
        $remember->getControlPrototype()
            ->appendAttribute('class', 'form-check-input');

        $form->addSubmit('send', 'Přihlásit')
            ->setAttribute('class', 'btn-primary float-right');

        $form->onSuccess[] = function (Form $form, \stdClass $values) use ($success) : void {
            try {
                $this->user->setExpiration($values->remember ? '14 days' : '20 minutes');
                $this->user->login($values->nickname, $values->password);
                $success();
            } catch (Security\AuthenticationException $e) {
                $form->addError('Byly zadány neplatné přihlašovací údaje');
                return;
            }
        };

        return $form;
    }

    public function createSignUp(callable $success): Form {
        $form = $this->factory->create();
        $form->addText('nickname', 'Přezdívka:')
            ->setRequired('Zadejte prosím pžezdívku.');

        $form->addPassword('password', 'Heslo:')
            ->setRequired('Zadejte heslo.')
            ->addRule($form::MIN_LENGTH, null, self::PASSWORD_MIN_LENGTH);

        $form->addSubmit('send', 'Registrovat')
            ->setAttribute('class', 'btn-primary float-right');

        $form->onSuccess[] = function (Form $form, \stdClass $values) use ($success) : void {
            try {
                $this->entityManager->persist(new Models\Security\UserEntity($values->nickname, $values->password));
                $this->entityManager->flush();
                $success();
            } catch (UniqueConstraintViolationException $e) {
                $form->addError('Uživatel s tímto jménem již existuje.');
                return;
            }
        };

        return $form;
    }
}
