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
 * A grid of cards (".card-grid"): optional heading, the nested "card" elements in 2, 3 or
 * 4 columns, and an optional note below them. Phones always get one column; see the grid
 * classes in public/layout/css/layout/page.css for the tablet steps.
 */
#[AsContentElement('card_grid', category: 'milica', nestedFragments: true)]
final class CardGridController extends AbstractContentElementController
{
    /** Columns on a desktop => grid class */
    public const COLUMNS = [
        '2' => 'grid--2',
        '3' => 'grid--3-tablet',
        '4' => 'grid--4',
    ];

    protected function getResponse(FragmentTemplate $template, ContentModel $model, Request $request): Response
    {
        $template->set('grid_class', self::COLUMNS[$model->gridColumns] ?? self::COLUMNS['3']);
        $template->set('title', (string) $model->introTitle);
        $template->set('note', (string) $model->introMeta);

        return $template->getResponse();
    }
}
