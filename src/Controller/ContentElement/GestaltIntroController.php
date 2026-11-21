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
 * Dark "Šta je geštalt terapija?" section of the home page (#gestalt): text, button
 * and the three-circles diagram. Nested elements (e.g. "Kako izgleda saradnja")
 * render below inside the same dark section.
 */
#[AsContentElement('gestalt_intro', category: 'milica', nestedFragments: true)]
final class GestaltIntroController extends AbstractContentElementController
{
    protected function getResponse(FragmentTemplate $template, ContentModel $model, Request $request): Response
    {
        $template->set('eyebrow', (string) $model->eyebrow);
        $template->set('title', (string) $model->introTitle);
        $template->set('lead', (string) $model->introText);
        $template->set('body', (string) $model->text);
        $template->set('button', ['href' => (string) $model->introPrimaryUrl, 'text' => (string) $model->introPrimaryText]);

        return $template->getResponse();
    }
}
