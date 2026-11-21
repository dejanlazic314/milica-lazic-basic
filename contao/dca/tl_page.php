<?php

declare(strict_types=1);

use Contao\CoreBundle\DataContainer\PaletteManipulator;

/*
 * Site data on the root page: one place for the contact details and logo used by
 * the header, footer, contact block and schema.org data.
 */
PaletteManipulator::create()
    ->addLegend('site_legend', 'global_legend', PaletteManipulator::POSITION_AFTER)
    ->addField(['siteLogo', 'infoBarText', 'phone', 'email', 'address', 'hours', 'instagram', 'linkedin', 'contactPage'], 'site_legend', PaletteManipulator::POSITION_APPEND)
    ->applyToPalette('root', 'tl_page')
    ->applyToPalette('rootfallback', 'tl_page')
;

// Pages such as "Česta pitanja" are in the mobile menu and footer, not in the desktop menu
PaletteManipulator::create()
    ->addField('navMobileOnly', 'hide', PaletteManipulator::POSITION_AFTER)
    ->applyToPalette('regular', 'tl_page')
    ->applyToPalette('forward', 'tl_page')
    ->applyToPalette('redirect', 'tl_page')
;

$GLOBALS['TL_DCA']['tl_page']['fields']['siteLogo'] = [
    'inputType' => 'fileTree',
    'eval' => ['filesOnly' => true, 'fieldType' => 'radio', 'extensions' => 'svg,png,webp', 'tl_class' => 'clr'],
    'sql' => ['type' => 'binary', 'length' => 16, 'fixed' => true, 'notnull' => false],
];

$GLOBALS['TL_DCA']['tl_page']['fields']['infoBarText'] = [
    'inputType' => 'text',
    'eval' => ['maxlength' => 255, 'tl_class' => 'w50 clr'],
    'sql' => ['type' => 'string', 'length' => 255, 'default' => ''],
];

$GLOBALS['TL_DCA']['tl_page']['fields']['phone'] = [
    'inputType' => 'text',
    'eval' => ['maxlength' => 64, 'rgxp' => 'phone', 'tl_class' => 'w50'],
    'sql' => ['type' => 'string', 'length' => 64, 'default' => ''],
];

$GLOBALS['TL_DCA']['tl_page']['fields']['email'] = [
    'inputType' => 'text',
    'eval' => ['maxlength' => 255, 'rgxp' => 'email', 'decodeEntities' => true, 'tl_class' => 'w50'],
    'sql' => ['type' => 'string', 'length' => 255, 'default' => ''],
];

$GLOBALS['TL_DCA']['tl_page']['fields']['address'] = [
    'inputType' => 'text',
    'eval' => ['maxlength' => 255, 'tl_class' => 'w50'],
    'sql' => ['type' => 'string', 'length' => 255, 'default' => ''],
];

$GLOBALS['TL_DCA']['tl_page']['fields']['hours'] = [
    'inputType' => 'text',
    'eval' => ['maxlength' => 255, 'tl_class' => 'w50'],
    'sql' => ['type' => 'string', 'length' => 255, 'default' => ''],
];

$GLOBALS['TL_DCA']['tl_page']['fields']['instagram'] = [
    'inputType' => 'text',
    'eval' => ['maxlength' => 255, 'rgxp' => 'url', 'decodeEntities' => true, 'tl_class' => 'w50'],
    'sql' => ['type' => 'string', 'length' => 255, 'default' => ''],
];

$GLOBALS['TL_DCA']['tl_page']['fields']['linkedin'] = [
    'inputType' => 'text',
    'eval' => ['maxlength' => 255, 'rgxp' => 'url', 'decodeEntities' => true, 'tl_class' => 'w50'],
    'sql' => ['type' => 'string', 'length' => 255, 'default' => ''],
];

$GLOBALS['TL_DCA']['tl_page']['fields']['contactPage'] = [
    'inputType' => 'pageTree',
    'foreignKey' => 'tl_page.title',
    'eval' => ['fieldType' => 'radio', 'tl_class' => 'clr'],
    'sql' => ['type' => 'integer', 'unsigned' => true, 'default' => 0],
    'relation' => ['type' => 'hasOne', 'load' => 'lazy'],
];

$GLOBALS['TL_DCA']['tl_page']['fields']['navMobileOnly'] = [
    'inputType' => 'checkbox',
    'eval' => ['tl_class' => 'w50'],
    'sql' => ['type' => 'boolean', 'default' => false],
];

/*
 * Menu subtitle and teaser card. Kept apart from "description", which is the SEO
 * meta description (140–160 characters).
 */
PaletteManipulator::create()
    ->addLegend('teaser_legend', 'meta_legend', PaletteManipulator::POSITION_AFTER)
    ->addField(['navSubtitle', 'teaserText', 'teaserImage'], 'teaser_legend', PaletteManipulator::POSITION_APPEND)
    ->applyToPalette('regular', 'tl_page')
;

$GLOBALS['TL_DCA']['tl_page']['fields']['navSubtitle'] = [
    'inputType' => 'text',
    'eval' => ['maxlength' => 64, 'tl_class' => 'w50'],
    'sql' => ['type' => 'string', 'length' => 64, 'default' => ''],
];

$GLOBALS['TL_DCA']['tl_page']['fields']['teaserText'] = [
    'inputType' => 'textarea',
    'eval' => ['rows' => 2, 'tl_class' => 'clr'],
    'sql' => ['type' => 'text', 'notnull' => false],
];

$GLOBALS['TL_DCA']['tl_page']['fields']['teaserImage'] = [
    'inputType' => 'fileTree',
    'eval' => ['filesOnly' => true, 'fieldType' => 'radio', 'extensions' => 'svg,png,webp', 'tl_class' => 'clr'],
    'sql' => ['type' => 'binary', 'length' => 16, 'fixed' => true, 'notnull' => false],
];

/*
 * Translations: an English page points to its Serbian page ("languageMain"). Used by the
 * language switch and hreflang (src/Language/LanguageSwitcher.php). Set on the pages of
 * the non-fallback root (EN) only.
 */
PaletteManipulator::create()
    ->addLegend('translation_legend', 'title_legend', PaletteManipulator::POSITION_AFTER)
    ->addField('languageMain', 'translation_legend', PaletteManipulator::POSITION_APPEND)
    ->applyToPalette('regular', 'tl_page')
    ->applyToPalette('forward', 'tl_page')
    ->applyToPalette('redirect', 'tl_page')
    ->applyToPalette('error_404', 'tl_page')
;

$GLOBALS['TL_DCA']['tl_page']['fields']['languageMain'] = [
    'inputType' => 'pageTree',
    'foreignKey' => 'tl_page.title',
    'eval' => ['fieldType' => 'radio', 'tl_class' => 'clr'],
    'sql' => ['type' => 'integer', 'unsigned' => true, 'default' => 0],
    'relation' => ['type' => 'hasOne', 'load' => 'lazy'],
];
