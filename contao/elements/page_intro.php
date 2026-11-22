<?php

declare(strict_types=1);

/*
 * Page intro (subpages): breadcrumb + H1 + lead + buttons + meta + illustration.
 * Nested elements (filter, "Ukratko", legal date) go inside.
 */
$GLOBALS['TL_DCA']['tl_content']['palettes']['page_intro'] = '{type_legend},type,introVariant;{text_legend},introTitle,introText,introMeta;{link_legend},introPrimaryUrl,introPrimaryText,introSecondaryUrl,introSecondaryText;{image_legend},introImage;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID;{invisible_legend:hide},invisible,start,stop';

$GLOBALS['TL_DCA']['tl_content']['fields']['introVariant'] = [
    'inputType' => 'select',
    'options' => MilicaLazic\Controller\ContentElement\PageIntroController::VARIANTS,
    'reference' => &$GLOBALS['TL_LANG']['tl_content']['introVariants'],
    'eval' => ['tl_class' => 'w50'],
    'sql' => ['type' => 'string', 'length' => 16, 'default' => 'default'],
];
