<?php

declare(strict_types=1);

namespace MilicaLazic\Topics;

use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\CoreBundle\Image\Studio\Figure;
use Contao\CoreBundle\Image\Studio\Studio;
use Contao\CoreBundle\Routing\ContentUrlGenerator;
use Contao\PageModel;
use Symfony\Component\Routing\Exception\ExceptionInterface;

/**
 * Topic cards = published subpages of a page ("Oblasti rada"), with the teaser
 * fields of each page (contao/dca/tl_page.php). Used by the topic grid, the
 * "Druge teme" block on topic pages and the 404 page.
 */
final class TopicProvider
{
    public function __construct(
        private readonly ContaoFramework $framework,
        private readonly ContentUrlGenerator $urlGenerator,
        private readonly Studio $studio,
    ) {
    }

    /**
     * @return list<array{id: int, title: string, href: string, text: string, figure: Figure|null}>
     */
    public function getTopics(int $parentId, int|null $excludeId = null): array
    {
        $pages = $this->framework->getAdapter(PageModel::class)->findPublishedRegularByPid($parentId);

        if (null === $pages) {
            return [];
        }

        $topics = [];

        foreach ($pages as $page) {
            if ($page->protected || (int) $page->id === $excludeId) {
                continue;
            }

            try {
                $href = $this->urlGenerator->generate($page);
            } catch (ExceptionInterface) {
                continue;
            }

            $topics[] = [
                'id' => (int) $page->id,
                'title' => (string) $page->title,
                'href' => $href,
                'text' => (string) $page->teaserText,
                'figure' => $page->teaserImage
                    ? $this->studio->createFigureBuilder()->fromUuid($page->teaserImage)->buildIfResourceExists()
                    : null,
            ];
        }

        return $topics;
    }
}
