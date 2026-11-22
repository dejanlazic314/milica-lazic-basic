<?php

declare(strict_types=1);

/*
 * Band: "online" (white with branch) or "cta" (apricot, end of subpages).
 */
$GLOBALS['TL_DCA']['tl_content']['palettes']['band'] = '{type_legend},type,bandVariant,bandSpacer;{text_legend},introTitle,introText;{link_legend},introPrimaryUrl,introPrimaryText;{image_legend},ornamentImage;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID;{invisible_legend:hide},invisible,start,stop';

$GLOBALS['TL_DCA']['tl_content']['fields']['bandVariant'] = [
    'inputType' => 'select',
    'options' => MilicaLazic\Controller\ContentElement\BandController::VARIANTS,
    'reference' => &$GLOBALS['TL_LANG']['tl_content']['bandVariants'],
    'eval' => ['tl_class' => 'w50'],
    'sql' => ['type' => 'string', 'length' => 16, 'default' => 'cta'],
];

// Small empty space above (topic pages, after a white section)
$GLOBALS['TL_DCA']['tl_content']['fields']['bandSpacer'] = [
    'inputType' => 'checkbox',
    'eval' => ['tl_class' => 'w50 m12'],
    'sql' => ['type' => 'boolean', 'default' => false],
];
