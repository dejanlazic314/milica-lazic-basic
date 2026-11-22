<?php

declare(strict_types=1);

/*
 * Topic grid: cards from the subpages of a page, plus the contact card at the end
 * ("Ne vidite svoju temu ovde?"; home page and overview, shown when its text is filled in).
 */
$GLOBALS['TL_DCA']['tl_content']['palettes']['topic_grid'] = '{type_legend},type,topicsVariant;{config_legend},topicsParent;{text_legend},introTitle,introText,introMeta;{link_legend},introPrimaryUrl,introPrimaryText;{cta_legend},ctaQuote,ctaText,ctaLinkText;{image_legend},ornamentImage;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID;{invisible_legend:hide},invisible,start,stop';

$GLOBALS['TL_DCA']['tl_content']['fields']['topicsVariant'] = [
    'inputType' => 'select',
    'options' => MilicaLazic\Controller\ContentElement\TopicGridController::VARIANTS,
    'reference' => &$GLOBALS['TL_LANG']['tl_content']['topicsVariants'],
    'eval' => ['tl_class' => 'w50'],
    'sql' => ['type' => 'string', 'length' => 16, 'default' => 'home'],
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
