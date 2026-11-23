<?php

declare(strict_types=1);

namespace MilicaLazic\Controller\ContentElement;

use Contao\ContentModel;
use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsContentElement;
use Contao\CoreBundle\Image\Studio\Figure;
use Contao\CoreBundle\Image\Studio\Studio;
use Contao\CoreBundle\Twig\FragmentTemplate;
use MilicaLazic\Navigation\BreadcrumbBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * The top block of a page, with its H1 (one per page). Markup: component/_hero.html.twig.
 *
 * The layout decides the role of the image: behind the text (background, a photo) or
 * next to it (split, an illustration); "plain" has none. The width applies to split and
 * plain. Nested elements (filter, "Ukratko") render below the meta line.
 */
#[AsContentElement('hero', category: 'milica', nestedFragments: true)]
final class HeroController extends AbstractContentElementController
{
    /** Not "text": Contao would read the value as the name of the core palette "text" (see contao/elements/hero.php) */
    public const LAYOUTS = ['background', 'split', 'plain'];

    public const WIDTHS = ['default', 'narrow', 'wide'];

    public function __construct(
        private readonly Studio $studio,
        private readonly BreadcrumbBuilder $breadcrumb,
    ) {
    }

    protected function getResponse(FragmentTemplate $template, ContentModel $model, Request $request): Response
    {
        $page = $this->getPageModel();
        $layout = \in_array($model->heroLayout, self::LAYOUTS, true) ? $model->heroLayout : 'plain';
        $background = 'background' === $layout;

        // Light buttons on the dark photo, the usual ones on the light background
        $buttons = $background
            ? ['btn btn-apricot btn-large', 'btn btn-outline-light']
            : ['btn btn-apricot', 'btn btn-outline'];

        $template->set('layout', $layout);
        $template->set('width', \in_array($model->heroWidth, self::WIDTHS, true) ? $model->heroWidth : 'default');
        $template->set('eyebrow', (string) $model->eyebrow);
        $template->set('title', (string) $model->introTitle);
        $template->set('text', (string) $model->introText);
        $template->set('meta', (string) $model->introMeta);
        $template->set('buttons', array_values(array_filter([
            ['href' => (string) $model->introPrimaryUrl, 'text' => (string) $model->introPrimaryText, 'class' => $buttons[0]],
            ['href' => (string) $model->introSecondaryUrl, 'text' => (string) $model->introSecondaryText, 'class' => $buttons[1]],
        ], static fn (array $b): bool => '' !== $b['href'] && '' !== $b['text'])));
        $template->set('figure', 'plain' === $layout ? null : $this->buildFigure($model, $background));
        $template->set('breadcrumb', $page ? $this->breadcrumb->build($page) : []);

        return $template->getResponse();
    }

    private function buildFigure(ContentModel $model, bool $background): Figure|null
    {
        if (!$model->introImage) {
            return null;
        }

        $builder = $this->studio->createFigureBuilder()->fromUuid($model->introImage);

        // The photo is the LCP: its own sizes, not lazy (config/config.yaml). Illustrations
        // are SVG and shown as they are. Alt text comes from the file manager.
        if ($background) {
            $builder->setSize('_hero');
        }

        return $builder->buildIfResourceExists();
    }
}
