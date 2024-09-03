<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Command;

use League\Csv\CannotInsertRecord;
use League\Csv\Exception;
use League\Csv\Writer;
use Pimcore\Console\AbstractCommand;
use Pimcore\Db;
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
        $heinekenCountries = $this->makeHeinekenCountries();

        $heinekenFilenamesWithoutExtension = [];
        foreach ($heinekenCountries as $heinekenCountry) {
            $newFileName = pathinfo($heinekenCountry, PATHINFO_FILENAME);

            $heinekenFilenamesWithoutExtension[] = $newFileName;
        }

        $presentInProd = [];
        $missingInProd = [];

        $pimcoreFilenames = array_values(array_filter(array_unique($this->makeKeyValueOfARIdFilename())));

        foreach ($pimcoreFilenames as $pimcoreFilename) {
            foreach ($heinekenFilenamesWithoutExtension as $heinekenFilename) {
                if (str_starts_with(haystack: $pimcoreFilename, needle: $heinekenFilename)) {
                    $presentInProd[] = $heinekenFilename;
                }
            }
        }

        foreach ($heinekenCountries as $heinekenCountry) {
            foreach ($presentInProd as $presentFilename) {
                if (!str_starts_with(haystack: $heinekenCountry, needle: $presentFilename)) {
                    $missingInProd[] = $heinekenCountry;
                }
            }
        }

        dd(array_values(array_filter(array_unique($missingInProd))));

        parent::__construct();
    }

    /**
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        return Command::SUCCESS;
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
