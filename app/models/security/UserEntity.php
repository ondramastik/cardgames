<?php

namespace App\Models\Security;

use Nette\Security\Passwords;

/**
 * @ORM\Entity
 * @ORM\Table(name="user")
 */
class UserEntity {

    use \Kdyby\Doctrine\Entities\Attributes\Identifier;

    /**
     * @ORM\Column(type="string")
     */
    protected $nickname;

    /**
     * @ORM\Column(type="string")
     */
    protected $password;

    /**
     * UserEntity constructor.
     * @param $nickname
     * @param $password
     */
    public function __construct($nickname, $password) {
        $this->nickname = $nickname;
        $this->password = Passwords::hash($password);
    }

    /**
     * @return mixed
     */
    public function getNickname() {
        return $this->nickname;
    }

    /**
     * @param mixed $nickname
     */
    public function setNickname($nickname): void {
        $this->nickname = $nickname;
    }

    /**
     * @return mixed
     */
    public function getPassword() {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password): void {
        $this->password = Passwords::hash($password);
    }

}
