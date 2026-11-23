<?php

declare(strict_types=1);

namespace MilicaLazic\Migration;

use Contao\CoreBundle\Migration\AbstractMigration;
use Contao\CoreBundle\Migration\MigrationResult;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;

/**
 * Elements that only got a new name (named by their shape, not by their content):
 * the old type becomes the new one, the fields stay as they are.
 *
 * Runs while an element with an old type exists, so it is safe to run again.
 */
class ElementTypesMigration extends AbstractMigration
{
    private const RENAMES = [
        'quote_photo' => 'quote',
    ];

    public function __construct(private readonly Connection $connection)
    {
    }

    public function shouldRun(): bool
    {
        if (!$this->connection->createSchemaManager()->tablesExist(['tl_content'])) {
            return false;
        }

        return (bool) $this->connection->fetchOne(
            'SELECT COUNT(*) FROM tl_content WHERE type IN (?)',
            [array_keys(self::RENAMES)],
            [ArrayParameterType::STRING],
        );
    }

    public function run(): MigrationResult
    {
        $done = [];

        foreach (self::RENAMES as $old => $new) {
            $count = $this->connection->update('tl_content', ['type' => $new], ['type' => $old]);

            if ($count) {
                $done[] = \sprintf('%d × %s → %s', $count, $old, $new);
            }
        }

        return $this->createResult(true, 'Renamed elements: '.implode(', ', $done).'.');
    }
}
