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
use ShopBundle\Repository\CategoryRepository;

class CategoryService implements CategoryServiceInterface
{
    /**
     * @var Category $categoryRepository
     */
    private $categoryRepository;

    /**
     * CategoryService constructor.
     * @param CategoryRepository $categoryRepository
     */
    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @return ArrayCollection|Category[]
     */
    public function getAllCategories()
    {
        return $this->categoryRepository->findAll();
    }
}