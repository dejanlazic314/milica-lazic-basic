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
 * Cards of the subpages of a page, plus the contact card ("Ne vidite svoju temu ovde?",
 * its own fields; not shown while empty). Styles:
 * - "large": cards with illustration, title and text; heading, lead and corner ornament;
 * - "framed": the same cards with a white frame, a note and a link below;
 * - "small": illustration and title only, four in a row, without the current page.
 */
#[AsContentElement('page_cards', category: 'milica')]
final class PageCardsController extends AbstractContentElementController
{
    public const STYLES = ['large', 'framed', 'small'];

    public function __construct(
        private readonly TopicProvider $topics,
        private readonly SiteSettings $settings,
        private readonly Studio $studio,
    ) {
    }

    protected function getResponse(FragmentTemplate $template, ContentModel $model, Request $request): Response
    {
        $page = $this->getPageModel();

        $template->set('style', \in_array($model->pageCardsStyle, self::STYLES, true) ? $model->pageCardsStyle : 'large');
        $template->set('title', (string) $model->introTitle);
        $template->set('text', (string) $model->introText);
        $template->set('note', (string) $model->introMeta);
        $template->set('link', ['href' => (string) $model->introPrimaryUrl, 'text' => (string) $model->introPrimaryText]);
        $template->set('subpages', $this->topics->getTopics((int) $model->topicsParent, $page ? (int) $page->id : null));
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
