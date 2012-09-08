<?php

namespace Koios\BlogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="users")
 */
class User implements UserInterface {
    protected $id;

    /**
     * @ORM\Column(type="string", length=255) @ORM\Id
     */
    protected $username;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $password;

    /**
     * @ORM\Column(type="string", length=50)
     */
    protected $role;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $salt = null;

    public function setRole($role) {
        if (!in_array($role, array('ROLE_ADMIN', 'ROLE_USER'))) {
            throw new \InvalidArgument('Invalid role specified.');
        }
        $this->role = $role;
    }


    public function getRoles() {
        return array($this->role);
    }

    public function getPassword() {
        return $this->password;
    }

    public function getSalt() {
        return $this->salt;
    }

    public function setSalt($salt) {
        $this->salt = $salt;
        return $this;
    }

    public function getUsername() {
        return $this->username;
    }

    public function setUsername($username) {
        $this->username = $username;
        return $this;
    }

    public function eraseCredentials() {
    }

    public function equals(UserInterface $user) {
        return $user->getUsername() == $this->getUsername();
    }

}
