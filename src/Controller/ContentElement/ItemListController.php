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
 * A list of short items (".item-list"); each row has a label, a title and a text, and the
 * style decides which of them it shows:
 *
 * - steps: dot, title, text (the approach on a topic page)
 * - checks: check mark and text ("Šta vam je potrebno")
 * - timeline: label (year), title, text (education; [text](link) works in the text)
 * - pairs: label and title in small boxes (time zones)
 * - tags: title as a chip (languages, memberships)
 * - row: title and text in columns with a line above ("Šta se dešava kada mi pišete")
 */
#[AsContentElement('item_list', category: 'milica')]
final class ItemListController extends AbstractContentElementController
{
    /** Style => list tag */
    public const STYLES = [
        'steps' => 'ol',
        'checks' => 'ul',
        'timeline' => 'ol',
        'pairs' => 'div',
        'tags' => 'div',
        'row' => 'div',
    ];

    protected function getResponse(FragmentTemplate $template, ContentModel $model, Request $request): Response
    {
        $style = isset(self::STYLES[$model->listStyle]) ? $model->listStyle : 'steps';
        $items = [];

        foreach (StringUtil::deserialize($model->listRows, true) as $row) {
            $item = [
                'label' => trim((string) ($row['label'] ?? '')),
                'title' => trim((string) ($row['title'] ?? '')),
                'text' => trim((string) ($row['text'] ?? '')),
            ];

            if (implode('', $item)) {
                $items[] = $item;
            }
        }

        $template->set('style', $style);
        $template->set('tag', self::STYLES[$style]);
        $template->set('items', $items);

        return $template->getResponse();
    }
}
