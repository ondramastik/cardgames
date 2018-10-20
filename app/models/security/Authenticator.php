<?php

namespace App\Models\Security;


use Kdyby\Doctrine\EntityManager;
use Nette\Security as NS;

class Authenticator implements NS\IAuthenticator {

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * Authenticator constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager) {
        $this->entityManager = $entityManager;
    }

    function authenticate(array $credentials) {
        list($nickname, $password) = $credentials;

        /** @var UserEntity|null $user */
        $user = $this->entityManager->getRepository(UserEntity::class)->findOneBy([
            'nickname' => $nickname
        ]);

        if ($user != null) {
            if (!NS\Passwords::verify($password, $user->getPassword())) {
                throw new NS\AuthenticationException('Invalid password.');
            }
        } else {
            throw new NS\AuthenticationException('User not found.');
        }

        return new NS\Identity($user->getId(), [], ['userEntity' => $user]);
    }


}