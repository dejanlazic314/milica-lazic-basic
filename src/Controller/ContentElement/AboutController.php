<?php

declare(strict_types=1);

namespace MilicaLazic\Controller\ContentElement;

use Contao\ContentModel;
use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsContentElement;
use Contao\CoreBundle\Image\Studio\Figure;
use Contao\CoreBundle\Image\Studio\Studio;
use Contao\CoreBundle\Twig\FragmentTemplate;
use MilicaLazic\Navigation\BreadcrumbBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * "O meni" block (".about"). Variant "home": eyebrow, H2, quote, text,
 * credentials, button, ornament (#o-meni on the home page). Variant "intro": top
 * of the page "O meni" with breadcrumb, H1 and two buttons.
 * Portrait: transparent PNG in front of the sage circle (image size "_portrait").
 */
#[AsContentElement('about', category: 'milica')]
final class AboutController extends AbstractContentElementController
{
    public const VARIANTS = ['home', 'intro'];

    public function __construct(
        private readonly Studio $studio,
        private readonly BreadcrumbBuilder $breadcrumb,
    ) {
    }

    protected function getResponse(FragmentTemplate $template, ContentModel $model, Request $request): Response
    {
        $variant = \in_array($model->aboutVariant, self::VARIANTS, true) ? $model->aboutVariant : 'home';
        $page = $this->getPageModel();

        $template->set('variant', $variant);
        $template->set('eyebrow', (string) $model->eyebrow);
        $template->set('title', (string) $model->introTitle);
        $template->set('quote', (string) $model->quote);
        $template->set('text', (string) $model->introText);
        $template->set('credentials', $this->parseCredentials((string) $model->credentials));
        $template->set('buttons', array_values(array_filter([
            ['href' => (string) $model->introPrimaryUrl, 'text' => (string) $model->introPrimaryText, 'class' => 'home' === $variant ? 'btn btn-dark' : 'btn btn-apricot'],
            ['href' => (string) $model->introSecondaryUrl, 'text' => (string) $model->introSecondaryText, 'class' => 'btn btn-outline'],
        ], static fn (array $b): bool => '' !== $b['href'] && '' !== $b['text'])));
        $template->set('portrait', $this->figure($model->introImage, '_portrait'));
        $template->set('ornament', 'home' === $variant ? $this->figure($model->ornamentImage) : null);
        $template->set('breadcrumb', 'intro' === $variant && $page ? $this->breadcrumb->build($page) : []);

        return $template->getResponse();
    }

    /**
     * One "Label: value" pair per line, e.g. "Edukacija: Geštalt institut".
     *
     * @return list<array{label: string, value: string}>
     */
    private function parseCredentials(string $text): array
    {
        $items = [];

        foreach (preg_split('/\R/', $text) ?: [] as $line) {
            if (2 === \count($parts = explode(':', $line, 2)) && '' !== trim($parts[0])) {
                $items[] = ['label' => trim($parts[0]), 'value' => trim($parts[1])];
            }
        }

        return $items;
    }

    private function figure(string|null $uuid, string|null $size = null): Figure|null
    {
        if (!$uuid) {
            return null;
        }

        $builder = $this->studio->createFigureBuilder()->fromUuid($uuid);

        if (null !== $size) {
            $builder->setSize($size);
        }

        return $builder->buildIfResourceExists();
    }
}
