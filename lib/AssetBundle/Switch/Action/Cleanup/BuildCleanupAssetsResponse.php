<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\Action\Cleanup;

use Froq\AssetBundle\Message\CleanupAssetsMessage;
use Froq\AssetBundle\Switch\Controller\Request\CleanupAssetsRequest;
use Froq\AssetBundle\Switch\Controller\Request\CleanupAssetsResponse;
use Froq\AssetBundle\Switch\Enum\AssetResourceOrganizationFolderNames;
use Froq\PortalBundle\Repository\OrganizationRepository;
use Pimcore\Log\ApplicationLogger;
use Pimcore\Model\DataObject\Organization;
use Pimcore\Model\DataObject\Product;
use Pimcore\Model\DataObject\Project;
use Symfony\Component\Messenger\MessageBusInterface;

final class BuildCleanupAssetsResponse
{
    public function __construct(
        private readonly ApplicationLogger $logger,
        private readonly OrganizationRepository $organizationRepository,
        private readonly MessageBusInterface $messageBus,
    ) {
    }

    /**
     * @throws \Exception
     */
    public function __invoke(CleanupAssetsRequest $cleanupAssetsRequest): CleanupAssetsResponse
    {
        $actions = [];

        $organization = $this->organizationRepository->getByOrganizationCode($cleanupAssetsRequest->customerCode);

        if (!($organization instanceof Organization)) {
            throw new \Exception(message: 'Organization does not exists.');
        }

        $batchCount = 1;
        $batchSize = 50;

        $projectsTotalCount = (new Project\Listing())
            ->addConditionParam(
                'o_path = ?',
                $organization->getObjectFolder() . '/' . AssetResourceOrganizationFolderNames::Projects->readable() . '/'
            )
            ->count();

        for ($i = 0; $i < $projectsTotalCount; $i += $batchSize) {
            $offset = ($batchCount - 1) * $batchSize;

            $projects = new Project\Listing();
            $projects->addConditionParam(
                'o_path = ?',
                $organization->getObjectFolder() . '/' . AssetResourceOrganizationFolderNames::Projects->readable() . '/'
            );
            $projects->setLimit($batchSize);
            $projects->setOffset($offset);

            $this->messageBus->dispatch(new CleanupAssetsMessage(projects: $projects->load(), products: []));

            $this->logger->info(message: sprintf('Projects CleanupAssetsMessage dispatched! Batch range: %s - %s', $i, $batchCount));

            $actions[] = sprintf('Projects CleanupAssetsMessage dispatched! Batch range: %s - %s', $i, $batchCount);

            $batchCount++;

            unset($offset);
        }

        $productsTotalCount = (new Product\Listing())
            ->addConditionParam(
                'o_path = ?',
                $organization->getObjectFolder() . '/' . AssetResourceOrganizationFolderNames::Products->readable() . '/'
            )
            ->count();

        for ($i = 0; $i < $productsTotalCount; $i += $batchSize) {
            $offset = ($batchCount - 1) * $batchSize;

            $products = new Product\Listing();
            $products->addConditionParam(
                'o_path = ?',
                $organization->getObjectFolder() . '/' . AssetResourceOrganizationFolderNames::Products->readable() . '/'
            );
            $products->setLimit($batchSize);
            $products->setOffset($offset);

            $this->messageBus->dispatch(new CleanupAssetsMessage(projects: [], products: $products->load()));

            $this->logger->info(message: sprintf('Products CleanupAssetsMessage dispatched! Batch range: %s - %s', $i, $batchCount));

            $actions[] = sprintf('Products CleanupAssetsMessage dispatched! Batch range: %s - %s', $i, $batchCount);

            $batchCount++;

            unset($offset);
        }

        return new CleanupAssetsResponse(
            eventName: $cleanupAssetsRequest->eventName,
            actions: $actions
        );
    }
}
