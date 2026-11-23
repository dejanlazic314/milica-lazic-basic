<?php

declare(strict_types=1);

namespace MilicaLazic\Migration;

use Contao\CoreBundle\Migration\AbstractMigration;
use Contao\CoreBundle\Migration\MigrationResult;
use Contao\StringUtil;
use Doctrine\DBAL\Connection;

/**
 * One-off: the look of a card is set on the card, no longer by the section around it.
 *
 * - "icon_card" becomes a card with the look "outline" and the size "compact"; its plain
 *   text becomes a paragraph of the card text;
 * - cards in a section with the layout "recognition" get the size "small", in a section with
 *   the layout "preparation" the size "large", in a section with the class "online-steps"
 *   the size "compact";
 * - the classes "values", "audience" and "online-steps" are removed from the sections:
 *   their CSS files are gone, the cards carry their look themselves.
 *
 * Runs while an icon card exists, so it runs once and never changes cards edited later.
 */
class CardMigration extends AbstractMigration
{
    private const CLASSES = ['values', 'audience', 'online-steps'];

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
        if (!isset(array_change_key_case($schema->listTableColumns('tl_content'))['cardsize'])) {
            return false;
        }

        return (bool) $this->connection->fetchOne("SELECT COUNT(*) FROM tl_content WHERE type = 'icon_card'");
    }

    public function run(): MigrationResult
    {
        // The text was stored already encoded (plain text field), so it goes into the paragraph as it is
        $icons = $this->connection->executeStatement(
            "UPDATE tl_content SET type = 'card', cardVariant = 'outline', cardSize = 'compact',
                text = CONCAT('<p>', COALESCE(introText, ''), '</p>'), introText = NULL
            WHERE type = 'icon_card'",
        );

        $small = $this->setSize("p.sectionLayout = 'recognition'", 'small');
        $large = $this->setSize("p.sectionLayout = 'preparation'", 'large');
        $compact = $this->setSize("p.cssID LIKE '%online-steps%'", 'compact');

        $sections = $this->connection->fetchAllAssociative("SELECT id, cssID FROM tl_content WHERE type = 'section' AND (cssID LIKE '%values%' OR cssID LIKE '%audience%' OR cssID LIKE '%online-steps%')");

        foreach ($sections as $section) {
            $cssId = StringUtil::deserialize($section['cssID'], true) + ['', ''];
            $classes = array_diff(explode(' ', (string) $cssId[1]), self::CLASSES);
            $cssId[1] = implode(' ', array_filter($classes));

            $this->connection->update('tl_content', ['cssID' => serialize([$cssId[0], $cssId[1]])], ['id' => $section['id']]);
        }

        return $this->createResult(true, \sprintf('%d icon cards became cards, %d cards got the size "small", %d "large", %d "compact"; %d sections lost their card class.', $icons, $small, $large, $compact, \count($sections)));
    }

    private function setSize(string $parentCondition, string $size): int
    {
        return $this->connection->executeStatement(
            "UPDATE tl_content c JOIN tl_content p ON p.id = c.pid AND c.ptable = 'tl_content' AND p.type = 'section'
            SET c.cardSize = ?
            WHERE c.type = 'card' AND $parentCondition",
            [$size],
        );
    }
}
