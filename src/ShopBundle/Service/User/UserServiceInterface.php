<?php
/**
 * Created by PhpStorm.
 * User: Bloodless
 * Date: 18.12.2018 г.
 * Time: 1:16
 */

namespace ShopBundle\Service\User;


use ShopBundle\Entity\User;

interface UserServiceInterface
{
    /**
     * @param User $user
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function register(User $user);

    /**
     * @param int $id
     * @return User
     */
    public function getUserById(int $id);

    /**
     * @param User $user
     * @param string $currentHashedPass
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function edit(User $user, string $currentHashedPass = null);
}