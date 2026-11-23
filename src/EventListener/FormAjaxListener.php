<?php

declare(strict_types=1);

namespace MilicaLazic\EventListener;

use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;
use Contao\CoreBundle\Exception\ResponseException;
use Contao\CoreBundle\InsertTag\InsertTagParser;
use Contao\Form;
use Contao\Widget;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

/**
 * Answers forms sent by public/layout/js/elements/form.js (fetch with "X-Requested-With").
 *
 * The script checks the fields itself and only looks at the status code, so the
 * server answers with a status instead of a whole page:
 *  - 200 and the confirmation (form_message.html.twig) once the e-mail is sent,
 *  - 422 if a field does not validate (the script then shows its "not sent" box).
 *
 * Without JavaScript the form is a normal Contao form: errors next to the fields,
 * the confirmation after the reload. Contao's own Ajax option of the form stays
 * off; its requests ("X-Contao-Ajax-Form") are left alone.
 */
final class FormAjaxListener
{
    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly InsertTagParser $insertTagParser,
        private readonly Environment $twig,
    ) {
    }

    #[AsHook('validateFormField')]
    public function onValidateFormField(Widget $widget): Widget
    {
        if ($widget->hasErrors() && $this->isScriptRequest()) {
            throw new ResponseException(new Response('', Response::HTTP_UNPROCESSABLE_ENTITY));
        }

        return $widget;
    }

    /**
     * Runs after the e-mail is sent and before Contao stores the confirmation in
     * the session for the next page view.
     *
     * @param array<string, mixed> $submitted
     * @param array<string, mixed> $formData
     */
    #[AsHook('processFormData')]
    public function onProcessFormData(array $submitted, array $formData, array|null $files, array $labels, Form $form): void
    {
        if ($form->hasErrors() || !$this->isScriptRequest()) {
            return;
        }

        $message = $this->insertTagParser->replaceInline((string) ($formData['confirmation'] ?? ''));

        throw new ResponseException(new Response(
            '' === $message ? '' : $this->twig->render('@Contao/form_message.html.twig', ['message' => $message]),
        ));
    }

    private function isScriptRequest(): bool
    {
        // The form is rendered in a fragment (nested element); the headers are on the main request
        $request = $this->requestStack->getMainRequest();

        return null !== $request
            && $request->isXmlHttpRequest()
            && !$request->headers->has('X-Contao-Ajax-Form');
    }
}
