<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Command;

use League\Csv\CannotInsertRecord;
use League\Csv\Exception;
use League\Csv\Writer;
use Pimcore\Console\AbstractCommand;
use Pimcore\Db;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\AssetResource;
use Pimcore\Model\DataObject\Project;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'test:anything',
    description: 'hi.',
    aliases: ['test:anything'],
    hidden: false
)]
class PlaygroundCommand extends AbstractCommand
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @throws \Exception
     * @throws \Doctrine\DBAL\Driver\Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $projectByProjectCode = (new Project\Listing())
            ->addConditionParam('Code = ?', 5621354)
            ->addConditionParam('o_path = ?', '/Customers/Action/Projects/')
            ->current();

        $statement = Db::get()->prepare('SELECT Assets FROM object_Project WHERE o_id = ?;');

        $statement->bindValue(1, $projectByProjectCode->getId(), \PDO::PARAM_INT);

        $relatedAssetResourceIds = array_filter(explode(',', $statement->executeQuery()->fetchOne()));

        $previouslyRelatedAssetResources = [];

        foreach ($relatedAssetResourceIds as $assetResourceId) {
            $assetResource = AssetResource::getById((int) $assetResourceId);

            if (!($assetResource instanceof AssetResource)) {
                continue;
            }

            if (!$assetResource->hasChildren()) {
                continue;
            }

            if (!str_contains(haystack: (string) $assetResource->getName(), needle: '5621354')) {
                continue;
            }

            $previouslyRelatedAssetResources[] = $assetResource;
        }

        $assetResources = array_values(array_filter(array_unique([...$previouslyRelatedAssetResources])));

        return Command::SUCCESS;
    }

    private function zeroBytes()
    {
        $assets = Asset::getList()->load();

        $assetsZeroByte = [];

        foreach ($assets as $asset) {
            if ((int) $asset->getFileSize() !== 0) {
                continue;
            }

            $assetsZeroByte[] = $asset->getId();
        }
    }

    private function finalResult(): void
    {
        $pimcore = array_values(array_filter(array_unique($this->makeKeyValueOfARIdFilename())));

        // read from missingFilesInDamProd.csv
        $dir = '/Users/w.delrosario/Sites/froq/froq-pimcore/misc/woodwing-1_30-08-2024/';
        $filenames = scandir($dir);

        $resultAllFilesWithPathAndId = [];

        foreach ($filenames as $filename) {
            if ($filename === '.' || $filename === '..') {
                continue;
            }

            $inputFile = "$dir$filename";

            $columnFilename = 0;
            $columnPath = 1;
            $columnId = 2;

            $columnValues = [];

            if (($handle = fopen($inputFile, 'r')) !== false) {
                while (($data = fgetcsv($handle, null, ',')) !== false) {
                    if ($data[$columnFilename] === 'filename') {
                        continue;
                    }

                    if (isset($data[$columnFilename])) {
                        $columnValues[] = [
                            'filename' => $data[$columnFilename],
                            'path' => $data[$columnPath] ?? '',
                            'id' => $data[$columnId] ?? '',
                        ];
                    }
                }

                fclose($handle);
            }

            if (empty($resultAllFilesWithPathAndId)) {
                $resultAllFilesWithPathAndId = $columnValues;
            }

            if (!empty($resultAllFilesWithPathAndId)) {
                $resultAllFilesWithPathAndId = array_merge($resultAllFilesWithPathAndId, $columnValues);
            }
        }

        $filename = 'missingFilesInDamProdWithPathAndId.csv';

        $items = [
            ['filename', 'path', 'id']
        ];

        $alreadyReadFilenames = [];

        foreach ($resultAllFilesWithPathAndId as $row) {
            if (in_array(needle: $row['filename'], haystack: $pimcore)) {
                continue;
            }

            if (in_array(needle: $row['filename'], haystack: $alreadyReadFilenames)) {
                continue;
            }

            $items[] = [
                $row['filename'],
                $row['path'],
                $row['id'],
            ];

            $alreadyReadFilenames[] = $row['filename'];
        }

        $csvWriter = Writer::createFromString('');

        $csvWriter->insertAll($items);

        $file = fopen($filename, 'w');

        if (is_resource($file)) {
            fwrite($file, $csvWriter->toString());
            fclose($file);
        }
    }

    /**
     * @throws CannotInsertRecord
     * @throws Exception
     */
    private function comparePimcoreAndWoodWingThenWriteToCsv(array $woodwing): void
    {
        $pimcore = array_values(array_filter(array_unique($this->makeKeyValueOfARIdFilename())));

        $filenames = array_diff($woodwing, $pimcore);

        $items = [['filename']];

        foreach ($filenames as $filename) {
            $items[] = [$filename];
        }

        $filename = 'missingFilesInDamProd.csv';

        $csvWriter = Writer::createFromString('');

        $csvWriter->insertAll($items);

        $file = fopen($filename, 'w');

        if (is_resource($file)) {
            fwrite($file, $csvWriter->toString());
            fclose($file);
        }
    }

    private function makeListOfFilenamesFromWoodwing(): array
    {
        $dir = '/Users/w.delrosario/Sites/froq/froq-pimcore/misc/woodwing-1_30-08-2024/';
        $filenames = scandir($dir);

        $resultAllFiles = [];

        foreach ($filenames as $filename) {
            if ($filename === '.' || $filename === '..') {
                continue;
            }

            $inputFile = "$dir$filename";

            $columnIndex = 0;

            $columnValues = [];

            if (($handle = fopen($inputFile, 'r')) !== false) {
                while (($data = fgetcsv($handle, null, ',')) !== false) {
                    if ($data[$columnIndex] === 'filename') {
                        continue;
                    }

                    if (isset($data[$columnIndex])) {
                        $columnValues[] = $data[$columnIndex];
                    }
                }

                fclose($handle);
            }

            $resultAllFiles = array_values(array_filter(array_unique([...$resultAllFiles, ...$columnValues])));
        }

        return $resultAllFiles;
    }

    private function makeHeinekenCountries(): array
    {
        $dir = '/Users/w.delrosario/Sites/froq/froq-pimcore/misc/heineken/';
        $filenames = scandir($dir);

        $resultAllFiles = [];

        foreach ($filenames as $filename) {
            if ($filename === '.' || $filename === '..') {
                continue;
            }

            $inputFile = "$dir$filename";

            $columnIndex = 0;

            $columnValues = [];

            if (($handle = fopen($inputFile, 'r')) !== false) {
                while (($data = fgetcsv($handle, null, ',')) !== false) {
                    if ($data[$columnIndex] === 'filename') {
                        continue;
                    }

                    if (isset($data[$columnIndex])) {
                        $columnValues[] = $data[$columnIndex];
                    }
                }

                fclose($handle);
            }

            $resultAllFiles = array_values(array_filter(array_unique([...$resultAllFiles, ...$columnValues])));
        }

        return $resultAllFiles;
    }

    private function makeKeyValueOfARIdFilename(): array
    {
        $sql = "
            SELECT o_id, o_key
            FROM objects
            WHERE o_className='AssetResource'
              AND (
                RIGHT(o_key, 4) = '.jpg' OR
                RIGHT(o_key, 4) = '.png' OR
                RIGHT(o_key, 4) = '.pdf'
                );
        ";

        return Db::get()->prepare($sql)->executeQuery()->fetchAllKeyValue();
    }

    private function makeKeyValueOfAssetIdFilename(): array
    {
        $sql = "
            SELECT id, filename
            FROM assets
            WHERE type='document'
              AND (
                RIGHT(filename, 4) = '.jpg' OR
                RIGHT(filename, 4) = '.png' OR
                RIGHT(filename, 4) = '.pdf'
                );
        ";

        return Db::get()->prepare($sql)->executeQuery()->fetchAllKeyValue();
    }
}
