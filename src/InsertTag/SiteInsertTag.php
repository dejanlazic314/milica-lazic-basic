<?php

declare(strict_types=1);

namespace MilicaLazic\InsertTag;

use Contao\CoreBundle\DependencyInjection\Attribute\AsInsertTag;
use Contao\CoreBundle\InsertTag\InsertTagResult;
use Contao\CoreBundle\InsertTag\OutputType;
use Contao\CoreBundle\InsertTag\Resolver\InsertTagResolverNestedResolvedInterface;
use Contao\CoreBundle\InsertTag\ResolvedInsertTag;
use Contao\CoreBundle\Routing\PageFinder;
use MilicaLazic\Site\SiteSettings;

/**
 * {{site::email}}, {{site::phone}}, {{site::address}}, {{site::hours}}: site data of
 * the current root page, for rich text (e.g. the privacy policy), so the text follows
 * the data entered once on the root page.
 */
#[AsInsertTag('site')]
final class SiteInsertTag implements InsertTagResolverNestedResolvedInterface
{
    private const KEYS = ['email', 'phone', 'address', 'hours'];

    public function __construct(
        private readonly PageFinder $pageFinder,
        private readonly SiteSettings $site,
    ) {
    }

    public function __invoke(ResolvedInsertTag $insertTag): InsertTagResult
    {
        $key = (string) $insertTag->getParameters()->get(0);
        $page = $this->pageFinder->getCurrentPage();

        if (!\in_array($key, self::KEYS, true) || null === $page) {
            return new InsertTagResult('');
        }

        return new InsertTagResult((string) ($this->site->forPage($page)[$key] ?? ''), OutputType::text);
    }
}
