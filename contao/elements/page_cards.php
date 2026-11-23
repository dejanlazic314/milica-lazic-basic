<?php

declare(strict_types=1);

/*
 * Page cards: one card per published subpage of a chosen page (title, card text and
 * illustration from the page settings), plus a contact card at the end, shown when its
 * text is filled in.
 */
$GLOBALS['TL_DCA']['tl_content']['palettes']['page_cards'] = '{type_legend},type;{layout_legend},pageCardsStyle;{config_legend},topicsParent;{text_legend},introTitle,introText,introMeta;{link_legend},introPrimaryUrl,introPrimaryText;{cta_legend},ctaQuote,ctaText,ctaLinkText;{image_legend},ornamentImage;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID;{invisible_legend:hide},invisible,start,stop';

// The framed style has no heading (ElementFieldsListener)
$GLOBALS['TL_DCA']['tl_content']['optionalFields']['page_cards'] = ['introTitle'];

$GLOBALS['TL_DCA']['tl_content']['fields']['pageCardsStyle'] = [
    'inputType' => 'select',
    'options' => MilicaLazic\Controller\ContentElement\PageCardsController::STYLES,
    'reference' => &$GLOBALS['TL_LANG']['tl_content']['pageCardsStyles'],
    'eval' => ['tl_class' => 'w50'],
    'sql' => ['type' => 'string', 'length' => 16, 'default' => 'large'],
];

$GLOBALS['TL_DCA']['tl_content']['fields']['ctaQuote'] = [
    'inputType' => 'text',
    'eval' => ['maxlength' => 255, 'tl_class' => 'clr long'],
    'sql' => ['type' => 'string', 'length' => 255, 'default' => ''],
];

$GLOBALS['TL_DCA']['tl_content']['fields']['ctaText'] = [
    'inputType' => 'textarea',
    'eval' => ['rows' => 3, 'tl_class' => 'clr'],
    'sql' => ['type' => 'text', 'notnull' => false],
];

$GLOBALS['TL_DCA']['tl_content']['fields']['ctaLinkText'] = [
    'inputType' => 'text',
    'eval' => ['maxlength' => 64, 'tl_class' => 'w50'],
    'sql' => ['type' => 'string', 'length' => 64, 'default' => ''],
];
