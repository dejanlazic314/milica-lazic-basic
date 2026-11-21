<?php

declare(strict_types=1);

namespace MilicaLazic\Twig;

use Contao\CoreBundle\Routing\PageFinder;
use MilicaLazic\Language\LanguageSwitcher;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * language_alternates(): the current page in every language (hreflang in the page
 * layout, SR/EN switch in the header). Empty with a single language.
 */
final class LanguageExtension extends AbstractExtension
{
    /** @var list<array{language: string, url: string, active: bool, translated: bool}>|null */
    private array|null $alternates = null;

    public function __construct(
        private readonly PageFinder $pageFinder,
        private readonly LanguageSwitcher $switcher,
    ) {
    }

    public function getFunctions(): array
    {
        return [new TwigFunction('language_alternates', $this->alternates(...))];
    }

    /**
     * @return list<array{language: string, url: string, active: bool, translated: bool}>
     */
    public function alternates(): array
    {
        if (null !== $this->alternates) {
            return $this->alternates;
        }

        $page = $this->pageFinder->getCurrentPage();

        return $this->alternates = $page ? $this->switcher->alternates($page) : [];
    }
}
