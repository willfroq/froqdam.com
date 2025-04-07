<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Command;

use Froq\AssetBundle\Model\DataObject\AssetDocument;
use League\Csv\CannotInsertRecord;
use League\Csv\Exception;
use League\Csv\Writer;
use Pimcore\Console\AbstractCommand;
use Pimcore\Db;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\AssetResource;
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
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        return Command::SUCCESS;
    }

    //    private function getArMetadata()
    //    {
    //        $assetResource = AssetResource::getById(17363);
    //
    //        dd($assetResource->getMetadata()->getItems()[0]->getMetadataKey());
    //    }
    //
    //    private function exportingAssetFromS3()
    //    {
    //        $stream = fopen('https://dam-assets-s3.s3.eu-central-1.amazonaws.com/local-assets/S3/Customer/AAA/pmfNa0HS5I/1176163_284_60_8718265422827_Stengels_Naturelxxxxxxxxx.pdf/1/1176163_284_60_8718265422827_Stengels_Naturelxxxxxxxxx.pdf', 'r');
    //
    //        $asset = new AssetDocument();
    //        $asset->setFilename('1176163_284_60_8718265422827_Stengels_Naturelxxxxxxxxx.pdf');
    //        $asset->setParentId(9488);
    //        $asset->setData((string) stream_get_contents($stream));
    //        $asset->save();
    //    }
    //
    //    /**
    //     * @throws CannotInsertRecord
    //     * @throws Exception
    //     */
    //    private function makeMissingFilesInProdCsv(): void
    //    {
    //        $missingArIds = [
    //            458, 472, 2472, 2731, 4017, 4138, 4946, 4988, 11863, 13653, 17207, 17215, 17240, 17243, 17248, 17251, 17254, 17417, 17419, 17421, 17423, 18859, 18861, 32277, 46426, 46429, 46432, 46435, 49367, 49381, 69311, 69313, 70237, 70242, 108835, 117011, 128134, 128192, 128194, 128206, 128232, 128314, 128354, 128368, 128448, 128589, 128593, 128601, 128631, 128797, 128846, 128866, 128945, 129165, 129170, 129184, 129253, 129329, 129554, 129660, 129792, 132435, 134587, 134599, 134615, 134627, 134699, 134711, 134724, 134752, 134841, 134843, 134888, 134925, 149263, 149575, 150255, 151765, 151771, 151773, 151775, 151777, 151779, 151781, 152100, 152102, 152146, 152751, 154485, 154632, 160684, 160729, 161050, 161461, 161504, 161556, 161711, 161872, 162116, 162145, 162306, 162530, 162701, 163118, 163216, 163392, 163845, 163847, 163850, 163904, 164063, 164093, 164196, 164289, 176944, 178505, 195060, 195064, 195070, 195102, 195126, 195144, 195149, 195153, 195155, 236023, 238173, 238175, 238177, 238179, 238181, 238374, 247886, 247892, 248406, 248519, 248616, 249349, 249355, 249367, 249375, 249379, 249419, 249439, 249601, 249649, 249708, 249744, 250492, 250502, 252321, 271084, 274527, 274584, 284075, 289100, 292962, 294426, 295852, 295855, 295864, 295873, 295894, 295898, 295908, 297995, 298061, 298279, 298349, 298357, 298540, 298914, 299711, 299747, 299917, 299930, 300282, 300845, 300884, 301212, 301294, 301338, 302022, 302503, 303314, 303958, 304237, 304345, 304499, 304693, 306597, 307449, 309475, 312606, 315095, 315098, 315101, 315103, 315106, 315108, 321073, 327954, 329860, 351091, 351093, 353121, 358984, 360940, 368509, 371021, 371023, 371025, 371027, 371029, 371031, 371033, 371218, 373475, 373656, 374451, 374453, 386812, 387988, 390373, 390598, 391011, 392563, 397018, 397021, 402543, 403591, 404259, 404261, 404263, 404265, 404267, 404269, 404271, 404273, 404275, 404277, 404279, 404281, 404283, 404285, 404287, 404289, 404291, 404293, 404295, 404297, 404299, 404301, 404303, 404305, 405207, 405608, 405610, 405612, 405614, 405616, 405618, 405620, 405622, 405624, 405626, 405628, 405630, 405632, 405634, 406945, 406947, 406949, 406951, 406973, 406986, 406988, 407017, 407019, 407065, 407170, 407802, 407804, 407806, 407808, 407810, 407830, 407892, 407894, 407896, 412024, 413943, 416738, 418603, 427360, 427410, 427415, 427444, 431198, 437720, 438885, 440032, 440050
    //        ];
    //
    //        $items = [
    //            ['assetResourceId', 'filename']
    //        ];
    //
    //        foreach ($missingArIds as $missingArId) {
    //            $ar = AssetResource::getById($missingArId);
    //
    //            if (!($ar instanceof AssetResource)) {
    //                continue;
    //            }
    //
    //            $latestAr = null;
    //
    //            $parentAr = $ar->getParent();
    //
    //            $latestArId = null;
    //
    //            if ($parentAr instanceof AssetResource) {
    //                $childrenIds = array_map(fn (AssetResource $assetResource) => $assetResource->getId(), $parentAr->getChildren());
    //
    //                $latestArId = (int) max($childrenIds);
    //            }
    //
    //            if ($latestArId === null) {
    //                continue;
    //            }
    //
    //            $latestAr = AssetResource::getById($latestArId);
    //
    //            if (!($latestAr instanceof AssetResource)) {
    //                continue;
    //            }
    //
    //            $items[] = [
    //                $latestAr->getId(),
    //                $latestAr->getFullPath(),
    //            ];
    //        }
    //
    //        $filename = 'missingFilesInDamProd.csv';
    //
    //        $csvWriter = Writer::createFromString('');
    //
    //        $csvWriter->insertAll($items);
    //
    //        $file = fopen($filename, 'w');
    //
    //        if (is_resource($file)) {
    //            fwrite($file, $csvWriter->toString());
    //            fclose($file);
    //        }
    //    }
    //
    //    private function zeroBytes()
    //    {
    //        $assets = Asset::getList()->load();
    //
    //        $assetsZeroByte = [];
    //
    //        foreach ($assets as $asset) {
    //            if ((int) $asset->getFileSize() !== 0) {
    //                continue;
    //            }
    //
    //            $assetsZeroByte[] = $asset->getId();
    //        }
    //    }

    /**
     * @throws CannotInsertRecord
     * @throws Exception
     */
    //        private function finalResult(): void
    //        {
    //            $pimcore = array_values(array_filter(array_unique($this->makeKeyValueOfARIdFilename())));
    //
    //            // read from missingFilesInDamProd.csv
    //            $dir = '/Users/w.delrosario/Sites/froq/froq-pimcore/misc/woodwing-1_30-08-2024/';
    //            $filenames = scandir($dir);
    //
    //            $resultAllFilesWithPathAndId = [];
    //
    //            foreach ($filenames as $filename) {
    //                if ($filename === '.' || $filename === '..') {
    //                    continue;
    //                }
    //
    //                $inputFile = "$dir$filename";
    //
    //                $columnFilename = 0;
    //                $columnPath = 1;
    //                $columnId = 2;
    //
    //                $columnValues = [];
    //
    //                if (($handle = fopen($inputFile, 'r')) !== false) {
    //                    while (($data = fgetcsv($handle, null, ',')) !== false) {
    //                        if ($data[$columnFilename] === 'filename') {
    //                            continue;
    //                        }
    //
    //                        if (isset($data[$columnFilename])) {
    //                            $columnValues[] = [
    //                                'filename' => $data[$columnFilename],
    //                                'path' => $data[$columnPath] ?? '',
    //                                'id' => $data[$columnId] ?? '',
    //                            ];
    //                        }
    //                    }
    //
    //                    fclose($handle);
    //                }
    //
    //                if (empty($resultAllFilesWithPathAndId)) {
    //                    $resultAllFilesWithPathAndId = $columnValues;
    //                }
    //
    //                if (!empty($resultAllFilesWithPathAndId)) {
    //                    $resultAllFilesWithPathAndId = array_merge($resultAllFilesWithPathAndId, $columnValues);
    //                }
    //            }
    //
    //            $filename = 'missingFilesInDamProdComparedToWoodwing.csv';
    //
    //            $items = [
    //                ['filename', 'path', 'id']
    //            ];
    //
    //            $alreadyReadFilenames = [];
    //
    //            foreach ($resultAllFilesWithPathAndId as $row) {
    //                if (in_array(needle: $row['filename'], haystack: $pimcore)) {
    //                    continue;
    //                }
    //
    //                if (in_array(needle: $row['filename'], haystack: $alreadyReadFilenames)) {
    //                    continue;
    //                }
    //
    //                if (preg_match('/^[^<>:"\/\\|?*\x00-\x1F]+\.[a-zA-Z0-9]+$/', $row['filename']) !== 1) {
    //                    continue;
    //                }
    //
    //                $items[] = [
    //                    $row['filename'],
    //                    $row['path'],
    //                    $row['id'],
    //                ];
    //
    //                $alreadyReadFilenames[] = $row['filename'];
    //            }
    //
    //            $csvWriter = Writer::createFromString('');
    //
    //            $csvWriter->insertAll($items);
    //
    //            $file = fopen($filename, 'w');
    //
    //            if (is_resource($file)) {
    //                fwrite($file, $csvWriter->toString());
    //                fclose($file);
    //            }
    //        }

    //    /**
    //     * @throws CannotInsertRecord
    //     * @throws Exception
    //     */
    //    private function comparePimcoreAndWoodWingThenWriteToCsv(array $woodwing): void
    //    {
    //        $pimcore = array_values(array_filter(array_unique($this->makeKeyValueOfARIdFilename())));
    //
    //        $filenames = array_diff($woodwing, $pimcore);
    //
    //        $items = [['filename']];
    //
    //        foreach ($filenames as $filename) {
    //            $items[] = [$filename];
    //        }
    //
    //        $filename = 'missingFilesInDamProd.csv';
    //
    //        $csvWriter = Writer::createFromString('');
    //
    //        $csvWriter->insertAll($items);
    //
    //        $file = fopen($filename, 'w');
    //
    //        if (is_resource($file)) {
    //            fwrite($file, $csvWriter->toString());
    //            fclose($file);
    //        }
    //    }
    //
    //    private function makeListOfFilenamesFromWoodwing(): array
    //    {
    //        $dir = '/Users/w.delrosario/Sites/froq/froq-pimcore/misc/woodwing-1_30-08-2024/';
    //        $filenames = scandir($dir);
    //
    //        $resultAllFiles = [];
    //
    //        foreach ($filenames as $filename) {
    //            if ($filename === '.' || $filename === '..') {
    //                continue;
    //            }
    //
    //            $inputFile = "$dir$filename";
    //
    //            $columnIndex = 0;
    //
    //            $columnValues = [];
    //
    //            if (($handle = fopen($inputFile, 'r')) !== false) {
    //                while (($data = fgetcsv($handle, null, ',')) !== false) {
    //                    if ($data[$columnIndex] === 'filename') {
    //                        continue;
    //                    }
    //
    //                    if (isset($data[$columnIndex])) {
    //                        $columnValues[] = $data[$columnIndex];
    //                    }
    //                }
    //
    //                fclose($handle);
    //            }
    //
    //            $resultAllFiles = array_values(array_filter(array_unique([...$resultAllFiles, ...$columnValues])));
    //        }
    //
    //        return $resultAllFiles;
    //    }
    //
    //    private function makeHeinekenCountries(): array
    //    {
    //        $dir = '/Users/w.delrosario/Sites/froq/froq-pimcore/misc/heineken/';
    //        $filenames = scandir($dir);
    //
    //        $resultAllFiles = [];
    //
    //        foreach ($filenames as $filename) {
    //            if ($filename === '.' || $filename === '..') {
    //                continue;
    //            }
    //
    //            $inputFile = "$dir$filename";
    //
    //            $columnIndex = 0;
    //
    //            $columnValues = [];
    //
    //            if (($handle = fopen($inputFile, 'r')) !== false) {
    //                while (($data = fgetcsv($handle, null, ',')) !== false) {
    //                    if ($data[$columnIndex] === 'filename') {
    //                        continue;
    //                    }
    //
    //                    if (isset($data[$columnIndex])) {
    //                        $columnValues[] = $data[$columnIndex];
    //                    }
    //                }
    //
    //                fclose($handle);
    //            }
    //
    //            $resultAllFiles = array_values(array_filter(array_unique([...$resultAllFiles, ...$columnValues])));
    //        }
    //
    //        return $resultAllFiles;
    //    }
    //
    //    private function makeKeyValueOfARIdFilename(): array
    //    {
    //        $sql = "
    //            SELECT o_id, o_key
    //            FROM objects
    //            WHERE o_className='AssetResource'
    //              AND (
    //                RIGHT(o_key, 4) = '.jpg' OR
    //                RIGHT(o_key, 4) = '.png' OR
    //                RIGHT(o_key, 4) = '.pdf'
    //                );
    //        ";
    //
    //        return Db::get()->prepare($sql)->executeQuery()->fetchAllKeyValue();
    //    }
    //
    //    private function makeKeyValueOfAssetIdFilename(): array
    //    {
    //        $sql = "
    //            SELECT id, filename
    //            FROM assets
    //            WHERE type='document'
    //              AND (
    //                RIGHT(filename, 4) = '.jpg' OR
    //                RIGHT(filename, 4) = '.png' OR
    //                RIGHT(filename, 4) = '.pdf'
    //                );
    //        ";
    //
    //        return Db::get()->prepare($sql)->executeQuery()->fetchAllKeyValue();
    //    }
}
