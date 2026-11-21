<?php

declare(strict_types=1);

namespace MilicaLazic\News;

use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\CoreBundle\Image\Studio\Figure;
use Contao\CoreBundle\Image\Studio\Studio;
use Contao\CoreBundle\Routing\ContentUrlGenerator;
use Contao\CoreBundle\Slug\Slug;
use Contao\NewsModel;
use Doctrine\DBAL\Connection;
use Symfony\Component\Routing\Exception\ExceptionInterface;

/**
 * Post cards ("Tekstovi") from Contao news: title, teaser, category chip, reading
 * time (from the text of the article's content elements) and optional image.
 *
 * @phpstan-type Post array{id: int, title: string, href: string, teaser: string, category: string, category_slug: string, featured: bool, minutes: int, figure: Figure|null}
 */
final class PostProvider
{
    private const WORDS_PER_MINUTE = 200;

    public function __construct(
        private readonly ContaoFramework $framework,
        private readonly ContentUrlGenerator $urlGenerator,
        private readonly Connection $connection,
        private readonly Studio $studio,
        private readonly Slug $slug,
    ) {
    }

    /**
     * @param list<int> $archiveIds
     *
     * @return list<Post>
     */
    public function getLatest(array $archiveIds, int $limit, int|null $excludeId = null): array
    {
        $posts = array_filter($this->getAll($archiveIds), static fn (array $p): bool => $p['id'] !== $excludeId);

        return \array_slice(array_values($posts), 0, $limit);
    }

    /**
     * All published posts, newest first. The blog is small, so filtering and paging
     * happen in PHP.
     *
     * @param list<int> $archiveIds
     *
     * @return list<Post>
     */
    public function getAll(array $archiveIds): array
    {
        $news = $archiveIds ? $this->framework->getAdapter(NewsModel::class)->findPublishedByPids($archiveIds) : null;
        $posts = [];

        foreach ($news ?? [] as $item) {
            try {
                $href = $this->urlGenerator->generate($item);
            } catch (ExceptionInterface) {
                continue;
            }

            $posts[] = [
                'id' => (int) $item->id,
                'title' => (string) $item->headline,
                'href' => $href,
                'teaser' => trim(strip_tags((string) $item->teaser)),
                'category' => (string) $item->category,
                'category_slug' => $this->categorySlug((string) $item->category),
                'featured' => (bool) $item->featured,
                'minutes' => $item->readingTime ? (int) $item->readingTime : $this->getReadingTime((int) $item->id),
                // A featured post shows its image (an illustration) uncropped in the featured box
                'figure' => $item->addImage && $item->singleSRC
                    ? $this->studio->createFigureBuilder()->fromUuid($item->singleSRC)->setSize($item->featured ? null : '_post_card')->buildIfResourceExists()
                    : null,
            ];
        }

        return $posts;
    }

    public function findByAlias(array $archiveIds, string $alias): NewsModel|null
    {
        return $archiveIds ? $this->framework->getAdapter(NewsModel::class)->findPublishedByParentAndIdOrAlias($alias, $archiveIds) : null;
    }

    public function categorySlug(string $category): string
    {
        return '' === $category ? '' : $this->slug->generate($category);
    }

    /**
     * Minutes to read the article, from the text of its visible content elements.
     */
    public function getReadingTime(int $newsId): int
    {
        $texts = $this->connection->fetchFirstColumn(
            "SELECT CONCAT_WS(' ', text, quote, introText) FROM tl_content WHERE ptable = 'tl_news' AND pid = ? AND invisible = 0",
            [$newsId],
        );

        $words = str_word_count(strip_tags(html_entity_decode(implode(' ', $texts))), 0, 'čćšžđČĆŠŽĐ');

        return max(1, (int) ceil($words / self::WORDS_PER_MINUTE));
    }
}
