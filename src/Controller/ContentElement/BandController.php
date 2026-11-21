<?php

declare(strict_types=1);

namespace MilicaLazic\Controller\ContentElement;

use Contao\ContentModel;
use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsContentElement;
use Contao\CoreBundle\Image\Studio\Studio;
use Contao\CoreBundle\Twig\FragmentTemplate;
use MilicaLazic\Site\SiteSettings;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Rounded band with heading, text and one button.
 * Variant "online": white band with the apricot branch (#online on the home page).
 * Variant "cta": apricot "Prvi korak može biti jedan razgovor" at the end of subpages;
 * without a link the button goes to the contact page of the root page.
 */
#[AsContentElement('band', category: 'milica')]
final class BandController extends AbstractContentElementController
{
    public const VARIANTS = ['online', 'cta'];

    public function __construct(
        private readonly Studio $studio,
        private readonly SiteSettings $settings,
    ) {
    }

    protected function getResponse(FragmentTemplate $template, ContentModel $model, Request $request): Response
    {
        $page = $this->getPageModel();
        $href = (string) $model->introPrimaryUrl;

        if ('' === $href && $page) {
            $href = (string) ($this->settings->forPage($page)['contact_url'] ?? '');
        }

        $template->set('variant', \in_array($model->bandVariant, self::VARIANTS, true) ? $model->bandVariant : 'cta');
        $template->set('spacer', (bool) $model->bandSpacer);
        $template->set('title', (string) $model->introTitle);
        $template->set('text', (string) $model->introText);
        $template->set('button', ['href' => $href, 'text' => (string) $model->introPrimaryText]);
        $template->set('ornament', $model->ornamentImage
            ? $this->studio->createFigureBuilder()->fromUuid($model->ornamentImage)->buildIfResourceExists()
            : null);

        return $template->getResponse();
    }
}
