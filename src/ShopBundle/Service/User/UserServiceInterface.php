<?php
/**
 * Created by PhpStorm.
 * User: Bloodless
 * Date: 18.12.2018 г.
 * Time: 1:16
 */

namespace ShopBundle\Service\User;


use Doctrine\Common\Collections\ArrayCollection;
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

    /**
     * @return ArrayCollection|User[]
     */
    public function getAllUsers();

    /**
     * @param int $userId
     * @param string $roleName
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editRoles(int $userId, string $roleName);
}