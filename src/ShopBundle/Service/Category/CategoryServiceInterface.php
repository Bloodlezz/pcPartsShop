<?php
/**
 * Created by PhpStorm.
 * User: Bloodless
 * Date: 20.12.2018 г.
 * Time: 21:09
 */

namespace ShopBundle\Service\Category;


use Doctrine\Common\Collections\ArrayCollection;
use ShopBundle\Entity\Category;
use ShopBundle\Entity\Product;

interface CategoryServiceInterface
{
    /**
     * @return ArrayCollection|Category[]
     */
    public function getAllCategories();

    /**
     * @return array
     */
    public function getCategoriesName();

    /**
     * @param string $categoryName
     * @return ArrayCollection|Product[]|null
     */
    public function getCategoryProducts(string $categoryName);
}