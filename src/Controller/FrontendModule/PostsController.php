<?php

declare(strict_types=1);

namespace MilicaLazic\Controller\FrontendModule;

use Contao\ContentModel;
use Contao\CoreBundle\Controller\FrontendModule\AbstractFrontendModuleController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsFrontendModule;
use Contao\CoreBundle\Exception\PageNotFoundException;
use Contao\CoreBundle\Image\Studio\Studio;
use Contao\CoreBundle\Routing\ResponseContext\HtmlHeadBag\HtmlHeadBag;
use Contao\CoreBundle\Routing\ResponseContext\ResponseContextAccessor;
use Contao\CoreBundle\Twig\FragmentTemplate;
use Contao\Input;
use Contao\ModuleModel;
use Contao\NewsModel;
use Contao\PageModel;
use Contao\StringUtil;
use MilicaLazic\Navigation\BreadcrumbBuilder;
use MilicaLazic\News\PostProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Page "Tekstovi". Without an alias in the URL: intro with the category filter
 * (?kategorija=… / ?category=…), the featured post, the cards and a "load more" link
 * (?strana=2 / ?page=2). Parameter names and texts: translations/site.*.yaml.
 * With an alias (/tekstovi/{alias}): the post with author line, summary, text,
 * bio and three related posts.
 */
#[AsFrontendModule('posts', category: 'milica', template: 'frontend_module/posts')]
final class PostsController extends AbstractFrontendModuleController
{
    public function __construct(
        private readonly PostProvider $posts,
        private readonly BreadcrumbBuilder $breadcrumb,
        private readonly Studio $studio,
        private readonly ResponseContextAccessor $responseContext,
        private readonly TranslatorInterface $translator,
    ) {
    }

    protected function getResponse(FragmentTemplate $template, ModuleModel $model, Request $request): Response
    {
        $page = $this->getPageModel();
        $archiveIds = array_map(intval(...), StringUtil::deserialize($model->news_archives, true));
        $alias = $this->getContaoAdapter(Input::class)->get('auto_item');

        $template->set('page_title', $page ? (string) $page->title : '');
        $template->set('intro', (string) $model->postsIntro);

        if (null !== $alias && '' !== $alias) {
            $news = $this->posts->findByAlias($archiveIds, (string) $alias);

            if (null === $news || !$page) {
                throw new PageNotFoundException('Post not found: '.$alias);
            }

            return $this->renderPost($template, $model, $news, $page, $archiveIds);
        }

        return $this->renderList($template, $model, $request, $page, $archiveIds);
    }

    /**
     * @param list<int> $archiveIds
     */
    private function renderList(FragmentTemplate $template, ModuleModel $model, Request $request, PageModel|null $page, array $archiveIds): Response
    {
        $all = $this->posts->getAll($archiveIds);
        $featured = null;

        foreach ($all as $i => $post) {
            if ($post['featured']) {
                $featured = $post;
                unset($all[$i]);
                break;
            }
        }

        // Categories in the order of the number of posts
        $counts = [];

        foreach ($all as $post) {
            if ('' !== $post['category']) {
                $counts[$post['category']] = ($counts[$post['category']] ?? 0) + 1;
            }
        }

        arsort($counts);

        $current = (string) $request->query->get($this->param('category'), '');
        $categories = [];

        foreach (array_keys($counts) as $name) {
            $categories[] = ['title' => $name, 'slug' => $this->posts->categorySlug($name)];
        }

        if ('' !== $current) {
            $all = array_filter($all, static fn (array $p): bool => $p['category_slug'] === $current);
        }

        $all = array_values($all);
        $perPage = max(1, (int) $model->perPage ?: 9);
        $pageNo = max(1, $request->query->getInt($this->param('page'), 1));
        $shown = \array_slice($all, 0, $perPage * $pageNo);

        $template->set('mode', 'list');
        $template->set('breadcrumb', $page ? $this->breadcrumb->build($page) : []);
        $template->set('categories', $categories);
        $template->set('current_category', $current);
        $template->set('featured', '' === $current ? $featured : null);
        $template->set('items', $shown);
        $template->set('total', \count($all));
        $template->set('next_page', \count($all) > \count($shown) ? $pageNo + 1 : null);

        return $template->getResponse();
    }

    /**
     * @param list<int> $archiveIds
     */
    private function renderPost(FragmentTemplate $template, ModuleModel $model, NewsModel $news, PageModel $page, array $archiveIds): Response
    {
        $category = (string) $news->category;
        $crumbs = $this->breadcrumb->build($page, (string) $news->headline);

        // Početna › Tekstovi › {category} › post
        if ('' !== $category && \count($crumbs) >= 2) {
            $listHref = $crumbs[\count($crumbs) - 2]['href'] ?? null;
            array_splice($crumbs, -1, 0, [['title' => $category, 'href' => $listHref ? $listHref.'?'.$this->param('category').'='.$this->posts->categorySlug($category) : null]]);
        }

        $elements = $this->getContaoAdapter(ContentModel::class)->findPublishedByPidAndTable((int) $news->id, 'tl_news');
        $bioPage = $model->postsBioPage ? $this->getContaoAdapter(PageModel::class)->findPublishedById((int) $model->postsBioPage) : null;
        $figure = fn (string $size) => $model->postsBioImage
            ? $this->studio->createFigureBuilder()->fromUuid($model->postsBioImage)->setSize($size)->buildIfResourceExists()
            : null;

        $template->set('mode', 'post');
        $template->set('breadcrumb', $crumbs);
        $template->set('post', [
            'title' => (string) $news->headline,
            'date' => (int) $news->date,
            'updated' => $news->dateUpdated ? (int) $news->dateUpdated : null,
            'minutes' => $news->readingTime ? (int) $news->readingTime : $this->posts->getReadingTime((int) $news->id),
            'summary' => (string) $news->summary,
            'category' => $category,
            'elements' => $elements ? array_map(intval(...), $elements->fetchEach('id')) : [],
        ]);
        $template->set('author', [
            'name' => StringUtil::decodeEntities((string) $page->rootPageTitle),
            'href' => $bioPage ? $this->generateContentUrl($bioPage) : null,
            'figure' => $figure('_portrait_author'),
        ]);
        $template->set('bio', [
            'text' => (string) $model->postsBio,
            'link' => ['href' => $bioPage ? $this->generateContentUrl($bioPage) : '', 'text' => $this->translator->trans('bio.more', [], 'site')],
            'figure' => $figure('_portrait_bio'),
        ]);
        $template->set('related', $this->posts->getLatest($archiveIds, 3, (int) $news->id));

        // Title and description of the post instead of the list page
        if ($headBag = $this->responseContext->getResponseContext()?->get(HtmlHeadBag::class)) {
            $headBag->setTitle((string) ($news->pageTitle ?: $news->headline));
            $headBag->setMetaDescription(trim(strip_tags((string) ($news->description ?: $news->teaser))));
        }

        return $template->getResponse();
    }

    /** Query parameter in the language of the page: kategorija/category, strana/page */
    private function param(string $name): string
    {
        return $this->translator->trans('post.param_'.$name, [], 'site');
    }
}
