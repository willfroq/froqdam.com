<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Repository;

use Doctrine\DBAL\Exception;
use Pimcore\Db;

final class MessengerRepository
{
    /**
     * @throws Exception
     * @throws \Doctrine\DBAL\Driver\Exception
     *
     * @return array<string, mixed>
     */
    public function getMessages(int $page, int $limit, string $queueName): array
    {
        $offset = $page === 1 ? $page : ($page - 1) * $limit;

        if (empty($queueName)) {
            $sql = 'SELECT * FROM messenger_messages mm ORDER BY mm.created_at DESC LIMIT ? OFFSET ?';

            $statement = Db::get()->prepare($sql);
            $statement->bindValue(1, $limit, \PDO::PARAM_INT);
            $statement->bindValue(2, $offset, \PDO::PARAM_INT);

            return (array) $statement->executeQuery()?->fetchAllAssociativeIndexed(); /** @phpstan-ignore-line */
        }

        $sql = 'SELECT * FROM messenger_messages mm WHERE mm.queue_name = ? ORDER BY mm.created_at DESC LIMIT ? OFFSET ?';

        $statement = Db::get()->prepare($sql);
        $statement->bindValue(1, $queueName, \PDO::PARAM_STR);
        $statement->bindValue(2, $limit, \PDO::PARAM_INT);
        $statement->bindValue(3, $offset, \PDO::PARAM_INT);

        return (array) $statement->executeQuery()?->fetchAllAssociativeIndexed(); /** @phpstan-ignore-line */
    }

    /**
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws Exception
     */
    public function countAll(): int
    {
        $sql = 'SELECT COUNT(*) FROM messenger_messages';

        $statement = Db::get()->prepare($sql);

        return (int) $statement->executeQuery()->fetchOne(); /** @phpstan-ignore-line */
    }

    /**
     * @throws Exception
     * @throws \Doctrine\DBAL\Driver\Exception
     */
    public function hasSwitchUploadQueued(): bool
    {
        $sql = "SELECT id FROM messenger_messages WHERE queue_name = 'switch_upload'";

        $statement = Db::get()->prepare($sql);

        return (bool) $statement->executeQuery()->fetchOne(); /** @phpstan-ignore-line */
    }
}
