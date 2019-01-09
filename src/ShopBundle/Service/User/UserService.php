<?php
/**
 * Created by PhpStorm.
 * User: Bloodless
 * Date: 18.12.2018 г.
 * Time: 1:16
 */

namespace ShopBundle\Service\User;


use Doctrine\Common\Collections\ArrayCollection;
use ShopBundle\Entity\Product;
use ShopBundle\Entity\Role;
use ShopBundle\Entity\User;
use ShopBundle\Repository\ProductRepository;
use ShopBundle\Repository\RoleRepository;
use ShopBundle\Repository\UserRepository;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
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
     * @var ProductRepository $productRepository
     */
    private $productRepository;

    /**
     * @var TokenStorageInterface
     */
    private $security;

    /**
     * UserService constructor.
     * @param UserRepository $userRepository
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param RoleRepository $roleRepository
     */
    public function __construct(UserRepository $userRepository,
                                UserPasswordEncoderInterface $passwordEncoder,
                                RoleRepository $roleRepository,
                                ProductRepository $productRepository,
                                TokenStorageInterface $security)
    {
        $this->userRepository = $userRepository;
        $this->passwordEncoder = $passwordEncoder;
        $this->roleRepository = $roleRepository;
        $this->productRepository = $productRepository;
        $this->security = $security;
    }

    /**
     * @return User|object|null
     */
    public function getCurrentUser()
    {
        return $this->security->getToken()->getUser();
    }

    /**
     * @param User $user
     * @throws \Doctrine\ORM\OptimisticLockException
     * @return \Symfony\Component\HttpFoundation\Response
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
     * @throws \Doctrine\ORM\OptimisticLockException
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function edit(User $user, string $currentHashedPass = null)
    {
        if ($currentHashedPass !== null) {
            if (null === $user->getPassword()) {
                $user->setPassword($currentHashedPass);
            } else {
                $hashedPassword = $this->passwordEncoder
                    ->encodePassword($user, $user->getPassword());

                $user->setPassword($hashedPassword);
            }
        }

        return $this->userRepository->edit($user);
    }

    /**
     * @return ArrayCollection|User[]
     */
    public function getAllUsers()
    {
        return $this->userRepository->findBy([], ['id' => 'ASC']);
    }

    /**
     * @param int $userId
     * @param int $roleId
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function editRoles(int $userId, int $roleId)
    {
        /** @var User $user */
        $user = $this->userRepository->find($userId);

        /** @var Role $role */
        $role = $this->roleRepository->find($roleId);

        if (in_array($role->getName(), $user->getRoles())) {
            $user->removeRole($role);
        } else {
            $user->addRole($role);
        }

        return $this->userRepository->edit($user);
    }

    /**
     * @param int $productId
     * @throws \Doctrine\ORM\OptimisticLockException
     * @return bool
     */
    public function editWishList(int $productId)
    {
        $user = $this->getCurrentUser();

        /** @var Product $product */
        $product = $this->productRepository->find($productId);

        if (in_array($product, $user->getWishList()->toArray())) {
            $user->removeFromWishList($product);
            $this->userRepository->edit($user);
            return false;
        }

        $user->addToWishList($product);
        $this->userRepository->edit($user);

        return true;
    }
}