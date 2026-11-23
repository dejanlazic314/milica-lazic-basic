<?php

declare(strict_types=1);

namespace MilicaLazic\Migration;

use Contao\CoreBundle\Migration\AbstractMigration;
use Contao\CoreBundle\Migration\MigrationResult;
use Doctrine\DBAL\Connection;

/**
 * One-off: a grid of cards is one element "card_grid", inside a section.
 *
 * - "card_group" becomes a card grid with 3 columns (same heading, same cards);
 * - a section with a card grid (the grid options 2, 3 and 4 columns, the layouts
 *   "recognition" and "two_cards") gets a card grid inside, and its cards move into it.
 *   The section becomes a plain section (layout "stack") with the same heading;
 *   the closing line of "recognition" becomes the note of the grid.
 *
 * Runs while such an element exists, so it runs once.
 */
class CardGridMigration extends AbstractMigration
{
    /** Old grid option or layout => columns */
    private const GRIDS = [
        'grid--2' => '2',
        'grid--3-tablet' => '3',
        'grid--4' => '4',
    ];

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
        if (!isset(array_change_key_case($schema->listTableColumns('tl_content'))['gridcolumns'])) {
            return false;
        }

        // After CardMigration: it finds the cards by their section, before they move into a grid.
        // contao:migrate runs the pending migrations again until none is left.
        if ($this->connection->fetchOne("SELECT COUNT(*) FROM tl_content WHERE type = 'icon_card'")) {
            return false;
        }

        return (bool) $this->connection->fetchOne(
            "SELECT COUNT(*) FROM tl_content
            WHERE type = 'card_group'
                OR (type = 'section' AND (sectionLayout IN ('recognition', 'two_cards') OR sectionGrid IN ('grid--2', 'grid--3-tablet', 'grid--4')))",
        );
    }

    public function run(): MigrationResult
    {
        $groups = $this->connection->executeStatement("UPDATE tl_content SET type = 'card_grid', gridColumns = '3' WHERE type = 'card_group'");

        $sections = $this->connection->fetchAllAssociative(
            "SELECT id, sectionLayout, sectionGrid, introMeta FROM tl_content
            WHERE type = 'section' AND (sectionLayout IN ('recognition', 'two_cards') OR sectionGrid IN ('grid--2', 'grid--3-tablet', 'grid--4'))",
        );

        foreach ($sections as $section) {
            $columns = match ($section['sectionLayout']) {
                'two_cards' => '2',
                default => self::GRIDS[$section['sectionGrid']] ?? '3',
            };

            $this->connection->insert('tl_content', [
                'pid' => $section['id'],
                'ptable' => 'tl_content',
                'sorting' => 128,
                'tstamp' => time(),
                'type' => 'card_grid',
                'gridColumns' => $columns,
                'introMeta' => 'recognition' === $section['sectionLayout'] ? (string) $section['introMeta'] : '',
            ]);

            $grid = (int) $this->connection->lastInsertId();

            // Every nested element of these sections was a card in the grid
            $this->connection->executeStatement(
                "UPDATE tl_content SET pid = ? WHERE pid = ? AND ptable = 'tl_content' AND id <> ?",
                [$grid, $section['id'], $grid],
            );

            $this->connection->update('tl_content', [
                'sectionLayout' => 'stack',
                'sectionGrid' => '',
                'introMeta' => '',
                'tstamp' => time(),
            ], ['id' => $section['id']]);
        }

        return $this->createResult(true, \sprintf('%d card groups became card grids, %d sections got a card grid inside.', $groups, \count($sections)));
    }
}
