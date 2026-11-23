<?php

declare(strict_types=1);

namespace MilicaLazic\Migration;

use Contao\CoreBundle\Migration\AbstractMigration;
use Contao\CoreBundle\Migration\MigrationResult;
use Doctrine\DBAL\Connection;

/**
 * One-off: the short lists of a section are one element "item_list", nested in the section.
 *
 * - the "step" elements of a section become one list "steps" in their place;
 * - the rows of a section ("year | name | place", "city | difference", one item per line)
 *   become a list: "timeline" (layout education), "pairs" (diaspora) or "checks"
 *   (preparation, in front of the card), and its chips a list "tags";
 * - the plain cards of a section with the grid "after-message" become one list "row";
 * - the layout "education" becomes "stack".
 *
 * Runs while a step, section rows or chips, or the grid "after-message" exist, so it runs once.
 */
class ItemListMigration extends AbstractMigration
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
        if (!isset($columns['liststyle'], $columns['listrows'])) {
            return false;
        }

        if ($this->connection->fetchOne("SELECT COUNT(*) FROM tl_content WHERE type = 'step'")) {
            return true;
        }

        // Without the old columns (a new installation) there is nothing to convert
        if (!isset($columns['sectionrows'], $columns['sectionchips'], $columns['sectiongrid'])) {
            return false;
        }

        return (bool) $this->connection->fetchOne(
            "SELECT COUNT(*) FROM tl_content WHERE type = 'section'
            AND (COALESCE(sectionRows, '') <> '' OR COALESCE(sectionChips, '') <> '' OR sectionGrid = 'after-message')",
        );
    }

    public function run(): MigrationResult
    {
        $lists = 0;

        // Steps: one list in the place of the first step
        foreach ($this->connection->fetchFirstColumn("SELECT DISTINCT pid FROM tl_content WHERE type = 'step' AND ptable = 'tl_content'") as $pid) {
            $steps = $this->connection->fetchAllAssociative("SELECT id, sorting, introTitle, introText FROM tl_content WHERE type = 'step' AND ptable = 'tl_content' AND pid = ? ORDER BY sorting", [$pid]);
            $items = array_map(static fn (array $step): array => ['label' => '', 'title' => (string) $step['introTitle'], 'text' => (string) $step['introText']], $steps);

            $this->insertList((int) $pid, (int) $steps[0]['sorting'], 'steps', $items);
            $this->connection->executeStatement("DELETE FROM tl_content WHERE type = 'step' AND ptable = 'tl_content' AND pid = ?", [$pid]);
            ++$lists;
        }

        $columns = array_change_key_case($this->connection->createSchemaManager()->listTableColumns('tl_content'));

        if (!isset($columns['sectionrows'], $columns['sectionchips'], $columns['sectiongrid'])) {
            return $this->createResult(true, \sprintf('%d lists.', $lists));
        }

        $sections = $this->connection->fetchAllAssociative(
            "SELECT id, sectionLayout, sectionRows, sectionChips, sectionGrid FROM tl_content WHERE type = 'section'
            AND (COALESCE(sectionRows, '') <> '' OR COALESCE(sectionChips, '') <> '' OR sectionGrid = 'after-message')",
        );

        foreach ($sections as $section) {
            $id = (int) $section['id'];
            $rows = $this->parseRows((string) $section['sectionRows']);
            $last = (int) $this->connection->fetchOne("SELECT COALESCE(MAX(sorting), 0) FROM tl_content WHERE pid = ? AND ptable = 'tl_content'", [$id]);

            if ($rows) {
                [$style, $items, $sorting] = match ($section['sectionLayout']) {
                    'education' => ['timeline', array_map(static fn (array $r): array => ['label' => $r[0], 'title' => $r[1] ?? '', 'text' => $r[2] ?? ''], $rows), $last + 128],
                    'diaspora' => ['pairs', array_map(static fn (array $r): array => ['label' => $r[0], 'title' => $r[1] ?? '', 'text' => ''], $rows), $last + 128],
                    // In front of the card: the left column has the list, the right one the card
                    default => ['checks', array_map(static fn (array $r): array => ['label' => '', 'title' => '', 'text' => $r[0]], $rows), 64],
                };

                $this->insertList($id, $sorting, $style, $items);
                $last = max($last, $sorting);
                ++$lists;
            }

            $chips = array_values(array_filter(array_map(trim(...), explode("\n", (string) $section['sectionChips']))));

            if ($chips) {
                $this->insertList($id, $last + 128, 'tags', array_map(static fn (string $chip): array => ['label' => '', 'title' => $chip, 'text' => ''], $chips));
                ++$lists;
            }

            if ('after-message' === $section['sectionGrid']) {
                $cards = $this->connection->fetchAllAssociative("SELECT id, sorting, introTitle, text FROM tl_content WHERE pid = ? AND ptable = 'tl_content' AND type = 'card' ORDER BY sorting", [$id]);

                if ($cards) {
                    // The card text was one paragraph of rich text; the list item has plain text
                    $items = array_map(static fn (array $card): array => ['label' => '', 'title' => (string) $card['introTitle'], 'text' => trim(strip_tags((string) $card['text']))], $cards);

                    $this->insertList($id, (int) $cards[0]['sorting'], 'row', $items);
                    $this->connection->executeStatement("DELETE FROM tl_content WHERE pid = ? AND ptable = 'tl_content' AND type = 'card'", [$id]);
                    ++$lists;
                }
            }

            $this->connection->update('tl_content', [
                'sectionLayout' => 'education' === $section['sectionLayout'] ? 'stack' : $section['sectionLayout'],
                'sectionRows' => null,
                'sectionChips' => null,
                'sectionGrid' => '',
                'tstamp' => time(),
            ], ['id' => $id]);
        }

        return $this->createResult(true, \sprintf('%d lists from steps, section rows, chips and plain cards.', $lists));
    }

    /**
     * @param list<array{label: string, title: string, text: string}> $items
     */
    private function insertList(int $pid, int $sorting, string $style, array $items): void
    {
        $this->connection->insert('tl_content', [
            'pid' => $pid,
            'ptable' => 'tl_content',
            'sorting' => $sorting,
            'tstamp' => time(),
            'type' => 'item_list',
            'listStyle' => $style,
            'listRows' => serialize($items),
        ]);
    }

    /**
     * One row per line, columns separated by "|".
     *
     * @return list<list<string>>
     */
    private function parseRows(string $value): array
    {
        $rows = [];

        foreach (explode("\n", $value) as $line) {
            if ('' !== trim($line)) {
                $rows[] = array_map(trim(...), explode('|', $line));
            }
        }

        return $rows;
    }
}
