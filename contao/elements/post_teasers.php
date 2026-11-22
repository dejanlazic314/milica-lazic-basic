<?php

declare(strict_types=1);

/*
 * Latest posts (home page).
 */
$GLOBALS['TL_DCA']['tl_content']['palettes']['post_teasers'] = '{type_legend},type;{config_legend},newsArchives;{text_legend},eyebrow,introTitle;{link_legend},introPrimaryUrl,introPrimaryText;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID;{invisible_legend:hide},invisible,start,stop';

$GLOBALS['TL_DCA']['tl_content']['fields']['newsArchives'] = [
    'inputType' => 'checkbox',
    'foreignKey' => 'tl_news_archive.title',
    'eval' => ['multiple' => true, 'mandatory' => true, 'tl_class' => 'clr'],
    'sql' => ['type' => 'blob', 'notnull' => false],
    'relation' => ['type' => 'hasMany', 'load' => 'lazy'],
];
