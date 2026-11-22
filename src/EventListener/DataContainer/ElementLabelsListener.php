<?php

declare(strict_types=1);

namespace MilicaLazic\EventListener\DataContainer;

use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Contao\DataContainer;
use Contao\Input;
use Doctrine\DBAL\Connection;

/**
 * Shared fields (introTitle, introText, …) have general labels such as "Heading (H1)".
 * For elements where they mean something else, this swaps in the labels from
 * $GLOBALS['TL_LANG']['tl_content']['labels'][<type>] (contao/languages/en/tl_content.php)
 * while such an element is being edited.
 */
class ElementLabelsListener
{
    public function __construct(private readonly Connection $connection)
    {
    }

    #[AsCallback(table: 'tl_content', target: 'config.onload')]
    public function __invoke(DataContainer|null $dc = null): void
    {
        // Only in the edit form; in the list view the id is the parent's
        if (!$dc?->id || 'edit' !== Input::get('act')) {
            return;
        }

        $type = $this->connection->fetchOne('SELECT type FROM tl_content WHERE id = ?', [$dc->id]);

        foreach ($GLOBALS['TL_LANG']['tl_content']['labels'][$type] ?? [] as $field => $label) {
            if (isset($GLOBALS['TL_DCA']['tl_content']['fields'][$field])) {
                $GLOBALS['TL_DCA']['tl_content']['fields'][$field]['label'] = $label;
            }
        }
    }
}
