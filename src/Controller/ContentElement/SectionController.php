<?php

declare(strict_types=1);

namespace MilicaLazic\Controller\ContentElement;

use Contao\ContentModel;
use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsContentElement;
use Contao\CoreBundle\Twig\FragmentTemplate;
use Contao\StringUtil;
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
        'approach' => 'approach',
        'my_path' => 'my-path',
        'preparation' => 'preparation',
        'diaspora' => 'diaspora',
        'location' => 'location',
    ];

    public const STYLES = ['white', 'warm'];

    /** Spacing => [class, inline style] as in the design markup */
    public const SPACINGS = [
        'no_top' => ['section--no-top', null],
        'tight' => [null, 'padding-top:0;padding-bottom:var(--section-small)'],
        'small' => [null, 'padding-top:var(--section-small);padding-bottom:var(--section-small)'],
    ];

    protected function getResponse(FragmentTemplate $template, ContentModel $model, Request $request): Response
    {
        $layout = isset(self::LAYOUTS[$model->sectionLayout]) ? $model->sectionLayout : 'stack';

        $template->set('layout', $layout);
        $template->set('content_class', self::LAYOUTS[$layout]);
        $template->set('extra_assets', $this->getExtraAssets($layout, $model));
        $template->set('style', \in_array($model->sectionStyle, self::STYLES, true) ? $model->sectionStyle : null);
        $template->set('spacing', self::SPACINGS[$model->sectionSpacing] ?? [null, null]);
        $template->set('eyebrow', (string) $model->eyebrow);
        $template->set('title', (string) $model->introTitle);
        $template->set('lead', (string) $model->introText);
        $template->set('text', (string) $model->text);
        $template->set('end', (string) $model->introMeta);
        $template->set('map', ['embed' => (string) $model->mapEmbedUrl, 'link' => (string) $model->mapLinkUrl]);

        return $template->getResponse();
    }

    /**
     * CSS and JS besides elements/section (src/Twig/AssetsExtension.php): the file of the
     * layout and of each class in the CSS ID field, e.g. elements/section-approach and
     * elements/section-values. The layout "location" has a map behind consent.
     *
     * @return list<string>
     */
    private function getExtraAssets(string $layout, ContentModel $model): array
    {
        $classes = array_filter(explode(' ', StringUtil::deserialize($model->cssID, true)[1] ?? ''));

        return [
            'elements/section-'.$layout,
            ...array_map(static fn (string $class): string => 'elements/section-'.$class, $classes),
            ...('location' === $layout ? ['components/map-consent'] : []),
        ];
    }
}
