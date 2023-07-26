<?php declare(strict_types=1);

namespace AiDescription\Service;

use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;

class ReadingData
{
    private EntityRepository $productRepository;

    public function __construct(EntityRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }
   public function readData(Context $context): EntitySearchResult
   {
       $products = $this->productRepository->search(new Criteria(), $context);
       return $products;
   }
}
