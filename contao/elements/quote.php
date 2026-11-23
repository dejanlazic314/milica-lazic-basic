<?php

declare(strict_types=1);

/*
 * Quote over a photo (full width). Words in *asterisks* are underlined in apricot.
 */
$GLOBALS['TL_DCA']['tl_content']['palettes']['quote'] = '{type_legend},type;{text_legend},quote,quoteSource,quoteText;{link_legend},quoteLink,linkTitle;{image_legend},quoteImage;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID;{invisible_legend:hide},invisible,start,stop';

$GLOBALS['TL_DCA']['tl_content']['fields']['quote'] = [
    'inputType' => 'textarea',
    'eval' => ['mandatory' => true, 'rows' => 3, 'tl_class' => 'clr'],
    'sql' => ['type' => 'text', 'notnull' => false],
];

$GLOBALS['TL_DCA']['tl_content']['fields']['quoteSource'] = [
    'inputType' => 'text',
    'eval' => ['maxlength' => 255, 'tl_class' => 'w50 clr'],
    'sql' => ['type' => 'string', 'length' => 255, 'default' => ''],
];

$GLOBALS['TL_DCA']['tl_content']['fields']['quoteText'] = [
    'inputType' => 'textarea',
    'eval' => ['rows' => 4, 'tl_class' => 'clr'],
    'sql' => ['type' => 'text', 'notnull' => false],
];

$GLOBALS['TL_DCA']['tl_content']['fields']['quoteLink'] = [
    'inputType' => 'text',
    'eval' => ['rgxp' => 'url', 'decodeEntities' => true, 'maxlength' => 2048, 'dcaPicker' => true, 'tl_class' => 'w50'],
    'sql' => ['type' => 'string', 'length' => 2048, 'default' => ''],
];

$GLOBALS['TL_DCA']['tl_content']['fields']['quoteImage'] = [
    'inputType' => 'fileTree',
    'eval' => ['filesOnly' => true, 'fieldType' => 'radio', 'extensions' => '%contao.image.valid_extensions%', 'tl_class' => 'clr'],
    'sql' => ['type' => 'binary', 'length' => 16, 'fixed' => true, 'notnull' => false],
];
