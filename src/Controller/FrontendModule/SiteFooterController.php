<?php

declare(strict_types=1);

namespace MilicaLazic\Controller\FrontendModule;

use Contao\CoreBundle\Controller\FrontendModule\AbstractFrontendModuleController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsFrontendModule;
use Contao\CoreBundle\Twig\FragmentTemplate;
use Contao\ModuleModel;
use Contao\StringUtil;
use MilicaLazic\Navigation\NavigationBuilder;
use MilicaLazic\Site\SiteSettings;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Footer: brand, hand-picked pages (module setting "pages"), contact details from
 * the root page, crisis numbers and the legal links. Goes into the "footer" slot.
 */
#[AsFrontendModule('site_footer', category: 'milica')]
final class SiteFooterController extends AbstractFrontendModuleController
{
    public function __construct(
        private readonly NavigationBuilder $navigation,
        private readonly SiteSettings $settings,
    ) {
    }

    protected function getResponse(FragmentTemplate $template, ModuleModel $model, Request $request): Response
    {
        $page = $this->getPageModel();

        if (null === $page) {
            return new Response();
        }

        $template->set('site', $this->settings->forPage($page));
        $template->set('items', $this->navigation->buildFromIds(StringUtil::deserialize($model->pages, true), $page));
        $template->set('legal_items', $this->navigation->buildFromIds(StringUtil::deserialize($model->legalPages, true), $page));
        $template->set('year', date('Y'));

        return $template->getResponse();
    }
}
