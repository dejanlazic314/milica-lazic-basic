<?php

declare(strict_types=1);

namespace MilicaLazic\Language;

use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\CoreBundle\Routing\ContentUrlGenerator;
use Contao\Input;
use Contao\NewsArchiveModel;
use Contao\NewsModel;
use Contao\PageModel;
use Symfony\Component\Routing\Exception\ExceptionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * The same page in the other languages, for the SR/EN switch and hreflang.
 *
 * One root page per language on the same domain (SR is the fallback root without a
 * URL prefix, EN has the prefix "en"). A translated page points to its Serbian page
 * with "languageMain" (tl_page, tl_news). Without a translation the switch leads to
 * the home page of that language. With a single language the list is empty.
 */
final class LanguageSwitcher
{
    public function __construct(
        private readonly ContaoFramework $framework,
        private readonly ContentUrlGenerator $urlGenerator,
    ) {
    }

    /**
     * @return list<array{language: string, url: string, active: bool, translated: bool}>
     */
    public function alternates(PageModel $page): array
    {
        $pageAdapter = $this->framework->getAdapter(PageModel::class);
        $currentRoot = $pageAdapter->findById($page->rootId);

        if (null === $currentRoot) {
            return [];
        }

        $roots = $pageAdapter->findBy(
            ["type = 'root'", 'published = 1', 'dns = ?'],
            [(string) $currentRoot->dns],
            ['order' => 'fallback DESC, sorting'],
        );

        if (null === $roots || $roots->count() < 2) {
            return [];
        }

        $news = $this->currentNews($page);
        $mainPage = $this->mainPage($page, $currentRoot);
        $mainNews = $news ? ($news->languageMain ? NewsModel::findById($news->languageMain) : $news) : null;
        $list = [];

        foreach ($roots as $root) {
            if ((int) $root->id === (int) $currentRoot->id) {
                $url = $this->url($news ?? $page);
                $list[] = ['language' => (string) $root->language, 'url' => $url ?? '', 'active' => true, 'translated' => true];
                continue;
            }

            $target = $mainPage ? $this->pageIn($root, $mainPage) : null;
            $targetNews = $mainNews ? $this->newsIn($root, $mainNews) : null;
            $url = $targetNews ? $this->url($targetNews) : null;
            $url ??= $target && !$news ? $this->url($target) : null;
            $translated = null !== $url;
            $url ??= $this->url($pageAdapter->findFirstPublishedRegularByPid($root->id));

            if (null !== $url) {
                $list[] = ['language' => (string) $root->language, 'url' => $url, 'active' => false, 'translated' => $translated];
            }
        }

        return $list;
    }

    /** The Serbian (fallback) page: the page itself in the fallback root, else its languageMain */
    private function mainPage(PageModel $page, PageModel $root): PageModel|null
    {
        if ($root->fallback) {
            return $page;
        }

        return $page->languageMain ? PageModel::findById($page->languageMain) : null;
    }

    /** The page of $root that translates $main (or $main itself in the fallback root) */
    private function pageIn(PageModel $root, PageModel $main): PageModel|null
    {
        if ($root->fallback) {
            return (int) $main->rootId === (int) $root->id || null === $main->rootId ? $main : null;
        }

        foreach (PageModel::findBy(['languageMain = ?', 'published = 1'], [(int) $main->id]) ?? [] as $candidate) {
            $candidate->loadDetails();

            if ((int) $candidate->rootId === (int) $root->id) {
                return $candidate;
            }
        }

        return null;
    }

    private function newsIn(PageModel $root, NewsModel $main): NewsModel|null
    {
        if ($root->fallback) {
            return $main;
        }

        foreach (NewsModel::findBy(['languageMain = ?', 'published = 1'], [(int) $main->id]) ?? [] as $candidate) {
            $reader = PageModel::findWithDetails((int) NewsArchiveModel::findById($candidate->pid)?->jumpTo);

            if ($reader && (int) $reader->rootId === (int) $root->id) {
                return $candidate;
            }
        }

        return null;
    }

    /** The post shown by the reader on this page (/tekstovi/{alias}), if any */
    private function currentNews(PageModel $page): NewsModel|null
    {
        $alias = Input::get('auto_item', false, true);

        if (!$alias) {
            return null;
        }

        $archives = NewsArchiveModel::findBy('jumpTo', (int) $page->id);
        $ids = $archives ? $archives->fetchEach('id') : [];

        return $ids ? NewsModel::findPublishedByParentAndIdOrAlias((string) $alias, $ids) : null;
    }

    private function url(PageModel|NewsModel|null $content): string|null
    {
        if (null === $content) {
            return null;
        }

        try {
            return $this->urlGenerator->generate($content, [], UrlGeneratorInterface::ABSOLUTE_URL);
        } catch (ExceptionInterface) {
            return null;
        }
    }
}
