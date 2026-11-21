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
 * One part of a "toc_article": H2, optional circles diagram, text; nested elements
 * (e.g. an inline FAQ list) render below the text.
 */
#[AsContentElement('article_part', category: 'milica', nestedFragments: true)]
final class ArticlePartController extends AbstractContentElementController
{
    protected function getResponse(FragmentTemplate $template, ContentModel $model, Request $request): Response
    {
        $template->set('title', (string) $model->introTitle);
        $template->set('text', (string) $model->text);
        $template->set('circles', (bool) $model->showCircles);

        return $template->getResponse();
    }
}
