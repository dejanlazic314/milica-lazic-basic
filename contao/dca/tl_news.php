<?php

declare(strict_types=1);

use Contao\CoreBundle\DataContainer\PaletteManipulator;

// Category shown as a chip on the post card and in the article ("Anksioznost", "Prvi korak", …)
PaletteManipulator::create()
    ->addField('category', 'headline', PaletteManipulator::POSITION_AFTER)
    ->applyToPalette('default', 'tl_news')
    ->applyToPalette('internal', 'tl_news')
    ->applyToPalette('article', 'tl_news')
    ->applyToPalette('external', 'tl_news')
;

$GLOBALS['TL_DCA']['tl_news']['fields']['category'] = [
    'inputType' => 'text',
    'filter' => true,
    'eval' => ['maxlength' => 64, 'tl_class' => 'w50'],
    'sql' => ['type' => 'string', 'length' => 64, 'default' => ''],
];

// Post page: "Ukratko" box, update date and an optional reading time (for posts that
// point to a page, e.g. "Šta je geštalt terapija?")
PaletteManipulator::create()
    ->addLegend('post_legend', 'teaser_legend', PaletteManipulator::POSITION_AFTER)
    ->addField(['summary', 'dateUpdated', 'readingTime'], 'post_legend', PaletteManipulator::POSITION_APPEND)
    ->applyToPalette('default', 'tl_news')
    ->applyToPalette('internal', 'tl_news')
    ->applyToPalette('article', 'tl_news')
    ->applyToPalette('external', 'tl_news')
;

$GLOBALS['TL_DCA']['tl_news']['fields']['summary'] = [
    'inputType' => 'textarea',
    'eval' => ['rte' => 'tinyMCE', 'basicEntities' => true, 'tl_class' => 'clr'],
    'sql' => ['type' => 'text', 'notnull' => false],
];

$GLOBALS['TL_DCA']['tl_news']['fields']['dateUpdated'] = [
    'inputType' => 'text',
    'eval' => ['rgxp' => 'date', 'datepicker' => true, 'tl_class' => 'w50 wizard'],
    'sql' => ['type' => 'integer', 'unsigned' => true, 'notnull' => false],
];

$GLOBALS['TL_DCA']['tl_news']['fields']['readingTime'] = [
    'inputType' => 'text',
    'eval' => ['rgxp' => 'natural', 'maxlength' => 3, 'tl_class' => 'w50'],
    'sql' => ['type' => 'smallint', 'unsigned' => true, 'default' => 0],
];

// Translations: an English post points to its Serbian post (language switch, hreflang)
PaletteManipulator::create()
    ->addLegend('translation_legend', 'title_legend', PaletteManipulator::POSITION_AFTER)
    ->addField('languageMain', 'translation_legend', PaletteManipulator::POSITION_APPEND)
    ->applyToPalette('default', 'tl_news')
    ->applyToPalette('internal', 'tl_news')
    ->applyToPalette('article', 'tl_news')
    ->applyToPalette('external', 'tl_news')
;

$GLOBALS['TL_DCA']['tl_news']['fields']['languageMain'] = [
    'inputType' => 'select',
    'foreignKey' => 'tl_news.headline',
    'eval' => ['includeBlankOption' => true, 'chosen' => true, 'tl_class' => 'w50'],
    'sql' => ['type' => 'integer', 'unsigned' => true, 'default' => 0],
    'relation' => ['type' => 'hasOne', 'load' => 'lazy'],
];
