<?php
/**
 * Created by PhpStorm.
 * User: Bloodless
 * Date: 20.12.2018 г.
 * Time: 21:20
 */

namespace ShopBundle\Service\Product;


use ShopBundle\Entity\Product;
use Symfony\Component\HttpFoundation\File\UploadedFile;

interface ProductServiceInterface
{
    /**
     * @param Product $product
     * @param string $uploadDir
     * @param UploadedFile $imageFile
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createProduct(Product $product, string $uploadDir, UploadedFile $imageFile);

    /**
     * @param int $id
     * @return Product
     */
    public function getProductById(int $id);

    /**
     * @param Product $product
     * @param string $uploadDir
     * @param string $currentImgName
     * @param UploadedFile|null $imageFile
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editProduct(Product $product, string $uploadDir, string $currentImgName, UploadedFile $imageFile = null);

    /**
     * @param Product $product
     * @param string $uploadDir
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteProduct(Product $product, string $uploadDir);
}