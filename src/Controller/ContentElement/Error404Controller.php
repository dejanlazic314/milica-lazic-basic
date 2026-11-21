<?php

declare(strict_types=1);

namespace MilicaLazic\Controller\ContentElement;

use Contao\ContentModel;
use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsContentElement;
use Contao\CoreBundle\Image\Studio\Studio;
use Contao\CoreBundle\Twig\FragmentTemplate;
use MilicaLazic\Topics\TopicProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * "Page not found" block: code line, H1, lead, two buttons, links to the topics
 * (subpages of the chosen page) and an illustration.
 */
#[AsContentElement('error_404', category: 'milica')]
final class Error404Controller extends AbstractContentElementController
{
    public function __construct(
        private readonly TopicProvider $topics,
        private readonly Studio $studio,
    ) {
    }

    protected function getResponse(FragmentTemplate $template, ContentModel $model, Request $request): Response
    {
        $template->set('code', (string) $model->eyebrow);
        $template->set('title', (string) $model->introTitle);
        $template->set('text', (string) $model->introText);
        $template->set('topics_title', (string) $model->introMeta);
        $template->set('buttons', [
            ['href' => (string) $model->introPrimaryUrl, 'text' => (string) $model->introPrimaryText, 'class' => 'btn btn-apricot'],
            ['href' => (string) $model->introSecondaryUrl, 'text' => (string) $model->introSecondaryText, 'class' => 'btn btn-outline'],
        ]);
        $template->set('topics', $model->topicsParent ? $this->topics->getTopics((int) $model->topicsParent) : []);
        $template->set('image', $model->ornamentImage
            ? $this->studio->createFigureBuilder()->fromUuid($model->ornamentImage)->buildIfResourceExists()
            : null);

        return $template->getResponse();
    }
}
