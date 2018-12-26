<?php
/**
 * Created by PhpStorm.
 * User: Bloodless
 * Date: 18.12.2018 г.
 * Time: 1:16
 */

namespace ShopBundle\Service\User;


use ShopBundle\Entity\Role;
use ShopBundle\Entity\User;
use ShopBundle\Repository\RoleRepository;
use ShopBundle\Repository\UserRepository;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserService implements UserServiceInterface
{
    const NEW_USER_ROLE = 'ROLE_USER';

    /**
     * @var UserRepository $userRepository
     */
    private $userRepository;

    /**
     * @var UserPasswordEncoderInterface $passwordEncoder
     */
    private $passwordEncoder;

    /**
     * @var RoleRepository $roleRepository
     */
    private $roleRepository;

    /**
     * UserService constructor.
     * @param UserRepository $userRepository
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param RoleRepository $roleRepository
     */
    public function __construct(UserRepository $userRepository, UserPasswordEncoderInterface $passwordEncoder, RoleRepository $roleRepository)
    {
        $this->userRepository = $userRepository;
        $this->passwordEncoder = $passwordEncoder;
        $this->roleRepository = $roleRepository;
    }

    /**
     * @param User $user
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function register(User $user)
    {
        $hashedPassword = $this->passwordEncoder
            ->encodePassword($user, $user->getPassword());

        $user->setPassword($hashedPassword);

        /** @var Role $role */
        $role = $this->roleRepository->findOneBy(['name' => self::NEW_USER_ROLE]);

        $user->addRole($role);

        return $this->userRepository->register($user);
    }

    /**
     * @param int $id
     * @return User|object|null
     */
    public function getUserById(int $id)
    {
        return $this->userRepository->find($id);
    }

    /**
     * @param User $user
     * @param string $currentHashedPass
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function edit(User $user, string $currentHashedPass)
    {
        if (null === $user->getPassword()) {
            $user->setPassword($currentHashedPass);
        } else {
            $hashedPassword = $this->passwordEncoder
                ->encodePassword($user, $user->getPassword());

            $user->setPassword($hashedPassword);
        }

        return $this->userRepository->edit($user);
    }
}