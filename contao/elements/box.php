<?php

declare(strict_types=1);

/*
 * Box: a highlighted block of text. Style: BoxController.
 */
$GLOBALS['TL_DCA']['tl_content']['palettes']['box'] = '{type_legend},type;{layout_legend},boxStyle;{text_legend},eyebrow,boxTitle,text;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID;{invisible_legend:hide},invisible,start,stop';

$GLOBALS['TL_DCA']['tl_content']['fields']['boxStyle'] = [
    'inputType' => 'select',
    'options' => MilicaLazic\Controller\ContentElement\BoxController::STYLES,
    'reference' => &$GLOBALS['TL_LANG']['tl_content']['boxStyles'],
    'eval' => ['tl_class' => 'w50'],
    'sql' => ['type' => 'string', 'length' => 16, 'default' => 'white'],
];

// Not the shared introTitle: that one is mandatory, and a box often has only an eyebrow
$GLOBALS['TL_DCA']['tl_content']['fields']['boxTitle'] = [
    'inputType' => 'text',
    'eval' => ['maxlength' => 255, 'tl_class' => 'clr long'],
    'sql' => ['type' => 'string', 'length' => 255, 'default' => ''],
];
