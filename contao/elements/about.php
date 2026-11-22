<?php

declare(strict_types=1);

/*
 * "O meni" block (home page and top of the page "O meni").
 */
$GLOBALS['TL_DCA']['tl_content']['palettes']['about'] = '{type_legend},type,aboutVariant;{text_legend},eyebrow,introTitle,quote,introText,credentials;{link_legend},introPrimaryUrl,introPrimaryText,introSecondaryUrl,introSecondaryText;{image_legend},introImage,ornamentImage;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID;{invisible_legend:hide},invisible,start,stop';

$GLOBALS['TL_DCA']['tl_content']['fields']['aboutVariant'] = [
    'inputType' => 'select',
    'options' => MilicaLazic\Controller\ContentElement\AboutController::VARIANTS,
    'reference' => &$GLOBALS['TL_LANG']['tl_content']['aboutVariants'],
    'eval' => ['tl_class' => 'w50'],
    'sql' => ['type' => 'string', 'length' => 16, 'default' => 'home'],
];

$GLOBALS['TL_DCA']['tl_content']['fields']['credentials'] = [
    'inputType' => 'textarea',
    'eval' => ['rows' => 3, 'tl_class' => 'clr'],
    'sql' => ['type' => 'text', 'notnull' => false],
];
