<?php

namespace ShopBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * User
 *
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="ShopBundle\Repository\UserRepository")
 *
 * @UniqueEntity(
 *     fields={"email"},
 *     errorPath="email",
 *     message="Account with email {{ value }} already exists!"
 * )
 *
 * @UniqueEntity(
 *     fields={"phone"},
 *     errorPath="phone",
 *     message="Account with phone number {{ value }} already exists!"
 * )
 */
class User implements UserInterface
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @Assert\NotBlank(message="First name field can't be empty!")
     * @Assert\Regex("/^(?:[\p{Cyrillic}]+|[\p{Latin}]+)$/u",
     *     message="First name must contains only letters (Cyrillic or Latin)!")
     * @Assert\Length(
     *      min = 2,
     *      max = 30,
     *      minMessage = "First name must be 2 letters or more!",
     *      maxMessage = "First name must be 30 letters or less!"
     * )
     *
     * @ORM\Column(name="firstName", type="string", length=30)
     */
    private $firstName;

    /**
     * @var string
     *
     * @Assert\NotBlank(message="Last name field can't be empty!")
     * @Assert\Regex("/^(?:[\p{Cyrillic}]+|[\p{Latin}]+)$/u",
     *     message="Last name must contains only letters (Cyrillic or Latin)!")
     * @Assert\Length(
     *      min = 2,
     *      max = 30,
     *      minMessage = "Last name must be 2 letters or more!",
     *      maxMessage = "Last name must be 30 letters or less!"
     * )
     *
     * @ORM\Column(name="lastName", type="string", length=30)
     */
    private $lastName;

    /**
     * @var string
     *
     * @Assert\NotBlank(message="Email field can't be empty!")
     * @Assert\Email(
     *     message="{{ value }} is not a valid email address!",
     *     checkMX=true
     * )
     *
     * @ORM\Column(name="email", type="string", length=50, unique=true)
     */
    private $email;

    /**
     * @var string
     *
     * @Assert\NotBlank(message="Phone number field can't be empty!")
     * @Assert\Regex("/^\+?[0-9]{8,14}$/",
     *     message="Phone number may start with '+' and contains between 8 and 14 digits!")
     *
     * @ORM\Column(name="phone", type="string", length=30)
     */
    private $phone;

    /**
     * @var string
     *
     * @Assert\NotBlank(groups={"Register"}, message="Password field can't be empty!")
     * @Assert\Regex("/^.{3,}$/",
     *     message="Password must be 3 symbols or more!")
     *
     * @ORM\Column(name="password", type="string", length=255)
     */
    private $password;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="registered", type="date")
     */
    private $registered;

    /**
     * @var ArrayCollection|Role[]
     *
     * @ORM\ManyToMany(targetEntity="ShopBundle\Entity\Role", inversedBy="users")
     * @ORM\JoinTable(name="users_roles",
     *     joinColumns={@ORM\JoinColumn(name="userId", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="roleId", referencedColumnName="id")})
     */
    private $roles;

    /**
     * @var ArrayCollection|Product[]
     *
     * @ORM\OneToMany(targetEntity="ShopBundle\Entity\Product", mappedBy="uploader")
     */
    private $uploadedProducts;

    /**
     * @var ArrayCollection|CartItem[]
     *
     * @ORM\OneToMany(targetEntity="ShopBundle\Entity\CartItem", mappedBy="user")
     */
    private $cartItems;

    /**
     * @var ArrayCollection|Order[]
     *
     * @ORM\OneToMany(targetEntity="ShopBundle\Entity\Order", mappedBy="user")
     */
    private $orders;

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->registered = new \DateTime('now');
        $this->roles = new ArrayCollection();
        $this->uploadedProducts = new ArrayCollection();
        $this->cartItems = new ArrayCollection();
        $this->orders = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set firstName
     *
     * @param string $firstName
     *
     * @return User
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     *
     * @return User
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set phone
     *
     * @param string $phone
     *
     * @return User
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set registered
     *
     * @param \DateTime $registered
     *
     * @return User
     */
    public function setRegistered($registered)
    {
        $this->registered = $registered;

        return $this;
    }

    /**
     * Get registered
     *
     * @return \DateTime
     */
    public function getRegistered()
    {
        return $this->registered;
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }

    /**
     * Returns the username used to authenticate the user.
     *
     * @return string The username
     */
    public function getUsername()
    {
        return $this->getEmail();
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    /**
     * Returns the roles granted to the user.
     *
     *     public function getRoles()
     *     {
     *         return array('ROLE_USER');
     *     }
     *
     * Alternatively, the roles might be stored on a ``roles`` property,
     * and populated in any number of different ways when the user object
     * is created.
     *
     * @return (Role|string)[] The user roles
     */
    public function getRoles()
    {
        $userRoles = [];

        foreach ($this->roles as $role) {
            /** @var Role $role */
            $userRoles[] = $role->getName();
        }

        return $userRoles;
    }

    /**
     * @param Role $role
     *
     * @return User
     */
    public function addRole($role): User
    {
        $this->roles[] = $role;

        return $this;
    }

    /**
     * @return ArrayCollection|Product[]
     */
    public function getUploadedProducts()
    {
        return $this->uploadedProducts;
    }

    /**
     * @param Product $product
     *
     * @return User
     */
    public function addUploadedProduct($product): User
    {
        $this->uploadedProducts[] = $product;
        return $this;
    }

    /**
     * @return ArrayCollection|CartItem[]
     */
    public function getCartItems()
    {
        return $this->cartItems;
    }

    /**
     * @param CartItem $cartItem
     *
     * @return User
     */
    public function addToCart($cartItem): User
    {
        $this->cartItems[] = $cartItem;

        return $this;
    }

    /**
     * @return ArrayCollection|Order[]
     */
    public function getOrders()
    {
        return $this->orders;
    }

    /**
     * @param Order $order
     *
     * @return User
     */
    public function addOrder($order): User
    {
        $this->orders[] = $order;

        return $this;
    }

    /**
     * @return bool
     */
    public function isAdmin()
    {
        return in_array("ROLE_ADMIN", $this->getRoles());
    }
}