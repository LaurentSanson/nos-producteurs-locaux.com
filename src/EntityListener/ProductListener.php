<?php

namespace App\EntityListener;

use App\Entity\Product;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Uid\Uuid;

/**
 * Class ProductListener
 * @package App\EntityListener
 */
class ProductListener
{

    /**
     * @var Security
     */
    private Security $security;

    /**
     * @var string
     */
    private string $uploadWebDir;

    /**
     * @var string
     */
    private string $uploadAsboluteDir;


    /**
     * ProductListener constructor.
     * @param Security $security
     * @param string $uploadWebDir
     * @param string $uploadAbsoluteDir
     */
    public function __construct(Security $security, string $uploadWebDir, string $uploadAbsoluteDir)
    {
        $this->security = $security;
        $this->uploadWebDir = $uploadWebDir;
        $this->uploadAsboluteDir = $uploadAbsoluteDir;
    }

    /**
     * @param Product $product
     */
    public function prePersist(Product $product): void
    {
        if ($product->getFarm() !== null) {
            return;
        }

        $product->setFarm($this->security->getUser()->getFarm());

        $this->upload($product);
    }

    public function preUpdate(Product $product): void
    {
        $this->upload($product);
    }

    private function upload(Product $product): void
    {
        if ($product->getImage() === null || $product->getImage()->getFile() === null) {
            return;
        }

        $filename = Uuid::v4() . $product->getImage()->getFile()->getClientOriginalExtension();

        $product->getImage()->getFile()->move($this->uploadAsboluteDir, $filename);

        $product->getImage()->setPath($this->uploadWebDir . $filename);
    }
}
