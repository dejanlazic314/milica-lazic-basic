<?php

declare(strict_types=1);

/*
 * Card: one block in a grid of cards. Colour, size and icon: CardController.
 */
$GLOBALS['TL_DCA']['tl_content']['palettes']['card'] = '{type_legend},type;{layout_legend},cardVariant,cardSize,cardIcon;{text_legend},introTitle,text;{link_legend},introPrimaryUrl,introPrimaryText;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID;{invisible_legend:hide},invisible,start,stop';

$GLOBALS['TL_DCA']['tl_content']['fields']['cardVariant'] = [
    'inputType' => 'select',
    'options' => MilicaLazic\Controller\ContentElement\CardController::VARIANTS,
    'reference' => &$GLOBALS['TL_LANG']['tl_content']['cardVariants'],
    'eval' => ['tl_class' => 'w50'],
    'sql' => ['type' => 'string', 'length' => 16, 'default' => 'white'],
];

$GLOBALS['TL_DCA']['tl_content']['fields']['cardSize'] = [
    'inputType' => 'select',
    'options' => MilicaLazic\Controller\ContentElement\CardController::SIZES,
    'reference' => &$GLOBALS['TL_LANG']['tl_content']['cardSizes'],
    'eval' => ['tl_class' => 'w50'],
    'sql' => ['type' => 'string', 'length' => 16, 'default' => 'default'],
];

$GLOBALS['TL_DCA']['tl_content']['fields']['cardIcon'] = [
    'inputType' => 'select',
    'options' => MilicaLazic\Controller\ContentElement\CardController::ICONS,
    'reference' => &$GLOBALS['TL_LANG']['tl_content']['cardIcons'],
    'eval' => ['includeBlankOption' => true, 'tl_class' => 'w50'],
    'sql' => ['type' => 'string', 'length' => 32, 'default' => ''],
];
