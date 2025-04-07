<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Command;

use Aws\S3\S3Client;
use Pimcore\Console\AbstractCommand;
use Pimcore\Log\ApplicationLogger;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

#[AsCommand(
    name: 'froq:dump-and-upload',
    description: 'Make a dump sql, zip it then upload to s3.',
    aliases: ['froq:dump-and-upload'],
    hidden: false
)]
final class DumpSqlThenUploadToS3Command extends AbstractCommand
{
    public function __construct(private readonly ApplicationLogger $logger)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $filesystem = new Filesystem();
        $dbName = $_ENV['FROQ_DB_NAME'] ?? '';

        $s3Client = new S3Client([
            'version' => 'latest',
            'region' => $_ENV['AWS_S3_BUCKET_REGION'] ?? '',
            'credentials' => [
                'key' => $_ENV['AWS_S3_ACCESS_ID'] ?? '',
                'secret' => $_ENV['AWS_S3_ACCESS_SECRET'] ?? '',
            ],
        ]);

        $bucketName = $_ENV['AWS_S3_BUCKET_NAME_DUMP_SQL'] ?? '';

        $localTempDirectory = sys_get_temp_dir();
        $dumpFilePath = $localTempDirectory . '/' . $dbName . '-' . date('Y-m-d') . '-dump.sql';
        $zipFilePath = $localTempDirectory . '/' . $dbName . '-' . date('Y-m-d') . '-dump.zip';

        try {
            $process = Process::fromShellCommandline("yousqldump > $dumpFilePath");
            $process->setTimeout(1800);
            $process->run();

            if (!$process->isSuccessful()) {
                $message = 'Failed to dump SQL: ' . $process->getErrorOutput();
                $this->logger->critical($message);
                throw new \RuntimeException($message);
            }

            $zipProcess = Process::fromShellCommandline("zip $zipFilePath $dumpFilePath");
            $zipProcess->setTimeout(1800);
            $zipProcess->run();

            if (!$zipProcess->isSuccessful()) {
                $message = 'Failed to zip the dump file: ' . $zipProcess->getErrorOutput();
                $this->logger->critical($message);
                throw new \RuntimeException($message);
            }

            $objectKey = basename($zipFilePath);

            $s3Client->putObject([
                'Bucket' => $bucketName,
                'Key' => $objectKey,
                'Body' => fopen($zipFilePath, 'rb'),
            ]);

            $output->writeln('SQL dump zipped and uploaded to S3 successfully.');

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->logger->critical('An error occurred: ' . $e->getMessage());
            throw $e;
        } finally {
            if ($filesystem->exists([$dumpFilePath])) {
                $filesystem->remove([$dumpFilePath]);
            }
            if ($filesystem->exists([$zipFilePath])) {
                $filesystem->remove([$zipFilePath]);
            }
        }
    }
}
