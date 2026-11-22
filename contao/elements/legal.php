<?php

declare(strict_types=1);

/*
 * Legal page (privacy policy, cookies): H1, dates, rich text with H2 sections.
 */
$GLOBALS['TL_DCA']['tl_content']['palettes']['legal'] = '{type_legend},type;{text_legend},introTitle,legalValidFrom,legalUpdated,text;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID;{invisible_legend:hide},invisible,start,stop';

$GLOBALS['TL_DCA']['tl_content']['fields']['legalValidFrom'] = [
    'inputType' => 'text',
    'eval' => ['rgxp' => 'date', 'datepicker' => true, 'tl_class' => 'w50 wizard'],
    'sql' => ['type' => 'integer', 'unsigned' => true, 'notnull' => false],
];

$GLOBALS['TL_DCA']['tl_content']['fields']['legalUpdated'] = [
    'inputType' => 'text',
    'eval' => ['rgxp' => 'date', 'datepicker' => true, 'tl_class' => 'w50 wizard'],
    'sql' => ['type' => 'integer', 'unsigned' => true, 'notnull' => false],
];
