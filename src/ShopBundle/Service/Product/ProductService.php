<?php
/**
 * Created by PhpStorm.
 * User: Bloodless
 * Date: 20.12.2018 г.
 * Time: 21:20
 */

namespace ShopBundle\Service\Product;


use Doctrine\Common\Collections\ArrayCollection;
use ShopBundle\Entity\Product;
use ShopBundle\Repository\ProductRepository;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ProductService implements ProductServiceInterface
{
    /**
     * @var ProductRepository $productRepository
     */
    private $productRepository;

    /**
     * @var Filesystem $fileSystem
     */
    private $fileSystem;

    /**
     * ProductService constructor.
     * @param ProductRepository $productRepository
     * @param Filesystem $fileSystem
     */
    public function __construct(ProductRepository $productRepository, Filesystem $fileSystem)
    {
        $this->productRepository = $productRepository;
        $this->fileSystem = $fileSystem;
    }

    /**
     * @param Product $product
     * @param string $uploadDir
     * @param UploadedFile $imageFile
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function createProduct(Product $product, string $uploadDir, UploadedFile $imageFile)
    {
        $imageName = md5(uniqid()) . "." . $imageFile->guessExtension();

        try {
            $imageFile->move($uploadDir, $imageName);
        } catch (FileException $ex) {
            $ex->getMessage();
        }

        $product->setImage($imageName);
        return $this->productRepository->create($product);
    }

    /**
     * @param int $id
     * @return Product|object
     */
    public function getProductById(int $id)
    {
        return $this->productRepository->find($id);
    }

    /**
     * @param Product $product
     * @param string $uploadDir
     * @param string $currentImgName
     * @param UploadedFile|null $imageFile
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function editProduct(Product $product, string $uploadDir, string $currentImgName, UploadedFile $imageFile = null)
    {
        if (null !== $imageFile) {
            $imageName = md5(uniqid()) . "." . $imageFile->guessExtension();
            $currentImgFullPath = $uploadDir . $currentImgName;

            try {
                $imageFile->move($uploadDir, $imageName);

                if ($this->fileSystem->exists($currentImgFullPath)) {
                    $this->fileSystem->remove($currentImgFullPath);
                }

                $product->setImage($imageName);

            } catch (FileException $ex) {
                $ex->getMessage();
            }
        } else {
            $product->setImage($currentImgName);
        }

        return $this->productRepository->edit($product);
    }

    /**
     * @param Product $product
     * @throws \Doctrine\ORM\OptimisticLockException
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function outOfStock(Product $product)
    {
//        $currentImgFullPath = $uploadDir . $product->getImage();
//
//        try {
//            if ($this->fileSystem->exists($currentImgFullPath)) {
//                $this->fileSystem->remove($currentImgFullPath);
//            }
//        } catch (FileException $ex) {
//            $ex->getMessage();
//        }

        return $this->productRepository->edit($product);
    }

    /**
     * @return ArrayCollection|Product[]|null
     */
    public function getAllTopProducts()
    {
        return $this->productRepository->findBy(['topProduct' => true], ['price' => 'ASC']);
    }
}