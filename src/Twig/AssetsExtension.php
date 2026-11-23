<?php

declare(strict_types=1);

namespace MilicaLazic\Twig;

use Contao\CoreBundle\Twig\Extension\ContaoExtension;
use Contao\CoreBundle\Twig\ResponseContext\DocumentLocation;
use Symfony\Component\Asset\Packages;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * add_assets('elements/faq_list') puts public/layout/css/elements/faq_list.css into the
 * <head> and public/layout/js/elements/faq_list.js at the end of the <body>, if the files
 * exist, once per page. Every content element calls it with its type
 * (templates/content_element/_base.html.twig), so an element gets its CSS and JS just by
 * having files with its name.
 *
 * It uses the same call as Twig's {% add %} tag. The tag needs a fixed name, this function
 * builds the name from the element type.
 */
final class AssetsExtension extends AbstractExtension
{
    /** @var array<string, bool> */
    private array $exists = [];

    public function __construct(
        private readonly Packages $packages,
        #[Autowire('%kernel.project_dir%/public')]
        private readonly string $publicDir,
    ) {
    }

    public function getFunctions(): array
    {
        return [new TwigFunction('add_assets', $this->addAssets(...), ['needs_environment' => true])];
    }

    public function addAssets(Environment $twig, string $name): void
    {
        $contao = $twig->getExtension(ContaoExtension::class);

        if ($css = $this->url("layout/css/$name.css")) {
            $contao->addDocumentContent("css:$name", \sprintf('<link rel="stylesheet" href="%s">', $css), DocumentLocation::stylesheets);
        }

        if ($js = $this->url("layout/js/$name.js")) {
            $contao->addDocumentContent("js:$name", \sprintf('<script type="module" src="%s"></script>', $js), DocumentLocation::endOfBody);
        }
    }

    // Versioned URL (?v=…, like asset() in Twig) of a file in public/, or null if it does not exist
    private function url(string $path): string|null
    {
        $this->exists[$path] ??= is_file($this->publicDir.'/'.$path);

        return $this->exists[$path] ? htmlspecialchars($this->packages->getUrl($path)) : null;
    }
}
