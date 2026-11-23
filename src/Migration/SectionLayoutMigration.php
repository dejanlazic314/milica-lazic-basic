<?php

declare(strict_types=1);

namespace MilicaLazic\Migration;

use Contao\CoreBundle\Migration\AbstractMigration;
use Contao\CoreBundle\Migration\MigrationResult;
use Doctrine\DBAL\Connection;

/**
 * One-off: the section layouts are named by their shape. "my_path" and "approach" are
 * "split" (heading left, the rest right); "preparation", "diaspora" and "location" are
 * "columns" (text left, the map or the last nested element right).
 *
 * Runs while a section has an old layout, so it runs once.
 */
class SectionLayoutMigration extends AbstractMigration
{
    private const LAYOUTS = [
        'my_path' => 'split',
        'approach' => 'split',
        'preparation' => 'columns',
        'diaspora' => 'columns',
        'location' => 'columns',
    ];

    public function __construct(private readonly Connection $connection)
    {
    }

    public function shouldRun(): bool
    {
        if (!$this->connection->createSchemaManager()->tablesExist(['tl_content'])) {
            return false;
        }

        // After ItemListMigration: it picks the style of a list by the old layout of its section
        if ($this->connection->fetchOne("SELECT COUNT(*) FROM tl_content WHERE type = 'step'")) {
            return false;
        }

        $columns = array_change_key_case($this->connection->createSchemaManager()->listTableColumns('tl_content'));

        if (isset($columns['sectionrows']) && $this->connection->fetchOne("SELECT COUNT(*) FROM tl_content WHERE type = 'section' AND COALESCE(sectionRows, '') <> ''")) {
            return false;
        }

        return (bool) $this->connection->fetchOne(
            "SELECT COUNT(*) FROM tl_content WHERE type = 'section' AND sectionLayout IN ('my_path', 'approach', 'preparation', 'diaspora', 'location')",
        );
    }

    public function run(): MigrationResult
    {
        $count = 0;

        foreach (self::LAYOUTS as $old => $new) {
            $count += $this->connection->update('tl_content', ['sectionLayout' => $new], ['type' => 'section', 'sectionLayout' => $old]);
        }

        return $this->createResult(true, \sprintf('%d sections got the layout "split" or "columns".', $count));
    }
}
