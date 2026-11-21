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
 * One step of the "approach" list (section layout "approach").
 */
#[AsContentElement('step', category: 'milica')]
final class StepController extends AbstractContentElementController
{
    protected function getResponse(FragmentTemplate $template, ContentModel $model, Request $request): Response
    {
        $template->set('title', (string) $model->introTitle);
        $template->set('text', (string) $model->introText);

        return $template->getResponse();
    }
}
