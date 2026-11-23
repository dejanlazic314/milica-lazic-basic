<?php

declare(strict_types=1);

/*
 * Generic subpage section (nested cards, steps, FAQ lists inside), card and approach step.
 */
$GLOBALS['TL_DCA']['tl_content']['palettes']['section'] = '{type_legend},type,sectionLayout;{style_legend},sectionStyle,sectionSpacing,sectionGrid;{text_legend},eyebrow,introTitle,introText,text,introMeta;{list_legend:hide},sectionRows,sectionChips;{map_legend:hide},mapEmbedUrl,mapLinkUrl;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID;{invisible_legend:hide},invisible,start,stop';

// A section can hold only nested elements, or only a heading (ElementFieldsListener)
$GLOBALS['TL_DCA']['tl_content']['optionalFields']['section'] = ['introTitle', 'text'];

$GLOBALS['TL_DCA']['tl_content']['palettes']['card'] = '{type_legend},type,cardVariant,cardIcon;{text_legend},introTitle,text;{link_legend},introPrimaryUrl,introPrimaryText;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID;{invisible_legend:hide},invisible,start,stop';

$GLOBALS['TL_DCA']['tl_content']['palettes']['step'] = '{type_legend},type;{text_legend},introTitle,introText;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID;{invisible_legend:hide},invisible,start,stop';

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

$GLOBALS['TL_DCA']['tl_content']['fields']['sectionGrid'] = [
    'inputType' => 'select',
    'options' => MilicaLazic\Controller\ContentElement\SectionController::GRIDS,
    'reference' => &$GLOBALS['TL_LANG']['tl_content']['sectionGrids'],
    'eval' => ['includeBlankOption' => true, 'tl_class' => 'w33'],
    'sql' => ['type' => 'string', 'length' => 16, 'default' => ''],
];

foreach (['sectionRows', 'sectionChips'] as $field) {
    $GLOBALS['TL_DCA']['tl_content']['fields'][$field] = [
        'inputType' => 'textarea',
        'eval' => ['rows' => 4, 'tl_class' => 'clr'],
        'sql' => ['type' => 'text', 'notnull' => false],
    ];
}

$GLOBALS['TL_DCA']['tl_content']['fields']['cardVariant'] = [
    'inputType' => 'select',
    'options' => MilicaLazic\Controller\ContentElement\CardController::VARIANTS,
    'reference' => &$GLOBALS['TL_LANG']['tl_content']['cardVariants'],
    'eval' => ['tl_class' => 'w50'],
    'sql' => ['type' => 'string', 'length' => 16, 'default' => 'white'],
];
