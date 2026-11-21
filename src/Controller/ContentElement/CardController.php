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
 * Card inside a "section": optional icon, H3, rich text and an optional arrow link.
 * Variant "plain" renders a bare <div> (e.g. "Šta se dešava kada mi pišete").
 */
#[AsContentElement('card', category: 'milica')]
final class CardController extends AbstractContentElementController
{
    public const VARIANTS = ['white', 'dark', 'green', 'sand', 'plain'];

    protected function getResponse(FragmentTemplate $template, ContentModel $model, Request $request): Response
    {
        $template->set('variant', \in_array($model->cardVariant, self::VARIANTS, true) ? $model->cardVariant : 'white');
        $template->set('icon', \in_array($model->cardIcon, IconCardController::ICONS, true) ? $model->cardIcon : null);
        $template->set('title', (string) $model->introTitle);
        $template->set('text', (string) $model->text);
        $template->set('link', ['href' => (string) $model->introPrimaryUrl, 'text' => (string) $model->introPrimaryText]);

        return $template->getResponse();
    }
}
