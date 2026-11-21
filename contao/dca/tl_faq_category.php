<?php

declare(strict_types=1);

use Contao\CoreBundle\DataContainer\PaletteManipulator;

// Anchor of the group on the FAQ page, used by the filter row ("#f-pre")
PaletteManipulator::create()
    ->addField('anchor', 'title', PaletteManipulator::POSITION_AFTER)
    ->applyToPalette('default', 'tl_faq_category')
;

$GLOBALS['TL_DCA']['tl_faq_category']['fields']['anchor'] = [
    'inputType' => 'text',
    'eval' => ['maxlength' => 64, 'rgxp' => 'alias', 'tl_class' => 'w50'],
    'sql' => ['type' => 'string', 'length' => 64, 'default' => ''],
];
