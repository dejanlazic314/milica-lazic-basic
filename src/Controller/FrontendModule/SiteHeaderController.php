<?php

declare(strict_types=1);

namespace MilicaLazic\Controller\FrontendModule;

use Contao\CoreBundle\Controller\FrontendModule\AbstractFrontendModuleController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsFrontendModule;
use Contao\CoreBundle\Twig\FragmentTemplate;
use Contao\ModuleModel;
use MilicaLazic\Navigation\NavigationBuilder;
use MilicaLazic\Site\SiteSettings;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Info bar, sticky header with the main menu and submenu, and the mobile menu
 * (header variant E of the design). Goes into the "header" slot of the layout.
 */
#[AsFrontendModule('site_header', category: 'milica')]
final class SiteHeaderController extends AbstractFrontendModuleController
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
        $template->set('items', $this->navigation->build($page));

        return $template->getResponse();
    }
}
