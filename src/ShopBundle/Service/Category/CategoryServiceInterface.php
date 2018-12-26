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

interface CategoryServiceInterface
{
    /**
     * @return ArrayCollection|Category[]
     */
    public function getAllCategories();
}