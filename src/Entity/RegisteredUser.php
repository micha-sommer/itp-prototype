<?php
/**
 * Created by PhpStorm.
 * User: mremm
 * Date: 24.08.2018
 * Time: 10:13
 */

namespace App\Entity;

use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Validator\Constraints as Assert;

class RegisteredUser extends Registration
{
    /**
     * @var string | null
     * @Assert\Length(max=4096)
     * @UserPassword(
     *
     * )
     */
    private $oldPassword;


    public function getOldPassword()
    {
        return $this->oldPassword;
    }

    public function setOldPassword($oldPassword): void
    {
        $this->oldPassword = $oldPassword;
    }
}