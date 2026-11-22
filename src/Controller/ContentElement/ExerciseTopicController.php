<?php

declare(strict_types=1);

namespace MilicaLazic\Controller\ContentElement;

use Contao\ContentModel;
use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsContentElement;
use Contao\CoreBundle\Twig\FragmentTemplate;
use Contao\StringUtil;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * One topic of the exercise (goes inside an "exercise" element): the choice, its
 * illustration, the steps, the reflection at the end and a link to the topic page.
 * The exercise element reads these fields itself (ExerciseController); this element
 * only renders a preview in the backend.
 */
#[AsContentElement('exercise_topic', category: 'milica')]
final class ExerciseTopicController extends AbstractContentElementController
{
    protected function getResponse(FragmentTemplate $template, ContentModel $model, Request $request): Response
    {
        $template->set('choice', (string) $model->introTitle);
        $template->set('steps', array_filter(StringUtil::deserialize($model->listitems, true)));
        $template->set('reflection', (string) $model->introText);
        $template->set('link_text', (string) $model->introPrimaryText);

        return $template->getResponse();
    }
}
