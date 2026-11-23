<?php

declare(strict_types=1);

/*
 * Item list: rows of label, title and text. Style: ItemListController.
 */
$GLOBALS['TL_DCA']['tl_content']['palettes']['item_list'] = '{type_legend},type;{layout_legend},listStyle;{list_legend},listRows;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID;{invisible_legend:hide},invisible,start,stop';

$GLOBALS['TL_DCA']['tl_content']['fields']['listStyle'] = [
    'inputType' => 'select',
    'options' => array_keys(MilicaLazic\Controller\ContentElement\ItemListController::STYLES),
    'reference' => &$GLOBALS['TL_LANG']['tl_content']['listStyles'],
    'eval' => ['tl_class' => 'w50'],
    'sql' => ['type' => 'string', 'length' => 16, 'default' => 'steps'],
];

$GLOBALS['TL_DCA']['tl_content']['fields']['listRows'] = [
    'inputType' => 'rowWizard',
    'fields' => [
        'label' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['listItemLabel'],
            'inputType' => 'text',
            'eval' => ['maxlength' => 64],
        ],
        'title' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['listItemTitle'],
            'inputType' => 'text',
            'eval' => ['maxlength' => 255],
        ],
        'text' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['listItemText'],
            'inputType' => 'text',
            'eval' => ['maxlength' => 1024],
        ],
    ],
    'eval' => ['tl_class' => 'clr'],
    'sql' => ['type' => 'text', 'notnull' => false],
];
