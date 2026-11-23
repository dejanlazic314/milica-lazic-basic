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
 * Full-width quote over an optional background photo (".quote").
 * Without a photo the template falls back to the dark sage variant.
 */
#[AsContentElement('quote', category: 'milica')]
final class QuoteController extends AbstractContentElementController
{
    public function __construct(private readonly Studio $studio)
    {
    }

    protected function getResponse(FragmentTemplate $template, ContentModel $model, Request $request): Response
    {
        $template->set('quote', (string) $model->quote);
        $template->set('source', (string) $model->quoteSource);
        $template->set('text', (string) $model->quoteText);
        $template->set('link_url', (string) $model->quoteLink);
        $template->set('link_text', (string) $model->linkTitle);
        $template->set('figure', $this->buildFigure($model));

        return $template->getResponse();
    }

    private function buildFigure(ContentModel $model): Figure|null
    {
        if (!$model->quoteImage) {
            return null;
        }

        return $this->studio
            ->createFigureBuilder()
            ->fromUuid($model->quoteImage)
            ->setSize('_quote') // config/config.yaml
            ->buildIfResourceExists()
        ;
    }
}
