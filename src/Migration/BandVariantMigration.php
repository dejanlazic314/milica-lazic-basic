<?php

declare(strict_types=1);

namespace MilicaLazic\Migration;

use Contao\CoreBundle\Migration\AbstractMigration;
use Contao\CoreBundle\Migration\MigrationResult;
use Doctrine\DBAL\Connection;

/**
 * One-off: the band variants are named by their look, "online" → "white" and
 * "cta" → "apricot". Runs while a band has an old name, so it runs once.
 */
class BandVariantMigration extends AbstractMigration
{
    private const NAMES = ['online' => 'white', 'cta' => 'apricot'];

    public function __construct(private readonly Connection $connection)
    {
    }

    public function shouldRun(): bool
    {
        if (!$this->connection->createSchemaManager()->tablesExist(['tl_content'])) {
            return false;
        }

        return (bool) $this->connection->fetchOne("SELECT COUNT(*) FROM tl_content WHERE type = 'band' AND bandVariant IN ('online', 'cta')");
    }

    public function run(): MigrationResult
    {
        $count = 0;

        foreach (self::NAMES as $old => $new) {
            $count += $this->connection->update('tl_content', ['bandVariant' => $new], ['type' => 'band', 'bandVariant' => $old]);
        }

        return $this->createResult(true, \sprintf('%d bands got the new variant name.', $count));
    }
}
