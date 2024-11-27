<?php

declare(strict_types=1);

namespace Froq\AssetBundle\MessageHandler;

use Doctrine\DBAL\Driver\Exception;
use Froq\AssetBundle\Message\CleanupAssetsMessage;
use Pimcore\Db;
use Pimcore\Log\ApplicationLogger;
use Pimcore\Model\DataObject\AssetResource;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

#[AsMessageHandler(fromTransport: 'cleanup_assets', handles: CleanupAssetsMessage::class, method: '__invoke', priority: 10)]
final class CleanupAssetsHandler implements MessageHandlerInterface
{
    public function __construct(
        private readonly ApplicationLogger $applicationLogger,
    ) {
    }

    /**
     * @throws \Exception
     */
    public function __invoke(CleanupAssetsMessage $cleanupAssetsMessage): void
    {
        try {
            foreach ($cleanupAssetsMessage->projects as $project) {
                $statement = Db::get()->prepare('SELECT Assets FROM object_Project WHERE o_id = ?;');

                $statement->bindValue(1, $project->getId(), \PDO::PARAM_INT);

                $relatedAssetResourceIds = array_filter(explode(',', (string) $statement->executeQuery()->fetchOne())); /** @phpstan-ignore-line */
                $previouslyRelatedAssetResources = [];

                foreach ($relatedAssetResourceIds as $assetResourceId) {
                    $assetResource = AssetResource::getById((int) $assetResourceId);

                    if (!($assetResource instanceof AssetResource)) {
                        continue;
                    }

                    if (!$assetResource->hasChildren()) {
                        continue;
                    }

                    if (str_contains(haystack: (string) $assetResource->getName(), needle: (string) $project->getFroq_project_number()) && !empty($project->getFroq_project_number())) {
                        $previouslyRelatedAssetResources[] = $assetResource;
                    }
                }

                $assetResources = array_values(array_filter(array_unique($previouslyRelatedAssetResources)));

                $project->setAssets($assetResources);
                $project->save();
            }

            if (empty($cleanupAssetsMessage->products)) {
                return;
            }

            foreach ($cleanupAssetsMessage->products as $product) {
                $statement = Db::get()->prepare('SELECT Assets FROM object_Product WHERE o_id = ?;');

                $statement->bindValue(1, $product->getId(), \PDO::PARAM_INT);

                $relatedAssetResourceIds = array_filter(explode(',', (string) $statement->executeQuery()->fetchOne())); /** @phpstan-ignore-line */
                $previouslyRelatedAssetResources = [];

                foreach ($relatedAssetResourceIds as $assetResourceId) {
                    $assetResource = AssetResource::getById((int) $assetResourceId);

                    if (!($assetResource instanceof AssetResource)) {
                        continue;
                    }

                    if (!$assetResource->hasChildren()) {
                        continue;
                    }

                    if (str_contains(haystack: (string) $assetResource->getName(), needle: (string) $product->getEAN()) && !empty($product->getEAN())) {
                        $previouslyRelatedAssetResources[] = $assetResource;
                    }

                    if (str_contains(haystack: (string) $assetResource->getName(), needle: (string) $product->getSKU()) && !empty($product->getSKU())) {
                        $previouslyRelatedAssetResources[] = $assetResource;
                    }
                }

                $assetResources = array_values(array_filter(array_unique($previouslyRelatedAssetResources)));

                $product->setAssets($assetResources);
                $product->save();
            }
        } catch (\Exception $exception) {
            $this->applicationLogger->error(message: $exception->getMessage());

            throw new \Exception(message: $exception->getMessage() . 'CleanupAssetsMessage.php line: 115');
        } catch (Exception $exception) {
            throw new \Exception(message: $exception->getMessage() . 'CleanupAssetsMessage.php line: 117');
        }
    }
}
