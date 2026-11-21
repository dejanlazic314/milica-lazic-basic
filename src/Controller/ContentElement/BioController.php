<?php

declare(strict_types=1);

namespace MilicaLazic\Controller\ContentElement;

use Contao\ContentModel;
use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsContentElement;
use Contao\CoreBundle\Image\Studio\Studio;
use Contao\CoreBundle\Twig\FragmentTemplate;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Author bio: round portrait, short text and a link (end of the Geštalt page).
 * Posts render the same box from the post reader template.
 */
#[AsContentElement('bio', category: 'milica')]
final class BioController extends AbstractContentElementController
{
    public function __construct(private readonly Studio $studio)
    {
    }

    protected function getResponse(FragmentTemplate $template, ContentModel $model, Request $request): Response
    {
        $template->set('bio', [
            'text' => (string) $model->text,
            'link' => ['href' => (string) $model->introPrimaryUrl, 'text' => (string) $model->introPrimaryText],
            'figure' => $model->introImage
                ? $this->studio->createFigureBuilder()->fromUuid($model->introImage)->setSize('_portrait_bio')->buildIfResourceExists()
                : null,
        ]);

        return $template->getResponse();
    }
}
