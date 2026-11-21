<?php

declare(strict_types=1);

namespace MilicaLazic\Controller\ContentElement;

use Contao\ContentModel;
use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsContentElement;
use Contao\CoreBundle\Image\Studio\Figure;
use Contao\CoreBundle\Image\Studio\Studio;
use Contao\CoreBundle\Twig\FragmentTemplate;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Home page hero (".hero"): eyebrow, H1, lead, two buttons, status line over a
 * full-width photo. Without a photo it falls back to dark sage ("--no-photo").
 * Shares the text and button fields with the page intro.
 */
#[AsContentElement('hero', category: 'milica')]
final class HeroController extends AbstractContentElementController
{
    public function __construct(private readonly Studio $studio)
    {
    }

    protected function getResponse(FragmentTemplate $template, ContentModel $model, Request $request): Response
    {
        $template->set('eyebrow', (string) $model->eyebrow);
        $template->set('title', (string) $model->introTitle);
        $template->set('text', (string) $model->introText);
        $template->set('status', (string) $model->introMeta);
        $template->set('buttons', array_values(array_filter([
            ['href' => (string) $model->introPrimaryUrl, 'text' => (string) $model->introPrimaryText, 'class' => 'btn btn-apricot btn-large'],
            ['href' => (string) $model->introSecondaryUrl, 'text' => (string) $model->introSecondaryText, 'class' => 'btn btn-outline-light'],
        ], static fn (array $b): bool => '' !== $b['href'] && '' !== $b['text'])));
        $template->set('figure', $this->buildFigure($model));

        return $template->getResponse();
    }

    private function buildFigure(ContentModel $model): Figure|null
    {
        if (!$model->introImage) {
            return null;
        }

        return $this->studio
            ->createFigureBuilder()
            ->fromUuid($model->introImage)
            ->setSize('_hero') // config/config.yaml, not lazy
            ->buildIfResourceExists()
        ;
    }
}
