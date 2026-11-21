<?php

declare(strict_types=1);

namespace MilicaLazic\Navigation;

final class NavItem
{
    /**
     * @param list<NavItem> $children
     */
    public function __construct(
        public readonly string $title,
        public readonly string $href,
        public readonly string $subtitle,
        public readonly bool $isActive,
        public readonly bool $isTrail,
        public readonly bool $mobileOnly,
        public readonly array $children,
    ) {
    }
}
