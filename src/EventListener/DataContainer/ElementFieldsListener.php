<?php

declare(strict_types=1);

namespace MilicaLazic\EventListener\DataContainer;

use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Contao\DataContainer;
use Contao\Input;
use Doctrine\DBAL\Connection;

/**
 * Shared fields (introTitle, text, …) are defined once, for all elements. While an element
 * is being edited, this adapts them to its type:
 *
 * - labels from $GLOBALS['TL_LANG']['tl_content']['labels'][<type>] (contao/languages/en/tl_content.php),
 *   for elements where a field means something else ("Choice" instead of "Heading (H1)");
 * - fields listed in $GLOBALS['TL_DCA']['tl_content']['optionalFields'][<type>] (the element's
 *   file in contao/elements/) are not mandatory, e.g. a section without a heading.
 */
class ElementFieldsListener
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

        foreach ($GLOBALS['TL_DCA']['tl_content']['optionalFields'][$type] ?? [] as $field) {
            if (isset($GLOBALS['TL_DCA']['tl_content']['fields'][$field])) {
                $GLOBALS['TL_DCA']['tl_content']['fields'][$field]['eval']['mandatory'] = false;
            }
        }
    }
}
