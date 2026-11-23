<?php

declare(strict_types=1);

namespace MilicaLazic\Migration;

use Contao\CoreBundle\Migration\AbstractMigration;
use Contao\CoreBundle\Migration\MigrationResult;
use Doctrine\DBAL\Connection;

/**
 * One-off: the element "summary" and the section layout "crisis" are one element "box".
 *
 * - a summary becomes a white box (same eyebrow and text);
 * - a crisis section becomes a plain section (layout "stack", same background and spacing)
 *   with a dark box inside, which takes over its title and text.
 *
 * Runs while a summary or a crisis section exists, so it runs once.
 */
class BoxMigration extends AbstractMigration
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
        if (!isset($columns['boxstyle'], $columns['boxtitle'])) {
            return false;
        }

        return (bool) $this->connection->fetchOne("SELECT COUNT(*) FROM tl_content WHERE type = 'summary' OR (type = 'section' AND sectionLayout = 'crisis')");
    }

    public function run(): MigrationResult
    {
        $summaries = $this->connection->executeStatement("UPDATE tl_content SET type = 'box', boxStyle = 'white' WHERE type = 'summary'");
        $sections = $this->connection->fetchAllAssociative("SELECT id, introTitle, text FROM tl_content WHERE type = 'section' AND sectionLayout = 'crisis'");

        foreach ($sections as $section) {
            $this->connection->insert('tl_content', [
                'pid' => $section['id'],
                'ptable' => 'tl_content',
                'sorting' => 128,
                'tstamp' => time(),
                'type' => 'box',
                'boxStyle' => 'dark',
                'boxTitle' => (string) $section['introTitle'],
                'text' => $section['text'],
            ]);

            $this->connection->update('tl_content', [
                'sectionLayout' => 'stack',
                'introTitle' => '',
                'text' => null,
                'tstamp' => time(),
            ], ['id' => $section['id']]);
        }

        return $this->createResult(true, \sprintf('%d summaries and %d crisis sections became boxes.', $summaries, \count($sections)));
    }
}
