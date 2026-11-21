<?php

declare(strict_types=1);

namespace MilicaLazic\Site;

use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\CoreBundle\Routing\ContentUrlGenerator;
use Contao\FilesModel;
use Contao\PageModel;
use Symfony\Component\Routing\Exception\ExceptionInterface;

/**
 * Site data entered on the root page (contao/dca/tl_page.php), prepared for templates.
 */
final class SiteSettings
{
    public function __construct(
        private readonly ContaoFramework $framework,
        private readonly ContentUrlGenerator $urlGenerator,
    ) {
    }

    /**
     * @return array<string, string|null>
     */
    public function forPage(PageModel $page): array
    {
        $pageAdapter = $this->framework->getAdapter(PageModel::class);
        $root = $pageAdapter->findById($page->rootId);

        if (null === $root) {
            return [];
        }

        $phone = (string) $root->phone;

        return [
            'home_url' => $this->url($pageAdapter->findFirstPublishedRegularByPid($root->id)) ?? '/',
            'contact_url' => $root->contactPage ? $this->url($pageAdapter->findPublishedById($root->contactPage)) : null,
            'logo' => $this->filePath($root->siteLogo),
            'info_bar_text' => (string) $root->infoBarText,
            'phone' => $phone,
            'phone_href' => '' !== $phone ? 'tel:'.preg_replace('/[^\d+]/', '', $phone) : null,
            'email' => (string) $root->email,
            'address' => (string) $root->address,
            'hours' => (string) $root->hours,
            'instagram' => (string) $root->instagram,
            'linkedin' => (string) $root->linkedin,
        ];
    }

    private function url(PageModel|null $page): string|null
    {
        if (null === $page) {
            return null;
        }

        try {
            return $this->urlGenerator->generate($page);
        } catch (ExceptionInterface) {
            return null;
        }
    }

    private function filePath(string|null $uuid): string|null
    {
        if (!$uuid) {
            return null;
        }

        $file = $this->framework->getAdapter(FilesModel::class)->findByUuid($uuid);

        return $file ? '/'.$file->path : null;
    }
}
