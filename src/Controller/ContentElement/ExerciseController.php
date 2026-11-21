<?php

declare(strict_types=1);

namespace MilicaLazic\Controller\ContentElement;

use Contao\ContentModel;
use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsContentElement;
use Contao\CoreBundle\Twig\FragmentTemplate;
use Contao\FilesModel;
use MilicaLazic\Site\SiteSettings;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Interactive "small experiment" (variant A or B). The section is empty markup;
 * public/layout/js/exercise.js fills it (texts are in the script). exercise.css and
 * exercise.js are added to the page only where this element is used.
 * Set the anchor ID "vezba" on the element the menu links to ("/#vezba").
 */
#[AsContentElement('exercise', category: 'milica')]
final class ExerciseController extends AbstractContentElementController
{
    public const VARIANTS = ['a', 'b'];

    public function __construct(private readonly SiteSettings $settings)
    {
    }

    protected function getResponse(FragmentTemplate $template, ContentModel $model, Request $request): Response
    {
        $page = $this->getPageModel();
        $site = $page ? $this->settings->forPage($page) : [];
        $folder = $model->exerciseFolder ? $this->getContaoAdapter(FilesModel::class)->findByUuid($model->exerciseFolder) : null;

        $template->set('variant', \in_array($model->exerciseVariant, self::VARIANTS, true) ? $model->exerciseVariant : 'a');
        $template->set('images_url', $folder ? '/'.rtrim($folder->path, '/').'/' : '');
        $template->set('contact_url', (string) $model->introPrimaryUrl ?: ($site['contact_url'] ?? ''));

        return $template->getResponse();
    }
}
