<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Command;

use Froq\AssetBundle\Manager\Organization\OrganizationToAssetResourcesConnector;
use Pimcore\Console\AbstractCommand;
use Pimcore\Log\ApplicationLogger;
use Pimcore\Model\DataObject\Organization;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateOrganizationAssetResourcesCommand extends AbstractCommand
{
    public function __construct(private readonly OrganizationToAssetResourcesConnector $connector, private readonly ApplicationLogger $logger)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('froq:asset:connect-organization-to-asset-resources')
            ->setDescription('Link all Organizations with their respective AssetResources');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $organizations = Organization::getList();
        $failed = 0;

        foreach ($organizations as $organization) {
            if (!($organization instanceof Organization)) {
                continue;
            }

            try {
                $this->connector->linkOrganizationToAssetResources($organization);
            } catch (\Exception $ex) {
                $failed++;
                $this->output->writeln($ex->getMessage());
                $this->logger->critical($ex->getMessage());
            }
        }
        $this->output->writeln('Done!');

        return $failed ? self::FAILURE : self::SUCCESS;
    }
}
