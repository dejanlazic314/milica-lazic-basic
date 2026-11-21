<?php

declare(strict_types=1);

namespace MilicaLazic\Navigation;

use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\CoreBundle\Routing\ContentUrlGenerator;
use Contao\PageModel;
use Symfony\Component\Routing\Exception\ExceptionInterface;

/**
 * Builds the page tree for the header and footer menus as plain arrays, so the
 * Twig templates can render the exact markup of the design.
 *
 * Skips pages hidden from navigation and protected pages (the site has no members).
 * Publishing, start/stop dates and preview mode are handled by the PageModel finder.
 */
final class NavigationBuilder
{
    public function __construct(
        private readonly ContaoFramework $framework,
        private readonly ContentUrlGenerator $urlGenerator,
    ) {
    }

    /**
     * @return list<NavItem>
     */
    public function build(PageModel $currentPage, int $levels = 2): array
    {
        $trail = array_map(intval(...), (array) $currentPage->trail);

        return $this->getItems((int) $currentPage->rootId, (int) $currentPage->id, $trail, $levels);
    }

    /**
     * Flat list of hand-picked pages in the given order (footer).
     *
     * @param list<int|string> $ids
     *
     * @return list<NavItem>
     */
    public function buildFromIds(array $ids, PageModel $currentPage): array
    {
        $ids = array_values(array_filter(array_map(intval(...), $ids)));
        $pages = $ids ? $this->framework->getAdapter(PageModel::class)->findPublishedRegularByIds($ids) : null;

        if (null === $pages) {
            return [];
        }

        $byId = [];

        foreach ($pages as $page) {
            try {
                $href = $this->urlGenerator->generate($page);
            } catch (ExceptionInterface) {
                continue;
            }

            $byId[(int) $page->id] = new NavItem(
                title: (string) $page->title,
                href: $href,
                subtitle: (string) $page->navSubtitle,
                isActive: (int) $page->id === (int) $currentPage->id,
                isTrail: false,
                mobileOnly: (bool) $page->navMobileOnly,
                children: [],
            );
        }

        return array_values(array_filter(array_map(static fn (int $id) => $byId[$id] ?? null, $ids)));
    }

    /**
     * @param list<int> $trail
     *
     * @return list<NavItem>
     */
    private function getItems(int $pid, int $currentId, array $trail, int $levels): array
    {
        $pages = $this->framework->getAdapter(PageModel::class)->findPublishedRegularByPid($pid);

        if (null === $pages) {
            return [];
        }

        $items = [];

        foreach ($pages as $page) {
            if ($page->hide || $page->protected) {
                continue;
            }

            try {
                $href = $this->urlGenerator->generate($page);
            } catch (ExceptionInterface) {
                continue;
            }

            $id = (int) $page->id;

            $items[] = new NavItem(
                title: (string) $page->title,
                href: $href,
                subtitle: (string) $page->navSubtitle,
                isActive: $id === $currentId,
                isTrail: $id !== $currentId && \in_array($id, $trail, true),
                mobileOnly: (bool) $page->navMobileOnly,
                children: $levels > 1 ? $this->getItems($id, $currentId, $trail, $levels - 1) : [],
            );
        }

        return $items;
    }
}
