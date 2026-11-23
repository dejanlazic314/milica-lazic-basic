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
 * One block in a grid of cards (".card"): optional icon, H3, rich text and an optional
 * arrow link. Variant "plain" renders a bare <div> (e.g. "Šta se dešava kada mi pišete");
 * "outline" is for a dark section. Size "small" has a smaller heading, "large" more room
 * (a single card next to a text), "compact" a sans heading and smaller text (cards with an icon).
 */
#[AsContentElement('card', category: 'milica')]
final class CardController extends AbstractContentElementController
{
    public const VARIANTS = ['white', 'dark', 'green', 'sand', 'outline', 'plain'];

    public const SIZES = ['default', 'small', 'large', 'compact'];

    // Symbols in public/layout/icons/sprite.svg that make sense on a card
    public const ICONS = ['i-chat', 'i-calendar', 'i-sprout', 'i-clock', 'i-lock', 'i-video', 'i-location', 'i-phone', 'i-email', 'i-check'];

    protected function getResponse(FragmentTemplate $template, ContentModel $model, Request $request): Response
    {
        $template->set('variant', \in_array($model->cardVariant, self::VARIANTS, true) ? $model->cardVariant : 'white');
        $template->set('size', \in_array($model->cardSize, self::SIZES, true) ? $model->cardSize : 'default');
        $template->set('icon', \in_array($model->cardIcon, self::ICONS, true) ? $model->cardIcon : null);
        $template->set('title', (string) $model->introTitle);
        $template->set('text', (string) $model->text);
        $template->set('link', ['href' => (string) $model->introPrimaryUrl, 'text' => (string) $model->introPrimaryText]);

        return $template->getResponse();
    }
}
