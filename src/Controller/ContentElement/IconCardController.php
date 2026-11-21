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
 * One card with an icon, a heading (H4) and a short text, nested in a card group.
 */
#[AsContentElement('icon_card', category: 'milica')]
final class IconCardController extends AbstractContentElementController
{
    // Symbols in public/layout/icons/sprite.svg that make sense on a card
    public const ICONS = ['i-chat', 'i-calendar', 'i-sprout', 'i-clock', 'i-lock', 'i-video', 'i-location', 'i-phone', 'i-email', 'i-check'];

    protected function getResponse(FragmentTemplate $template, ContentModel $model, Request $request): Response
    {
        $template->set('icon', \in_array($model->cardIcon, self::ICONS, true) ? $model->cardIcon : null);
        $template->set('title', (string) $model->introTitle);
        $template->set('text', (string) $model->introText);

        return $template->getResponse();
    }
}
