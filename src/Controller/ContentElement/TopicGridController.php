<?php

declare(strict_types=1);

namespace MilicaLazic\Controller\ContentElement;

use Contao\ContentModel;
use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsContentElement;
use Contao\CoreBundle\Image\Studio\Studio;
use Contao\CoreBundle\Twig\FragmentTemplate;
use MilicaLazic\Site\SiteSettings;
use MilicaLazic\Topics\TopicProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Grid of topic cards from the subpages of "S čim radim", plus the contact card
 * ("Ne vidite svoju temu ovde?", its own fields; not shown while empty). Variant "home": heading, lead and corner ornament (#teme). Variant
 * "overview": bottom row with a note and a link (page "S čim radim").
 * "other": "Druge teme" at the end of a topic page (without the current page).
 */
#[AsContentElement('topic_grid', category: 'milica')]
final class TopicGridController extends AbstractContentElementController
{
    public const VARIANTS = ['home', 'overview', 'other'];

    public function __construct(
        private readonly TopicProvider $topics,
        private readonly SiteSettings $settings,
        private readonly Studio $studio,
    ) {
    }

    protected function getResponse(FragmentTemplate $template, ContentModel $model, Request $request): Response
    {
        $page = $this->getPageModel();

        $template->set('variant', \in_array($model->topicsVariant, self::VARIANTS, true) ? $model->topicsVariant : 'home');
        $template->set('title', (string) $model->introTitle);
        $template->set('text', (string) $model->introText);
        $template->set('note', (string) $model->introMeta);
        $template->set('link', ['href' => (string) $model->introPrimaryUrl, 'text' => (string) $model->introPrimaryText]);
        $template->set('topics', $this->topics->getTopics((int) $model->topicsParent, $page ? (int) $page->id : null));
        $template->set('site', $page ? $this->settings->forPage($page) : []);
        $template->set('cta', [
            'quote' => (string) $model->ctaQuote,
            'text' => (string) $model->ctaText,
            'link_text' => (string) $model->ctaLinkText,
        ]);
        $template->set('ornament', $model->ornamentImage
            ? $this->studio->createFigureBuilder()->fromUuid($model->ornamentImage)->buildIfResourceExists()
            : null);

        return $template->getResponse();
    }
}
