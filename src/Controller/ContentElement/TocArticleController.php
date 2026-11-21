<?php

declare(strict_types=1);

namespace MilicaLazic\Controller\ContentElement;

use Contao\ContentModel;
use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsContentElement;
use Contao\CoreBundle\Twig\FragmentTemplate;
use Contao\StringUtil;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Long text with a sticky table of contents (page "Geštalt terapija"). The TOC lists
 * the nested "article_part" elements that have a heading and a CSS ID.
 */
#[AsContentElement('toc_article', category: 'milica', nestedFragments: true)]
final class TocArticleController extends AbstractContentElementController
{
    protected function getResponse(FragmentTemplate $template, ContentModel $model, Request $request): Response
    {
        $toc = [];
        $parts = $this->getContaoAdapter(ContentModel::class)->findPublishedByPidAndTable((int) $model->id, 'tl_content');

        foreach ($parts ?? [] as $part) {
            $id = StringUtil::deserialize($part->cssID, true)[0] ?? '';

            if ('article_part' === $part->type && $part->introTitle && $id) {
                $toc[] = ['id' => $id, 'title' => (string) $part->introTitle];
            }
        }

        $template->set('toc', $toc);

        return $template->getResponse();
    }
}
