<?php

declare(strict_types=1);

namespace MilicaLazic\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Filter "inline_markup" for one-line fields: escapes the text, then turns
 * *words* into <em> and [label](url) into a link. Editors avoid HTML in plain fields
 * such as the meta line "Autorka: [Milica Lazić](/o-meni) · Ažurirano: …".
 */
final class InlineMarkupExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [new TwigFilter('inline_markup', $this->convert(...), ['is_safe' => ['html']])];
    }

    public function convert(string|null $text): string
    {
        // Contao stores input with ( ) # < > encoded as entities (&#40;…), which would hide the link
        // syntax; decode first, then escape everything once
        $text = html_entity_decode((string) $text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $html = htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5, 'UTF-8');
        $html = preg_replace('/\[([^\]]+)\]\(([^)\s]+)\)/u', '<a href="$2">$1</a>', $html);

        return preg_replace('/\*([^*]+)\*/u', '<em>$1</em>', $html);
    }
}
