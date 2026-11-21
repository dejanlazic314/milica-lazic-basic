<?php

declare(strict_types=1);

namespace MilicaLazic\Twig;

use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Filter "legal_text" for the rich text of a legal page (markup of
 * html/strane/politika-privatnosti.html):
 *  - every H2 gets an ID (from its text, unless it has one) and goes into the
 *    table of contents, which is placed before the first H2;
 *  - table cells get data-name from their column heading (the phone layout shows it);
 *  - a paragraph with only {{cookie_settings}} becomes the button that opens the
 *    cookie settings.
 * Runs after sanitize_html, so the attributes it adds stay.
 */
final class LegalExtension extends AbstractExtension
{
    public function __construct(private readonly TranslatorInterface $translator)
    {
    }

    public function getFilters(): array
    {
        return [new TwigFilter('legal_text', $this->convert(...), ['is_safe' => ['html']])];
    }

    public function convert(string|null $html): string
    {
        if (null === $html || '' === trim($html)) {
            return '';
        }

        $doc = new \DOMDocument();
        $previous = libxml_use_internal_errors(true);
        $doc->loadHTML('<?xml encoding="UTF-8"><div id="legal-root">'.$html.'</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $root = $doc->getElementById('legal-root');

        if (null === $root) {
            return $html;
        }

        $this->addHeadingIds($doc, $root);
        $this->addCellNames($root);
        $this->addCookieButton($doc, $root);

        $out = '';

        foreach ($root->childNodes as $child) {
            $out .= $doc->saveHTML($child);
        }

        // saveHTML() URL-encodes insert tags in href ({{site::email|urlattr}}); restore them
        return preg_replace_callback('/%7B%7B(.+?)%7D%7D/i', static fn (array $m): string => '{{'.rawurldecode($m[1]).'}}', $out);
    }

    private function addHeadingIds(\DOMDocument $doc, \DOMElement $root): void
    {
        $slugger = new AsciiSlugger();
        $toc = [];
        $used = [];

        foreach ($root->getElementsByTagName('h2') as $h2) {
            $id = $h2->getAttribute('id') ?: $slugger->slug(trim($h2->textContent))->lower()->toString();

            for ($base = $id, $i = 2; isset($used[$id]); ++$i) {
                $id = $base.'-'.$i;
            }

            $used[$id] = true;
            $h2->setAttribute('id', $id);
            $toc[] = [$id, trim($h2->textContent)];
        }

        $first = $root->getElementsByTagName('h2')->item(0);

        if (\count($toc) < 2 || null === $first) {
            return;
        }

        $nav = $doc->createElement('nav');
        $nav->setAttribute('class', 'legal__content');
        $nav->setAttribute('aria-label', $this->translator->trans('legal.contents', [], 'site'));
        $label = $doc->createElement('p');
        $label->setAttribute('class', 'eyebrow');
        $label->appendChild($doc->createTextNode($this->translator->trans('legal.contents', [], 'site')));
        $nav->appendChild($label);
        $list = $doc->createElement('ol');

        foreach ($toc as [$id, $title]) {
            $link = $doc->createElement('a');
            $link->setAttribute('href', '#'.$id);
            $link->appendChild($doc->createTextNode($title));
            $item = $doc->createElement('li');
            $item->appendChild($link);
            $list->appendChild($item);
        }

        $nav->appendChild($list);
        $first->parentNode?->insertBefore($nav, $first);
    }

    private function addCellNames(\DOMElement $root): void
    {
        foreach ($root->getElementsByTagName('table') as $table) {
            $names = [];

            foreach ($table->getElementsByTagName('th') as $th) {
                if ('thead' === $th->parentNode?->parentNode?->nodeName) {
                    $names[] = trim($th->textContent);
                }
            }

            foreach ($table->getElementsByTagName('tr') as $tr) {
                $col = 0;

                foreach ($tr->childNodes as $cell) {
                    if ($cell instanceof \DOMElement && 'td' === $cell->nodeName) {
                        if (isset($names[$col]) && !$cell->hasAttribute('data-name')) {
                            $cell->setAttribute('data-name', $names[$col]);
                        }

                        ++$col;
                    }
                }
            }
        }
    }

    private function addCookieButton(\DOMDocument $doc, \DOMElement $root): void
    {
        foreach (iterator_to_array($root->getElementsByTagName('p')) as $p) {
            if ('{{cookie_settings}}' !== trim($p->textContent)) {
                continue;
            }

            $button = $doc->createElement('button');
            $button->setAttribute('class', 'btn btn-dark btn-small');
            $button->setAttribute('type', 'button');
            $button->setAttribute('data-cookies-open', '');
            $button->appendChild($doc->createTextNode($this->translator->trans('legal.cookie_settings', [], 'site')));

            while ($p->firstChild) {
                $p->removeChild($p->firstChild);
            }

            $p->appendChild($button);
        }
    }
}
