<?php

declare(strict_types=1);

namespace MilicaLazic\Migration;

use Contao\CoreBundle\Migration\AbstractMigration;
use Contao\CoreBundle\Migration\MigrationResult;
use Doctrine\DBAL\Connection;

/**
 * One-off: "topic_grid" is named by its shape, "page_cards", and its variants become styles:
 * "home" → "large", "overview" → "framed", "other" → "small".
 * Runs while a topic grid exists, so it runs once.
 */
class PageCardsMigration extends AbstractMigration
{
    private const STYLES = ['home' => 'large', 'overview' => 'framed', 'other' => 'small'];

    public function __construct(private readonly Connection $connection)
    {
    }

    public function shouldRun(): bool
    {
        $schema = $this->connection->createSchemaManager();

        if (!$schema->tablesExist(['tl_content'])) {
            return false;
        }

        // The new field must exist first (contao:migrate adds it, then runs this again)
        if (!isset(array_change_key_case($schema->listTableColumns('tl_content'))['pagecardsstyle'])) {
            return false;
        }

        return (bool) $this->connection->fetchOne("SELECT COUNT(*) FROM tl_content WHERE type = 'topic_grid'");
    }

    public function run(): MigrationResult
    {
        $count = 0;

        foreach ($this->connection->fetchAllAssociative("SELECT id, topicsVariant FROM tl_content WHERE type = 'topic_grid'") as $row) {
            $count += $this->connection->update('tl_content', [
                'type' => 'page_cards',
                'pageCardsStyle' => self::STYLES[$row['topicsVariant']] ?? 'large',
                'tstamp' => time(),
            ], ['id' => $row['id']]);
        }

        return $this->createResult(true, \sprintf('%d topic grids became page cards.', $count));
    }
}
