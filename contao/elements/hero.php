<?php

declare(strict_types=1);

/*
 * Hero: the top block of a page with its H1. Layout and width: HeroController.
 *
 * The layout comes first, in its own legend: it decides which fields the hero uses, so
 * the form reloads on a change and shows only those (subpalettes below).
 *
 * Contao also reads the value of a selector field as a palette name, so a layout value must
 * never be the type of an element: "text" opened the form of the core text element.
 */
$GLOBALS['TL_DCA']['tl_content']['palettes']['hero'] = '{type_legend},type;{layout_legend},heroLayout;{text_legend},eyebrow,introTitle,introText,introMeta;{link_legend},introPrimaryUrl,introPrimaryText,introSecondaryUrl,introSecondaryText;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID;{invisible_legend:hide},invisible,start,stop';

// The column default is empty, so HeroMigration can tell the old elements; a new one starts as "plain"
$GLOBALS['TL_DCA']['tl_content']['fields']['heroLayout'] = [
    'inputType' => 'select',
    'options' => MilicaLazic\Controller\ContentElement\HeroController::LAYOUTS,
    'reference' => &$GLOBALS['TL_LANG']['tl_content']['heroLayouts'],
    'default' => 'plain',
    'eval' => ['submitOnChange' => true, 'tl_class' => 'w50'],
    'sql' => ['type' => 'string', 'length' => 16, 'default' => ''],
];

$GLOBALS['TL_DCA']['tl_content']['fields']['heroWidth'] = [
    'inputType' => 'select',
    'options' => MilicaLazic\Controller\ContentElement\HeroController::WIDTHS,
    'reference' => &$GLOBALS['TL_LANG']['tl_content']['heroWidths'],
    'eval' => ['tl_class' => 'w50'],
    'sql' => ['type' => 'string', 'length' => 16, 'default' => 'default'],
];

$GLOBALS['TL_DCA']['tl_content']['palettes']['__selector__'][] = 'heroLayout';
$GLOBALS['TL_DCA']['tl_content']['subpalettes']['heroLayout_background'] = 'introImage';
$GLOBALS['TL_DCA']['tl_content']['subpalettes']['heroLayout_split'] = 'heroWidth,introImage';
$GLOBALS['TL_DCA']['tl_content']['subpalettes']['heroLayout_plain'] = 'heroWidth';
