<?php

declare(strict_types=1);

namespace MilicaLazic\Controller\ContentElement;

use Contao\ContentModel;
use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsContentElement;
use Contao\CoreBundle\Twig\FragmentTemplate;
use Contao\FaqCategoryModel;
use Contao\FaqModel;
use Contao\StringUtil;
use MilicaLazic\Site\SiteSettings;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Questions from the FAQ module (Contao FAQ bundle), rendered as <details>.
 * Variant "home": featured question box + the questions marked "show on home page"
 * (#pitanja). Variant "page": one group per category with its anchor, the
 * "Niste našli odgovor?" box and FAQPage JSON-LD. Variant "inline": only the questions,
 * inside a section or text part (topic pages, Geštalt, posts), also with FAQPage JSON-LD.
 */
#[AsContentElement('faq_list', category: 'milica')]
final class FaqListController extends AbstractContentElementController
{
    public const VARIANTS = ['home', 'page', 'inline'];

    public function __construct(private readonly SiteSettings $settings)
    {
    }

    protected function getResponse(FragmentTemplate $template, ContentModel $model, Request $request): Response
    {
        $variant = \in_array($model->faqVariant, self::VARIANTS, true) ? $model->faqVariant : 'home';
        $categoryIds = array_map(intval(...), StringUtil::deserialize($model->faqCategories, true));
        $page = $this->getPageModel();

        $template->set('variant', $variant);
        $template->set('eyebrow', (string) $model->eyebrow);
        $template->set('title', (string) $model->introTitle);
        $template->set('featured', [
            'title' => (string) $model->featuredTitle,
            'text' => (string) $model->text,
            'link' => ['href' => (string) $model->introPrimaryUrl, 'text' => (string) $model->introPrimaryText],
        ]);
        $template->set('groups', $this->getGroups($categoryIds, 'home' === $variant));
        $template->set('site', $page ? $this->settings->forPage($page) : []);

        return $template->getResponse();
    }

    /**
     * @param list<int> $categoryIds in the order chosen in the element
     *
     * @return list<array{title: string, anchor: string, questions: list<array{question: string, answer: string}>}>
     */
    private function getGroups(array $categoryIds, bool $homeOnly): array
    {
        $groups = [];
        $faqAdapter = $this->getContaoAdapter(FaqModel::class);
        $categoryAdapter = $this->getContaoAdapter(FaqCategoryModel::class);

        foreach ($categoryIds as $id) {
            $category = $categoryAdapter->findById($id);
            $faqs = $faqAdapter->findPublishedByPid($id);

            if (null === $category || null === $faqs) {
                continue;
            }

            $questions = [];

            foreach ($faqs as $faq) {
                if (!$homeOnly || $faq->showOnHome) {
                    $questions[] = ['question' => (string) $faq->question, 'answer' => (string) $faq->answer];
                }
            }

            if ($questions) {
                $groups[] = ['title' => (string) ($category->headline ?: $category->title), 'anchor' => (string) $category->anchor, 'questions' => $questions];
            }
        }

        return $groups;
    }
}
