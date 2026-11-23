<?php

declare(strict_types=1);

namespace MilicaLazic\Controller\ContentElement;

use Contao\ContentModel;
use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsContentElement;
use Contao\CoreBundle\InsertTag\InsertTagParser;
use Contao\CoreBundle\Twig\FragmentTemplate;
use Contao\FilesModel;
use Contao\StringUtil;
use MilicaLazic\Site\SiteSettings;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Interactive "small experiment" (variant A or B). The texts come from this element and
 * its nested "exercise_topic" elements; the template renders every step, and
 * public/layout/js/elements/exercise.js shows them one by one.
 * Its CSS and JS (elements/exercise.css, elements/exercise.js) come with the element.
 * Set the anchor ID "vezba" on the element the menu links to ("/#vezba").
 */
#[AsContentElement('exercise', category: 'milica', nestedFragments: ['allowedTypes' => ['exercise_topic']])]
final class ExerciseController extends AbstractContentElementController
{
    public const VARIANTS = ['a', 'b'];

    public function __construct(
        private readonly SiteSettings $settings,
        private readonly InsertTagParser $insertTags,
    ) {
    }

    protected function getResponse(FragmentTemplate $template, ContentModel $model, Request $request): Response
    {
        $page = $this->getPageModel();
        $site = $page ? $this->settings->forPage($page) : [];

        $template->set('variant', \in_array($model->exerciseVariant, self::VARIANTS, true) ? $model->exerciseVariant : 'a');
        $template->set('content', [
            'eyebrow' => $this->text($model->eyebrow),
            'title' => $this->text($model->introTitle),
            'text' => $this->text($model->introText),
            'endEyebrow' => $this->text($model->exerciseEndEyebrow),
            'endTitle' => $this->text($model->exerciseEndTitle),
            'contactUrl' => $this->url((string) $model->introPrimaryUrl ?: ($site['contact_url'] ?? '')),
            'contactText' => $this->text($model->introPrimaryText),
        ]);
        $template->set('topics', $this->getTopics($model));

        return $template->getResponse();
    }

    /**
     * @return list<array{choice: string, image: string, steps: list<string>, reflection: string, link: string, linkText: string}>
     */
    private function getTopics(ContentModel $model): array
    {
        $topics = [];
        $children = $this->getContaoAdapter(ContentModel::class)->findPublishedByPidAndTable((int) $model->id, 'tl_content');

        foreach ($children ?? [] as $child) {
            if ('exercise_topic' !== $child->type) {
                continue;
            }

            $image = $child->introImage ? $this->getContaoAdapter(FilesModel::class)->findByUuid($child->introImage) : null;
            $steps = array_values(array_filter(array_map($this->text(...), StringUtil::deserialize($child->listitems, true))));

            $topics[] = [
                'choice' => $this->text($child->introTitle),
                'image' => $image ? '/'.$image->path : '',
                'steps' => $steps,
                'reflection' => $this->text($child->introText),
                'link' => $this->url((string) $child->introPrimaryUrl),
                'linkText' => $this->text($child->introPrimaryText),
            ];
        }

        return $topics;
    }

    // Plain text (Contao stores entities); the template escapes it once
    private function text(mixed $value): string
    {
        return trim(StringUtil::decodeEntities((string) $value));
    }

    // Links may be insert tags ({{link_url::20}}) from the link picker
    private function url(string $value): string
    {
        return $this->insertTags->replaceInline(StringUtil::decodeEntities($value));
    }
}
