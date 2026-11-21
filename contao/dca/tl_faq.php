<?php

declare(strict_types=1);

use Contao\CoreBundle\DataContainer\PaletteManipulator;

// The home page shows a hand-picked selection of questions
PaletteManipulator::create()
    ->addField('showOnHome', 'publish_legend', PaletteManipulator::POSITION_PREPEND)
    ->applyToPalette('default', 'tl_faq')
;

$GLOBALS['TL_DCA']['tl_faq']['fields']['showOnHome'] = [
    'inputType' => 'checkbox',
    'filter' => true,
    'eval' => ['tl_class' => 'w50'],
    'sql' => ['type' => 'boolean', 'default' => false],
];
