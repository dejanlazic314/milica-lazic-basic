<?php

declare(strict_types=1);

namespace MilicaLazic\EventListener\DataContainer;

use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Contao\CoreBundle\Security\ContaoCorePermissions;
use Contao\CoreBundle\Security\DataContainer\CreateAction;
use Contao\DC_Table;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * Limits the element type select to our own elements plus a few core ones, so the
 * editor only sees what the design supports. Priority > 0 replaces the core
 * options callback (priority 0).
 */
class ContentElementTypeListener
{
    // Whole groups (keys of $GLOBALS['TL_CTE'])
    private const GROUPS = ['milica'];

    // Single core elements, by type
    private const CORE_TYPES = ['text', 'headline', 'image', 'accordion', 'element_group', 'html', 'module', 'alias', 'form'];

    public function __construct(private readonly Security $security)
    {
    }

    #[AsCallback(table: 'tl_content', target: 'fields.type.options', priority: 10)]
    public function getOptions(DC_Table $dc): array
    {
        $options = [];

        foreach ($GLOBALS['TL_CTE'] as $group => $elements) {
            foreach (array_keys($elements) as $type) {
                $wanted = \in_array($group, self::GROUPS, true) || \in_array($type, self::CORE_TYPES, true);

                if ($wanted && $this->isAllowed($dc, $type)) {
                    $options[$group][] = $type;
                }
            }
        }

        // Keep the current type selectable so existing elements are not silently changed
        $current = $dc->activeRecord?->type;

        if ($current && !\in_array($current, array_merge(...array_values($options ?: [[]])), true)) {
            foreach ($GLOBALS['TL_CTE'] as $group => $elements) {
                if (isset($elements[$current])) {
                    $options[$group][] = $current;
                    break;
                }
            }
        }

        return $options;
    }

    private function isAllowed(DC_Table $dc, string $type): bool
    {
        $action = new CreateAction('tl_content', [
            'ptable' => $dc->parentTable,
            'pid' => $dc->currentPid,
            'type' => $type,
        ]);

        return $this->security->isGranted(ContaoCorePermissions::DC_PREFIX.'tl_content', $action);
    }
}
