<?php

declare(strict_types=1);

namespace MilicaLazic\Controller\ContentElement;

use Contao\ContentModel;
use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsContentElement;
use Contao\CoreBundle\Twig\FragmentTemplate;
use Contao\StringUtil;
use MilicaLazic\News\PostProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Latest posts on the home page (#tekstovi): eyebrow, heading, "Svi tekstovi →"
 * link and three post cards from the selected news archives.
 */
#[AsContentElement('post_teasers', category: 'milica')]
final class PostTeasersController extends AbstractContentElementController
{
    public function __construct(private readonly PostProvider $posts)
    {
    }

    protected function getResponse(FragmentTemplate $template, ContentModel $model, Request $request): Response
    {
        $archives = array_map(intval(...), StringUtil::deserialize($model->newsArchives, true));

        $template->set('eyebrow', (string) $model->eyebrow);
        $template->set('title', (string) $model->introTitle);
        $template->set('link', ['href' => (string) $model->introPrimaryUrl, 'text' => (string) $model->introPrimaryText]);
        $template->set('posts', $this->posts->getLatest($archives, 3));

        return $template->getResponse();
    }
}
