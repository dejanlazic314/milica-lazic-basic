<?php

declare(strict_types=1);

/*
 * FAQ list from the FAQ module: home page selection or the full FAQ page.
 */
$GLOBALS['TL_DCA']['tl_content']['palettes']['faq_list'] = '{type_legend},type,faqVariant;{config_legend},faqCategories;{text_legend},eyebrow,introTitle;{featured_legend},featuredTitle,text,introPrimaryUrl,introPrimaryText;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID;{invisible_legend:hide},invisible,start,stop';

$GLOBALS['TL_DCA']['tl_content']['fields']['faqVariant'] = [
    'inputType' => 'select',
    'options' => MilicaLazic\Controller\ContentElement\FaqListController::VARIANTS,
    'reference' => &$GLOBALS['TL_LANG']['tl_content']['faqVariants'],
    'eval' => ['tl_class' => 'w50'],
    'sql' => ['type' => 'string', 'length' => 16, 'default' => 'home'],
];

$GLOBALS['TL_DCA']['tl_content']['fields']['faqCategories'] = [
    'inputType' => 'checkboxWizard',
    'foreignKey' => 'tl_faq_category.title',
    'eval' => ['multiple' => true, 'mandatory' => true, 'tl_class' => 'clr'],
    'sql' => ['type' => 'blob', 'notnull' => false],
    'relation' => ['type' => 'hasMany', 'load' => 'lazy'],
];

$GLOBALS['TL_DCA']['tl_content']['fields']['featuredTitle'] = [
    'inputType' => 'text',
    'eval' => ['maxlength' => 255, 'tl_class' => 'clr long'],
    'sql' => ['type' => 'string', 'length' => 255, 'default' => ''],
];
