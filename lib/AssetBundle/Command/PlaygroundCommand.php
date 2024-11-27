<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Command;

use League\Csv\CannotInsertRecord;
use League\Csv\Exception;
use League\Csv\Writer;
use Pimcore\Console\AbstractCommand;
use Pimcore\Db;
use Pimcore\Model\Asset;
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
    protected function execute(InputInterface $input, OutputInterface $output)
    {

        return Command::SUCCESS;
    }

    private function makeMissingFilesCsv(): void
    {
        $arIds = [
            458, 472, 2472, 2731, 4017, 4138, 4946, 4988, 11863, 11952, 11968, 11993, 12035, 12064, 12096, 12128, 13653, 17207, 17215, 17217, 17223, 17226, 17240, 17243, 17248, 17251, 17254, 17417, 17419, 17421, 17423, 17484, 18859, 18861, 32277, 46426, 46429, 46432, 46435, 49367, 49381, 50484, 53869, 69311, 69313, 70237, 70242, 100026, 101769, 108835, 111335, 114117, 114118, 114119, 114135, 115771, 115788, 117011, 128134, 128192, 128194, 128206, 128232, 128314, 128354, 128368, 128448, 128589, 128593, 128601, 128631, 128797, 128846, 128866, 128945, 129165, 129170, 129184, 129253, 129329, 129554, 129660, 129792, 132435, 133378, 134587, 134599, 134615, 134627, 134699, 134711, 134724, 134752, 134841, 134843, 134888, 134925, 148792, 148793, 148794, 148795, 148796, 148797, 148798, 148799, 148800, 148801, 148936, 149060, 149263, 149393, 149427, 149483, 149575, 149647, 149700, 149826, 149874, 150027, 150179, 150255, 150317, 150466, 150478, 150491, 150599, 150748, 150843, 150902, 150950, 151005, 151061, 151112, 151211, 151378, 151571, 151631, 151726, 151765, 151771, 151773, 151775, 151777, 151779, 151781, 151806, 151822, 151826, 151912, 151913, 151937, 151938, 151939, 151940, 151941, 151942, 151943, 151944, 151945, 151964, 152055, 152059, 152062, 152076, 152083, 152084, 152085, 152091, 152100, 152102, 152110, 152146, 152315, 152317, 152336, 152337, 152338, 152339, 152359, 152390, 152391, 152392, 152393, 152394, 152395, 152396, 152397, 152398, 152399, 152400, 152401, 152404, 152405, 152438, 152439, 152440, 152444, 152451, 152560, 152601, 152602, 152611, 152612, 152613, 152614, 152615, 152616, 152617, 152618, 152619, 152620, 152622, 152637, 152638, 152675, 152676, 152677, 152678, 152679, 152680, 152681, 152722, 152751, 152752, 152816, 152817, 152818, 152825, 152858, 152880, 152881, 152882, 152925, 152926, 152927, 152928, 152929, 152942, 152945, 152954, 152955, 152972, 152976, 152977, 153016, 153161, 153168, 153169, 153170, 153171, 153172, 153177, 153178, 154485, 154491, 154496, 154503, 154545, 154632, 155562, 155563, 155564, 155571, 157437, 157512, 157787, 158486, 158487, 158488, 158492, 158496, 159201, 159760, 160127, 160231, 160232, 160233, 160234, 160235, 160236, 160237, 160238, 160239, 160240, 160241, 160242, 160243, 160244, 160464, 160465, 160684, 160729, 161050, 161461, 161504, 161556, 161637, 161711, 161872, 162099, 162116, 162145, 162306, 162530, 162701, 163118, 163216, 163221, 163392, 163783, 163788, 163792, 163802, 163809, 163816, 163845, 163847, 163850, 163904, 164063, 164093, 164196, 164273, 164289, 164305, 164309, 164316, 164320, 164324, 164330, 164336, 164337, 164338, 164342, 164354, 164355, 164356, 164360, 164361, 164362, 164366, 164373, 164380, 164411, 164418, 164419, 164420, 164421, 164422, 164423, 164487, 164488, 164489, 164490, 164491, 164492, 164493, 164494, 164495, 164496, 164497, 164498, 164499, 164500, 164501, 164502, 164503, 164504, 164505, 164506, 164507, 164508, 164509, 164510, 164511, 164512, 164513, 164514, 164515, 164516, 164517, 164518, 164519, 164520, 164521, 164522, 164523, 164524, 164525, 164526, 164527, 164528, 164529, 164530, 164531, 164532, 164533, 164534, 164535, 164536, 164537, 164538, 164540, 164566, 164753, 164787, 169489, 171650, 175905, 176119, 176944, 178505, 184073, 185957, 188670, 195060, 195064, 195068, 195070, 195102, 195117, 195118, 195126, 195144, 195149, 195153, 195155, 195171, 195341, 197972, 201597, 203361, 209935, 211333, 216179, 218326, 220145, 221646, 223927, 224507, 229208, 229832, 229883, 230244, 230713, 231075, 231691, 232109, 232249, 232305, 232306, 232307, 232308, 232309, 232310, 232311, 232312, 232313, 232557, 232750, 232952, 233473, 233884, 234169, 234229, 234261, 234700, 234877, 234878, 234879, 234880, 234881, 234882, 234883, 234884, 234885, 234886, 234887, 234888, 234889, 234890, 234891, 234892, 234893, 234894, 234982, 234989, 235010, 235052, 235067, 235068, 235081, 235135, 235161, 235318, 235333, 235353, 235359, 235380, 235397, 235405, 235448, 235488, 235518, 235536, 235625, 235678, 235731, 235738, 235739, 235740, 235741, 235742, 235743, 235744, 235745, 235746, 235747, 235748, 235749, 235750, 235751, 235752, 235753, 235754, 235755, 235756, 235757, 235758, 235759, 235760, 236023, 238052, 238153, 238173, 238175, 238177, 238179, 238181, 238310, 238313, 238374, 238428, 238433, 238691, 247886, 247892, 248406, 248519, 248616, 249349, 249355, 249367, 249375, 249379, 249419, 249439, 249601, 249649, 249708, 249744, 249993, 250452, 250492, 250502, 250788, 252321, 252431, 256571, 259166, 259587, 259710, 269041, 269049, 270384, 270508, 271084, 274527, 274584, 275663, 276608, 276736, 283830, 284072, 284075, 286594, 287150, 287153, 288491, 289100, 289101, 290071, 290072, 290073, 290074, 290075, 290077, 290078, 290736, 290800, 291297, 291309, 291312, 291985, 292962, 294426, 295820, 295852, 295855, 295864, 295873, 295894, 295898, 295908, 296499, 297995, 298061, 298279, 298349, 298357, 298540, 298914, 299711, 299747, 299917, 299930, 300282, 300845, 300884, 300926, 300931, 301212, 301294, 301338, 302022, 302503, 303314, 303958, 304237, 304345, 304499, 304693, 306597, 307449, 309475, 312606, 315095, 315098, 315101, 315103, 315106, 315108, 319242, 321073, 325582, 327954, 329860, 333485, 337470, 338020, 338467, 339474, 340383, 341757, 342086, 343034, 344001, 344952, 346686, 346688, 346690, 349499, 349730, 350205, 351091, 351093, 351294, 352234, 352749, 353121, 353622, 355897, 356691, 358984, 359519, 360940, 363589, 363826, 367163, 367167, 367693, 367694, 368292, 368509, 371021, 371023, 371025, 371027, 371029, 371031, 371033, 371218, 371607, 373475, 373656, 374451, 374453, 386812, 387985, 387988, 390056, 390373, 390598, 390866, 390868, 390873, 390874, 390878, 390879, 390883, 391011, 391166, 391947, 392289, 392293, 392519, 392563, 392668, 392672, 392676, 392685, 396968, 397018, 397021, 397161, 401746, 401757, 401768, 401777, 402543, 402750, 402880, 402905, 403244, 403591, 404259, 404261, 404263, 404265, 404267, 404269, 404271, 404273, 404275, 404277, 404279, 404281, 404283, 404285, 404287, 404289, 404291, 404293, 404295, 404297, 404299, 404301, 404303, 404305, 405207, 405444, 405445, 405449, 405608, 405610, 405612, 405614, 405616, 405618, 405620, 405622, 405624, 405626, 405628, 405630, 405632, 405634, 405969, 405970, 406000, 406001, 406945, 406947, 406949, 406951, 406973, 406986, 406988, 407017, 407019, 407065, 407170, 407802, 407804, 407806, 407808, 407810, 407830, 407862, 407863, 407864, 407865, 407867, 407868, 407869, 407871, 407872, 407873, 407875, 407876, 407877, 407878, 407879, 407880, 407881, 407882, 407884, 407885, 407886, 407887, 407889, 407892, 407894, 407896, 408803, 410448, 410449, 410450, 410451, 410452, 410453, 410454, 410457, 410458, 410460, 410461, 410462, 410463, 410464, 410465, 410466, 410467, 410470, 410471, 410472, 410473, 410474, 410475, 410476, 410477, 410478, 410481, 410482, 410483, 410484, 410485, 410486, 410487, 410488, 410489, 410491, 410493, 410494, 410495, 410496, 410497, 410498, 410499, 410500, 410501, 410504, 410505, 410506, 410507, 410580, 411123, 411231, 411730, 411731, 411734, 412024, 412715
        ];

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
