<?php

declare(strict_types=1);

namespace MilicaLazic\Migration;

use Contao\CoreBundle\Migration\AbstractMigration;
use Contao\CoreBundle\Migration\MigrationResult;
use Doctrine\DBAL\Connection;

/**
 * One-off: the elements "hero" (home page) and "page_intro" (subpages) are one element
 * "hero" with a layout and a width.
 *
 * - the old hero gets the layout "background";
 * - a page intro becomes a hero: variant "image" → layout "split", the others → "plain";
 *   "narrow" and "wide" become the width. Its nested elements stay where they are.
 *
 * Runs while a page intro exists or a hero has no layout yet, so it runs once and
 * never changes an element that was edited afterwards. Only type, layout and width
 * change; the texts, links and images are the same fields as before.
 */
class HeroMigration extends AbstractMigration
{
    public function __construct(private readonly Connection $connection)
    {
    }

    public function shouldRun(): bool
    {
        $schema = $this->connection->createSchemaManager();

        if (!$schema->tablesExist(['tl_content'])) {
            return false;
        }

        $columns = array_change_key_case($schema->listTableColumns('tl_content'));

        // The new fields must exist first (contao:migrate adds them, then runs this again)
        if (!isset($columns['herolayout'], $columns['herowidth'])) {
            return false;
        }

        return (bool) $this->connection->fetchOne("SELECT COUNT(*) FROM tl_content WHERE type = 'page_intro' OR (type = 'hero' AND heroLayout = '')");
    }

    public function run(): MigrationResult
    {
        $heroes = $this->connection->executeStatement("UPDATE tl_content SET heroLayout = 'background', heroWidth = 'default' WHERE type = 'hero' AND heroLayout = ''");

        // The old variant column stays in the database (contao:migrate drops columns only with --with-deletes)
        $columns = array_change_key_case($this->connection->createSchemaManager()->listTableColumns('tl_content'));
        $variant = isset($columns['introvariant']) ? 'introVariant' : "'default'";

        $intros = $this->connection->executeStatement(
            "UPDATE tl_content SET
                type = 'hero',
                heroLayout = CASE WHEN $variant = 'image' THEN 'split' ELSE 'plain' END,
                heroWidth = CASE WHEN $variant IN ('narrow', 'wide') THEN $variant ELSE 'default' END
            WHERE type = 'page_intro'",
        );

        return $this->createResult(true, \sprintf('%d hero elements got the layout "background", %d page intros became hero elements.', $heroes, $intros));
    }
}
