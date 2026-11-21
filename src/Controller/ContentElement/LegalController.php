<?php

declare(strict_types=1);

namespace MilicaLazic\Controller\ContentElement;

use Contao\ContentModel;
use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsContentElement;
use Contao\CoreBundle\Twig\FragmentTemplate;
use MilicaLazic\Navigation\BreadcrumbBuilder;
use MilicaLazic\Site\SiteSettings;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * A whole legal page (privacy policy, cookies): narrow intro with the dates, then the
 * rich text with a table of contents built from its H2 headings (twig filter
 * "legal_text") and the privacy contact box from the root page.
 */
#[AsContentElement('legal', category: 'milica')]
final class LegalController extends AbstractContentElementController
{
    public function __construct(
        private readonly BreadcrumbBuilder $breadcrumb,
        private readonly SiteSettings $site,
    ) {
    }

    protected function getResponse(FragmentTemplate $template, ContentModel $model, Request $request): Response
    {
        $page = $this->getPageModel();

        $template->set('title', (string) $model->introTitle);
        $template->set('valid_from', $model->legalValidFrom ? (int) $model->legalValidFrom : null);
        $template->set('updated', $model->legalUpdated ? (int) $model->legalUpdated : null);
        $template->set('text', (string) $model->text);
        $template->set('breadcrumb', $page ? $this->breadcrumb->build($page) : []);
        $template->set('site', $page ? $this->site->forPage($page) : []);

        return $template->getResponse();
    }
}
