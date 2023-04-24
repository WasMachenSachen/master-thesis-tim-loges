<?php declare(strict_types=1);

namespace Shopware\Core\Content\ProductExport\EventListener;

use League\Flysystem\FilesystemInterface;
use Shopware\Core\Content\ProductExport\Service\ProductExportFileHandlerInterface;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\EntityWriteResult;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\Log\Package;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @deprecated tag:v6.5.0 - reason:becomes-internal - EventSubscribers will become internal in v6.5.0
 */
#[Package('inventory')]
class ProductExportEventListener implements EventSubscriberInterface
{
    private EntityRepositoryInterface $productExportRepository;

    private ProductExportFileHandlerInterface $productExportFileHandler;

    private FilesystemInterface $fileSystem;

    /**
     * @internal
     */
    public function __construct(
        EntityRepositoryInterface $productExportRepository,
        ProductExportFileHandlerInterface $productExportFileHandler,
        FilesystemInterface $fileSystem
    ) {
        $this->productExportRepository = $productExportRepository;
        $this->productExportFileHandler = $productExportFileHandler;
        $this->fileSystem = $fileSystem;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'product_export.written' => 'afterWrite',
        ];
    }

    public function afterWrite(EntityWrittenEvent $event): void
    {
        foreach ($event->getWriteResults() as $writeResult) {
            if (!$this->productExportWritten($writeResult)) {
                continue;
            }

            $primaryKey = $writeResult->getPrimaryKey();
            $primaryKey = \is_array($primaryKey) ? $primaryKey['id'] : $primaryKey;

            $this->productExportRepository->update(
                [
                    [
                        'id' => $primaryKey,
                        'generatedAt' => null,
                    ],
                ],
                $event->getContext()
            );
            $productExportResult = $this->productExportRepository->search(new Criteria([$primaryKey]), $event->getContext());
            if ($productExportResult->getTotal() !== 0) {
                $productExport = $productExportResult->first();

                $filePath = $this->productExportFileHandler->getFilePath($productExport);
                if ($this->fileSystem->has($filePath)) {
                    $this->fileSystem->delete($filePath);
                }
            }
        }
    }

    private function productExportWritten(EntityWriteResult $writeResult): bool
    {
        return $writeResult->getEntityName() === 'product_export'
            && $writeResult->getOperation() !== EntityWriteResult::OPERATION_DELETE
            && !\array_key_exists('generatedAt', $writeResult->getPayload());
    }
}
