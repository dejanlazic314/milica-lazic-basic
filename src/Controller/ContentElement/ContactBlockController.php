<?php

declare(strict_types=1);

namespace MilicaLazic\Controller\ContentElement;

use Contao\ContentModel;
use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsContentElement;
use Contao\CoreBundle\Twig\FragmentTemplate;
use MilicaLazic\Site\SiteSettings;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Apricot contact block (#kontakt): heading, text, contact rows from the root page
 * and the Google map behind consent (cookies.js loads it). The form goes inside as
 * a nested element ("Form") and renders on the right.
 */
#[AsContentElement('contact_block', category: 'milica', nestedFragments: true)]
final class ContactBlockController extends AbstractContentElementController
{
    public function __construct(private readonly SiteSettings $settings)
    {
    }

    protected function getResponse(FragmentTemplate $template, ContentModel $model, Request $request): Response
    {
        $page = $this->getPageModel();

        $template->set('style', \in_array($model->sectionStyle, SectionController::STYLES, true) ? $model->sectionStyle : null);
        $template->set('eyebrow', (string) $model->eyebrow);
        $template->set('title', (string) $model->introTitle);
        $template->set('text', (string) $model->introText);
        $template->set('map', ['embed' => (string) $model->mapEmbedUrl, 'link' => (string) $model->mapLinkUrl]);
        $template->set('site', $page ? $this->settings->forPage($page) : []);

        return $template->getResponse();
    }
}
