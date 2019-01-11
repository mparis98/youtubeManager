<?php
/**
 * Created by PhpStorm.
 * User: matthieuparis
 * Date: 10/01/2019
 * Time: 14:08
 */

namespace App\Event;


use App\Entity\User;
use Symfony\Component\EventDispatcher\Event;

class UserRegisteredEvent extends Event {

    const NAME = 'user.registered';
    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user
        ;
    }
    public function getUser(): User
    {
        return $this->user
            ;
    }
}