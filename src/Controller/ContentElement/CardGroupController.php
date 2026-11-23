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
 * Heading plus a grid of cards; the cards are nested "card" elements.
 * Variant "how": "Kako izgleda saradnja" inside the dark #gestalt section.
 */
#[AsContentElement('card_group', category: 'milica', nestedFragments: true)]
final class CardGroupController extends AbstractContentElementController
{
    public const VARIANTS = ['how'];

    protected function getResponse(FragmentTemplate $template, ContentModel $model, Request $request): Response
    {
        $template->set('variant', \in_array($model->cardsVariant, self::VARIANTS, true) ? $model->cardsVariant : 'how');
        $template->set('title', (string) $model->introTitle);

        return $template->getResponse();
    }
}
