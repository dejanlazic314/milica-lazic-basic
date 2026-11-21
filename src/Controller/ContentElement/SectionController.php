<?php

declare(strict_types=1);

namespace MilicaLazic\Controller\ContentElement;

use Contao\ContentModel;
use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsContentElement;
use Contao\CoreBundle\Twig\FragmentTemplate;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Generic subpage section: background, spacing, a layout from the design and the
 * heading fields. Cards, steps, FAQ lists etc. go inside as nested elements.
 *
 * Extra section classes (e.g. "values", "audience") and anchors go in the CSS ID field.
 */
#[AsContentElement('section', category: 'milica', nestedFragments: true)]
final class SectionController extends AbstractContentElementController
{
    /** Layout => wrapper class of div.content */
    public const LAYOUTS = [
        'stack' => 'stack',
        'recognition' => 'recognition',
        'approach' => 'approach',
        'crisis' => 'crisis-box',
        'my_path' => 'my-path',
        'preparation' => 'preparation',
        'diaspora' => 'diaspora',
        'education' => 'stack',
        'location' => 'location',
        'two_cards' => 'two-cards',
    ];

    public const STYLES = ['white', 'warm'];

    /** Spacing => [class, inline style] as in the design markup */
    public const SPACINGS = [
        'no_top' => ['section--no-top', null],
        'tight' => [null, 'padding-top:0;padding-bottom:var(--section-small)'],
        'small' => [null, 'padding-top:var(--section-small);padding-bottom:var(--section-small)'],
    ];

    public const GRIDS = ['grid--2', 'grid--3', 'grid--3-tablet', 'grid--4', 'after-message'];

    protected function getResponse(FragmentTemplate $template, ContentModel $model, Request $request): Response
    {
        $layout = isset(self::LAYOUTS[$model->sectionLayout]) ? $model->sectionLayout : 'stack';

        $template->set('layout', $layout);
        $template->set('content_class', self::LAYOUTS[$layout]);
        $template->set('style', \in_array($model->sectionStyle, self::STYLES, true) ? $model->sectionStyle : null);
        $template->set('spacing', self::SPACINGS[$model->sectionSpacing] ?? [null, null]);
        $template->set('grid', \in_array($model->sectionGrid, self::GRIDS, true) ? $model->sectionGrid : null);
        $template->set('eyebrow', (string) $model->eyebrow);
        $template->set('title', (string) $model->introTitle);
        $template->set('lead', (string) $model->introText);
        $template->set('text', (string) $model->text);
        $template->set('end', (string) $model->introMeta);
        $template->set('rows', $this->parseRows((string) $model->sectionRows));
        $template->set('chips', array_values(array_filter(array_map(trim(...), explode("\n", (string) $model->sectionChips)))));
        $template->set('map', ['embed' => (string) $model->mapEmbedUrl, 'link' => (string) $model->mapLinkUrl]);

        return $template->getResponse();
    }

    /**
     * One row per line, columns separated by "|" (education: year | name | place;
     * time zones: city | difference).
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
