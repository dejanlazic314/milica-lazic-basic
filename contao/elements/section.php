<?php

declare(strict_types=1);

/*
 * Generic subpage section (nested card grids, lists, boxes, FAQ lists inside).
 */
$GLOBALS['TL_DCA']['tl_content']['palettes']['section'] = '{type_legend},type;{layout_legend},sectionLayout;{style_legend},sectionStyle,sectionSpacing;{text_legend},eyebrow,introTitle,introText,text;{map_legend:hide},mapEmbedUrl,mapLinkUrl;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID;{invisible_legend:hide},invisible,start,stop';

// A section can hold only nested elements, or only a heading (ElementFieldsListener)
$GLOBALS['TL_DCA']['tl_content']['optionalFields']['section'] = ['introTitle', 'text'];

$GLOBALS['TL_DCA']['tl_content']['fields']['sectionLayout'] = [
    'inputType' => 'select',
    'options' => array_keys(MilicaLazic\Controller\ContentElement\SectionController::LAYOUTS),
    'reference' => &$GLOBALS['TL_LANG']['tl_content']['sectionLayouts'],
    'eval' => ['tl_class' => 'w50'],
    'sql' => ['type' => 'string', 'length' => 16, 'default' => 'stack'],
];

$GLOBALS['TL_DCA']['tl_content']['fields']['sectionSpacing'] = [
    'inputType' => 'select',
    'options' => array_keys(MilicaLazic\Controller\ContentElement\SectionController::SPACINGS),
    'reference' => &$GLOBALS['TL_LANG']['tl_content']['sectionSpacings'],
    'eval' => ['includeBlankOption' => true, 'tl_class' => 'w33'],
    'sql' => ['type' => 'string', 'length' => 16, 'default' => ''],
];

