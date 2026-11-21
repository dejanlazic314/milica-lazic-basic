<?php

declare(strict_types=1);

namespace MilicaLazic\Navigation;

use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\CoreBundle\Routing\ContentUrlGenerator;
use Contao\PageModel;
use Symfony\Component\Routing\Exception\ExceptionInterface;

/**
 * Breadcrumb items from the home page down to the current page. Hidden pages
 * (e.g. Kontakt) are included; the home page itself gets no breadcrumb.
 */
final class BreadcrumbBuilder
{
    public function __construct(
        private readonly ContaoFramework $framework,
        private readonly ContentUrlGenerator $urlGenerator,
    ) {
    }

    /**
     * @return list<array{title: string, href: string|null}> the last item is the current page (href null)
     */
    public function build(PageModel $currentPage, string|null $currentTitle = null): array
    {
        $pageAdapter = $this->framework->getAdapter(PageModel::class);
        $home = $pageAdapter->findFirstPublishedRegularByPid($currentPage->rootId);

        if (null === $home || (int) $home->id === (int) $currentPage->id) {
            return [];
        }

        $items = [['title' => (string) $home->title, 'href' => $this->url($home)]];

        // trail: root, ancestors, current page
        foreach (\array_slice((array) $currentPage->trail, 1, -1) as $id) {
            $page = $pageAdapter->findPublishedById($id);

            if (null !== $page && (int) $page->id !== (int) $home->id) {
                $items[] = ['title' => (string) $page->title, 'href' => $this->url($page)];
            }
        }

        $items[] = ['title' => $currentTitle ?? (string) $currentPage->title, 'href' => null];

        return $items;
    }

    private function url(PageModel $page): string|null
    {
        try {
            return $this->urlGenerator->generate($page);
        } catch (ExceptionInterface) {
            return null;
        }
    }
}
