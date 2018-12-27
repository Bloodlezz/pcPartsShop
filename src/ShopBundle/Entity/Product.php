<?php

namespace ShopBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Product
 *
 * @ORM\Table(name="products")
 * @ORM\Entity(repositoryClass="ShopBundle\Repository\ProductRepository")
 *
 * @UniqueEntity(
 *     fields={"title"},
 *     errorPath="title",
 *     message="Product with this title already exists!"
 * )
 */
class Product
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
     * @Assert\NotBlank(message="Title field can't be empty!")
     *
     * @ORM\Column(name="title", type="string", length=255, unique=true)
     */
    private $title;

    /**
     * @var string
     *
     * @Assert\NotBlank(message="Description field can't be empty!")
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @var string
     *
     * @Assert\NotBlank(message="Price field can't be empty or zero!")
     * @Assert\Regex(
     *     "/^[0-9]{1,4}\.?[0-9]{0,2}$/",
     *     message="Price should be a valid number in range [0.01-9999]!")
     *
     * @ORM\Column(name="price", type="decimal", precision=10, scale=2)
     */
    private $price;

    /**
     * @var string
     *
     * @Assert\NotBlank(groups={"Edit"}, message="Please choose an image file (jpeg or png)!")
     * @Assert\File(
     *     maxSize = "2Mi",
     *     mimeTypes = {"image/jpeg", "image/png"},
     *     mimeTypesMessage = "Allowed file types are jpeg & png!"
     * )
     *
     * @ORM\Column(name="image", type="string", length=255)
     */
    private $image;

    /**
     * @var bool
     *
     * @ORM\Column(name="topProduct", type="boolean")
     */
    private $topProduct;

    /**
     * @var int
     *
     * @ORM\Column(name="orderedCount", type="integer")
     */
    private $orderedCount;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="added", type="datetime")
     */
    private $added;

    /**
     * @var Category
     *
     * @Assert\NotNull(message="Please choose product category!")
     *
     * @ORM\ManyToOne(targetEntity="ShopBundle\Entity\Category", inversedBy="products")
     * @ORM\JoinColumn(name="categoryId", referencedColumnName="id", nullable=false)
     */
    private $category;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="ShopBundle\Entity\User", inversedBy="uploadedProducts")
     * @ORM\JoinColumn(name="uploaderId", referencedColumnName="id", nullable=false)
     */
    private $uploader;

    /**
     * @var ArrayCollection|OrderItem[]
     *
     * @ORM\OneToMany(targetEntity="ShopBundle\Entity\OrderItem", mappedBy="product")
     */
    private $orderItems;

    /**
     * Product constructor.
     */
    public function __construct()
    {
        $this->orderedCount = 0;
        $this->added = new \DateTime('now');
        $this->orderItems = new ArrayCollection();
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
     * Set title
     *
     * @param string $title
     *
     * @return Product
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Product
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set price
     *
     * @param string $price
     *
     * @return Product
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return string
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set image
     *
     * @param string $image
     *
     * @return Product
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set topProduct
     *
     * @param boolean $topProduct
     *
     * @return Product
     */
    public function setTopProduct($topProduct)
    {
        $this->topProduct = $topProduct;

        return $this;
    }

    /**
     * Get topProduct
     *
     * @return bool
     */
    public function getTopProduct()
    {
        return $this->topProduct;
    }

    /**
     * Set orderedCount
     *
     * @param integer $orderedCount
     *
     * @return Product
     */
    public function setOrderedCount($orderedCount)
    {
        $this->orderedCount = $orderedCount;

        return $this;
    }

    /**
     * Get orderedCount
     *
     * @return int
     */
    public function getOrderedCount()
    {
        return $this->orderedCount;
    }

    /**
     * Set added
     *
     * @param \DateTime $added
     *
     * @return Product
     */
    public function setAdded($added)
    {
        $this->added = $added;

        return $this;
    }

    /**
     * Get added
     *
     * @return \DateTime
     */
    public function getAdded()
    {
        return $this->added;
    }

    /**
     * @return Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param Category $category
     */
    public function setCategory(Category $category): void
    {
        $this->category = $category;
    }

    /**
     * @return User
     */
    public function getUploader(): User
    {
        return $this->uploader;
    }

    /**
     * @param User $user
     */
    public function setUploader(User $user): void
    {
        $this->uploader = $user;
    }

    /**
     * @return ArrayCollection|OrderItem[]
     */
    public function getOrderItems()
    {
        return $this->orderItems;
    }

    /**
     * @param OrderItem $orderItem
     *
     * @return Product
     */
    public function addOrderItem($orderItem): Product
    {
        $this->orderItems[] = $orderItem;

        return $this;
    }
}

