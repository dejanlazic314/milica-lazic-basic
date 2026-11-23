<?php

declare(strict_types=1);

/*
 * Card grid: nested cards in 2, 3 or 4 columns, with an optional heading and note.
 */
$GLOBALS['TL_DCA']['tl_content']['palettes']['card_grid'] = '{type_legend},type;{layout_legend},gridColumns;{text_legend},introTitle,introMeta;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID;{invisible_legend:hide},invisible,start,stop';

// Inside a section the heading is usually the section's (ElementFieldsListener)
$GLOBALS['TL_DCA']['tl_content']['optionalFields']['card_grid'] = ['introTitle'];

$GLOBALS['TL_DCA']['tl_content']['fields']['gridColumns'] = [
    'inputType' => 'select',
    'options' => array_keys(MilicaLazic\Controller\ContentElement\CardGridController::COLUMNS),
    'reference' => &$GLOBALS['TL_LANG']['tl_content']['gridColumnsOptions'],
    'eval' => ['tl_class' => 'w50'],
    'sql' => ['type' => 'string', 'length' => 8, 'default' => '3'],
];
