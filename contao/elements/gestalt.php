<?php

declare(strict_types=1);

/*
 * #gestalt section (nested elements below) and card group (nested cards).
 */
$GLOBALS['TL_DCA']['tl_content']['palettes']['gestalt_intro'] = '{type_legend},type;{text_legend},eyebrow,introTitle,introText,text;{link_legend},introPrimaryUrl,introPrimaryText;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID;{invisible_legend:hide},invisible,start,stop';

$GLOBALS['TL_DCA']['tl_content']['palettes']['card_group'] = '{type_legend},type,cardsVariant;{text_legend},introTitle;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID;{invisible_legend:hide},invisible,start,stop';

$GLOBALS['TL_DCA']['tl_content']['fields']['cardsVariant'] = [
    'inputType' => 'select',
    'options' => MilicaLazic\Controller\ContentElement\CardGroupController::VARIANTS,
    'reference' => &$GLOBALS['TL_LANG']['tl_content']['cardsVariants'],
    'eval' => ['tl_class' => 'w50'],
    'sql' => ['type' => 'string', 'length' => 16, 'default' => 'how'],
];
