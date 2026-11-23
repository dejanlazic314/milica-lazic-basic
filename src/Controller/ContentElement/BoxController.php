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
 * A highlighted block of text inside a section or a hero (".box"): white with a border
 * ("Ukratko" with a short list) or dark (crisis numbers on the topic pages).
 */
#[AsContentElement('box', category: 'milica')]
final class BoxController extends AbstractContentElementController
{
    public const STYLES = ['white', 'dark'];

    protected function getResponse(FragmentTemplate $template, ContentModel $model, Request $request): Response
    {
        $template->set('style', \in_array($model->boxStyle, self::STYLES, true) ? $model->boxStyle : 'white');
        $template->set('eyebrow', (string) $model->eyebrow);
        $template->set('title', (string) $model->boxTitle);
        $template->set('text', (string) $model->text);

        return $template->getResponse();
    }
}
