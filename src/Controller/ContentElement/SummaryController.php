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
 * Box "Ukratko" with a short list, inside a page intro.
 */
#[AsContentElement('summary', category: 'milica')]
final class SummaryController extends AbstractContentElementController
{
    protected function getResponse(FragmentTemplate $template, ContentModel $model, Request $request): Response
    {
        $template->set('eyebrow', (string) $model->eyebrow ?: 'Ukratko');
        $template->set('text', (string) $model->text);

        return $template->getResponse();
    }
}
