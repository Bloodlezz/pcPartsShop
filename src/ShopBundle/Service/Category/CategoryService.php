<?php
/**
 * Created by PhpStorm.
 * User: Bloodless
 * Date: 20.12.2018 г.
 * Time: 21:10
 */

namespace ShopBundle\Service\Category;


use Doctrine\Common\Collections\ArrayCollection;
use ShopBundle\Entity\Category;
use ShopBundle\Entity\Product;
use ShopBundle\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CategoryService implements CategoryServiceInterface
{
    /**
     * @var Category $categoryRepository
     */
    private $categoryRepository;

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * CategoryService constructor.
     * @param CategoryRepository $categoryRepository
     * @param SessionInterface $session
     */
    public function __construct(CategoryRepository $categoryRepository, SessionInterface $session)
    {
        $this->categoryRepository = $categoryRepository;
        $this->session = $session;
    }

    /**
     * @return ArrayCollection|Category[]
     */
    public function getAllCategories()
    {
        return $this->categoryRepository->findAll();
    }

    /**
     * @return array
     */
    public function getCategoriesName()
    {
        if ($this->session->has('categories')) {
            return $this->session->get('categories');
        }

        $result = [];

        /** @var Category $category */
        foreach ($this->getAllCategories() as $category) {
            $result[] = $category->getName();
        }

        sort($result);
        $this->session->set('categories', $result);

        return $result;
    }

    /**
     * @param string $categoryName
     * @return ArrayCollection|Product[]|null
     */
    public function getCategoryProducts(string $categoryName)
    {
        /** @var Category $category */
        $category = $this->categoryRepository->findOneBy(['name' => $categoryName]);

        return $category->getProducts();
    }
}