<?php

declare(strict_types=1);

/*
 * Content elements of the site (category "milica").
 *
 * This file has the fields that several elements share (heading, lead, buttons, images).
 * Each element has its own file in contao/elements/ with its palette and its own
 * fields; the files are loaded below in alphabetical order.
 *
 * Shared palette ending: template, protection, CSS ID and visibility, as in the core elements.
 */

use Contao\System;

$GLOBALS['TL_DCA']['tl_content']['fields']['eyebrow'] = [
    'inputType' => 'text',
    'eval' => ['maxlength' => 128, 'tl_class' => 'w50'],
    'sql' => ['type' => 'string', 'length' => 128, 'default' => ''],
];

$GLOBALS['TL_DCA']['tl_content']['fields']['introTitle'] = [
    'inputType' => 'text',
    'eval' => ['mandatory' => true, 'maxlength' => 255, 'tl_class' => 'clr long'],
    'sql' => ['type' => 'string', 'length' => 255, 'default' => ''],
];

$GLOBALS['TL_DCA']['tl_content']['fields']['introText'] = [
    'inputType' => 'textarea',
    'eval' => ['rows' => 3, 'tl_class' => 'clr'],
    'sql' => ['type' => 'text', 'notnull' => false],
];

$GLOBALS['TL_DCA']['tl_content']['fields']['introMeta'] = [
    'inputType' => 'text',
    'eval' => ['maxlength' => 255, 'tl_class' => 'clr long'],
    'sql' => ['type' => 'string', 'length' => 255, 'default' => ''],
];

// Own link fields: the core "url" field is mandatory
foreach (['introPrimaryUrl', 'introSecondaryUrl'] as $field) {
    $GLOBALS['TL_DCA']['tl_content']['fields'][$field] = [
        'inputType' => 'text',
        'eval' => ['rgxp' => 'url', 'decodeEntities' => true, 'maxlength' => 2048, 'dcaPicker' => true, 'tl_class' => 'w50 clr'],
        'sql' => ['type' => 'string', 'length' => 2048, 'default' => ''],
    ];
}

foreach (['introPrimaryText', 'introSecondaryText'] as $field) {
    $GLOBALS['TL_DCA']['tl_content']['fields'][$field] = [
        'inputType' => 'text',
        'eval' => ['maxlength' => 64, 'tl_class' => 'w50'],
        'sql' => ['type' => 'string', 'length' => 64, 'default' => ''],
    ];
}

// Own image field: the core "singleSRC" field is mandatory
$GLOBALS['TL_DCA']['tl_content']['fields']['introImage'] = [
    'inputType' => 'fileTree',
    'eval' => ['filesOnly' => true, 'fieldType' => 'radio', 'extensions' => '%contao.image.valid_extensions%', 'tl_class' => 'clr'],
    'sql' => ['type' => 'binary', 'length' => 16, 'fixed' => true, 'notnull' => false],
];

// Decorative line drawing in a corner of a section (no alt text)
$GLOBALS['TL_DCA']['tl_content']['fields']['ornamentImage'] = [
    'inputType' => 'fileTree',
    'eval' => ['filesOnly' => true, 'fieldType' => 'radio', 'extensions' => 'svg', 'tl_class' => 'clr'],
    'sql' => ['type' => 'binary', 'length' => 16, 'fixed' => true, 'notnull' => false],
];

// Section background (section, contact block)
$GLOBALS['TL_DCA']['tl_content']['fields']['sectionStyle'] = [
    'inputType' => 'select',
    'options' => MilicaLazic\Controller\ContentElement\SectionController::STYLES,
    'reference' => &$GLOBALS['TL_LANG']['tl_content']['sectionStyles'],
    'eval' => ['includeBlankOption' => true, 'tl_class' => 'w33'],
    'sql' => ['type' => 'string', 'length' => 16, 'default' => ''],
];

// Google map (section with the layout "location", contact block)
foreach (['mapEmbedUrl', 'mapLinkUrl'] as $field) {
    $GLOBALS['TL_DCA']['tl_content']['fields'][$field] = [
        'inputType' => 'text',
        'eval' => ['rgxp' => 'url', 'decodeEntities' => true, 'maxlength' => 2048, 'tl_class' => 'clr long'],
        'sql' => ['type' => 'string', 'length' => 2048, 'default' => ''],
    ];
}

// Page whose subpages are the topics (topic grid, 404 page)
$GLOBALS['TL_DCA']['tl_content']['fields']['topicsParent'] = [
    'inputType' => 'pageTree',
    'foreignKey' => 'tl_page.title',
    'eval' => ['mandatory' => true, 'fieldType' => 'radio', 'tl_class' => 'clr'],
    'sql' => ['type' => 'integer', 'unsigned' => true, 'default' => 0],
    'relation' => ['type' => 'hasOne', 'load' => 'lazy'],
];

/*
 * One file per element. Not with __DIR__: Contao copies this file into its cache
 * (var/cache/…/contao/dca), so the path is taken from the project directory.
 */
$elementFiles = glob(System::getContainer()->getParameter('kernel.project_dir').'/contao/elements/*.php') ?: [];

foreach ($elementFiles as $elementFile) {
    require $elementFile;
}

unset($elementFiles, $elementFile);
