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
 * Intro of every subpage (".page-intro"): breadcrumb, H1, lead, optional buttons,
 * meta line and illustration. Extra blocks (filter, "Ukratko", legal date) are
 * nested elements and render below the meta line.
 */
#[AsContentElement('page_intro', category: 'milica', nestedFragments: true)]
final class PageIntroController extends AbstractContentElementController
{
    public const VARIANTS = ['default', 'image', 'narrow', 'wide'];

    public function __construct(
        private readonly Studio $studio,
        private readonly BreadcrumbBuilder $breadcrumb,
    ) {
    }

    protected function getResponse(FragmentTemplate $template, ContentModel $model, Request $request): Response
    {
        $page = $this->getPageModel();
        $figure = $this->buildFigure($model);

        $template->set('variant', \in_array($model->introVariant, self::VARIANTS, true) ? $model->introVariant : 'default');
        $template->set('title', (string) $model->introTitle);
        $template->set('text', (string) $model->introText);
        $template->set('meta', (string) $model->introMeta);
        $template->set('buttons', array_values(array_filter([
            ['href' => (string) $model->introPrimaryUrl, 'text' => (string) $model->introPrimaryText, 'class' => 'btn btn-apricot'],
            ['href' => (string) $model->introSecondaryUrl, 'text' => (string) $model->introSecondaryText, 'class' => 'btn btn-outline'],
        ], static fn (array $b): bool => '' !== $b['href'] && '' !== $b['text'])));
        $template->set('figure', $figure);
        $template->set('breadcrumb', $page ? $this->breadcrumb->build($page) : []);

        return $template->getResponse();
    }

    private function buildFigure(ContentModel $model): Figure|null
    {
        if (!$model->introImage) {
            return null;
        }

        // Illustrations are SVG and shown as they are; alt text comes from the file manager
        return $this->studio
            ->createFigureBuilder()
            ->fromUuid($model->introImage)
            ->buildIfResourceExists()
        ;
    }
}
