<?php

declare(strict_types=1);

/*
 * #gestalt section (nested elements below), card group (nested cards) and icon card.
 */
$GLOBALS['TL_DCA']['tl_content']['palettes']['gestalt_intro'] = '{type_legend},type;{text_legend},eyebrow,introTitle,introText,text;{link_legend},introPrimaryUrl,introPrimaryText;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID;{invisible_legend:hide},invisible,start,stop';

$GLOBALS['TL_DCA']['tl_content']['palettes']['card_group'] = '{type_legend},type,cardsVariant;{text_legend},introTitle;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID;{invisible_legend:hide},invisible,start,stop';

$GLOBALS['TL_DCA']['tl_content']['palettes']['icon_card'] = '{type_legend},type,cardIcon;{text_legend},introTitle,introText;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID;{invisible_legend:hide},invisible,start,stop';

$GLOBALS['TL_DCA']['tl_content']['fields']['cardsVariant'] = [
    'inputType' => 'select',
    'options' => MilicaLazic\Controller\ContentElement\CardGroupController::VARIANTS,
    'reference' => &$GLOBALS['TL_LANG']['tl_content']['cardsVariants'],
    'eval' => ['tl_class' => 'w50'],
    'sql' => ['type' => 'string', 'length' => 16, 'default' => 'how'],
];

// Also used by the card (section.php)
$GLOBALS['TL_DCA']['tl_content']['fields']['cardIcon'] = [
    'inputType' => 'select',
    'options' => MilicaLazic\Controller\ContentElement\IconCardController::ICONS,
    'reference' => &$GLOBALS['TL_LANG']['tl_content']['cardIcons'],
    'eval' => ['includeBlankOption' => true, 'tl_class' => 'w50'],
    'sql' => ['type' => 'string', 'length' => 32, 'default' => ''],
];
