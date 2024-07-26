<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Command;

use Aws\S3\S3Client;
use Elastica\Client;
use Elastica\Query\MatchAll;
use Elastica\Search;
use Pimcore\Console\AbstractCommand;
use Pimcore\Log\ApplicationLogger;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

#[AsCommand(
    name: 'froq:dump-and-upload-es',
    description: 'Make a json dump of es, zip it then upload to s3.',
    aliases: ['froq:dump-and-upload-es'],
    hidden: false
)]
final class DumpESThenUploadToS3Command extends AbstractCommand
{
    public function __construct(private readonly ApplicationLogger $logger)
    {
        parent::__construct();
    }

    /**
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $s3Client = new S3Client([
            'version' => 'latest',
            'region' => $_ENV['AWS_S3_BUCKET_REGION'] ?? '',
            'credentials' => [
                'key' => $_ENV['AWS_S3_ACCESS_ID'] ?? '',
                'secret' => $_ENV['AWS_S3_ACCESS_SECRET'] ?? '',
            ],
        ]);

        $elasticaClient = new Client([
            'host' => $_ENV['ES_HOST'] ?? '',
            'port' => $_ENV['ES_PORT'] ?? '',
        ]);

        $filesystem = new Filesystem();
        $indexName = $_ENV['ES_INDEX_NAME'] ?? '';

        $bucketName = $_ENV['AWS_S3_BUCKET_NAME_ES_DUMP'] ?? '';
        $esDumpPrefix = $_ENV['ES_DUMP_PREFIX'] ?? '';

        $search = new Search($elasticaClient);
        $search->addIndexByName($indexName);

        $query = new MatchAll();
        $search->setQuery($query);

        try {
            $resultSet = $search->search();

            $data = [];
            foreach ($resultSet as $result) {
                $data[] = $result->getSource();
            }
            $jsonData = json_encode($data);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception(message: 'JSON encoding error: ' . json_last_error_msg());
            }

            $filename = $indexName . '-' . date('Y-m-d');

            $localTempDirectory = sys_get_temp_dir()."/$esDumpPrefix/";

            $jsonFilePath = "$localTempDirectory/$filename.json";

            if (!is_dir($localTempDirectory)) {
                mkdir($localTempDirectory, 0777, true);
            }

            $contents = file_put_contents($jsonFilePath, $jsonData);

            if ($contents === false) {
                throw new \Exception(message: 'Error putting file contents.');
            }

            $zipFilePath = "$localTempDirectory/$filename-dump.zip";

            $objectKey = basename($zipFilePath);

            $zipProcess = Process::fromShellCommandline("zip $zipFilePath $jsonFilePath");
            $zipProcess->setTimeout(1200);
            $zipProcess->run();

            if (!$zipProcess->isSuccessful()) {
                $message = 'Failed to zip the dump file: ' . $zipProcess->getErrorOutput();
                $this->logger->critical($message);

                if ($filesystem->exists([$zipFilePath])) {
                    $filesystem->remove([$zipFilePath]);
                }

                throw new \RuntimeException($message);
            }

            $s3Client->putObject([
                'Bucket' => $bucketName,
                'Key'    => $objectKey,
                'Body' => fopen($zipFilePath, 'rb'),
            ]);

            if (unlink($jsonFilePath) === false) {
                throw new \Exception(message: 'Error deleting json file.');
            }

            if (unlink($zipFilePath) === false) {
                throw new \Exception(message: 'Error deleting zip file.');
            }

            $filesystem->remove([$jsonFilePath, $zipFilePath]);

            $output->writeln('ES data successfully dumped.');
        } catch (\Exception $exception) {
            $this->logger->critical($exception->getMessage());

            throw new \Exception(message: $exception->getMessage());
        }

        return Command::SUCCESS;
    }
}
