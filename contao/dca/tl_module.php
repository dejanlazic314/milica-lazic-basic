<?php

declare(strict_types=1);

// The header takes everything from the page tree and the root page (site data)
$GLOBALS['TL_DCA']['tl_module']['palettes']['site_header'] = '{title_legend},name,type;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID';

// The footer lists hand-picked pages; contact details come from the root page
$GLOBALS['TL_DCA']['tl_module']['palettes']['site_footer'] = '{title_legend},name,type;{nav_legend},pages,legalPages;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID';

$GLOBALS['TL_DCA']['tl_module']['fields']['legalPages'] = [
    'inputType' => 'pageTree',
    'foreignKey' => 'tl_page.title',
    'eval' => ['multiple' => true, 'fieldType' => 'checkbox', 'isSortable' => true, 'tl_class' => 'clr'],
    'sql' => ['type' => 'blob', 'notnull' => false],
    'relation' => ['type' => 'hasMany', 'load' => 'lazy'],
];

// Posts ("Tekstovi"): list with filter and "load more"; the same module shows a post
// when the URL has an alias (/tekstovi/{alias}). Bio and author photo for the post page.
$GLOBALS['TL_DCA']['tl_module']['palettes']['posts'] = '{title_legend},name,headline,type;{config_legend},news_archives,perPage,postsIntro;{bio_legend},postsBio,postsBioImage,postsBioPage;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID';

$GLOBALS['TL_DCA']['tl_module']['fields']['postsIntro'] = [
    'inputType' => 'textarea',
    'eval' => ['rows' => 2, 'tl_class' => 'clr'],
    'sql' => ['type' => 'text', 'notnull' => false],
];

$GLOBALS['TL_DCA']['tl_module']['fields']['postsBio'] = [
    'inputType' => 'textarea',
    'eval' => ['rte' => 'tinyMCE', 'basicEntities' => true, 'tl_class' => 'clr'],
    'sql' => ['type' => 'text', 'notnull' => false],
];

$GLOBALS['TL_DCA']['tl_module']['fields']['postsBioImage'] = [
    'inputType' => 'fileTree',
    'eval' => ['filesOnly' => true, 'fieldType' => 'radio', 'extensions' => '%contao.image.valid_extensions%', 'tl_class' => 'clr'],
    'sql' => ['type' => 'binary', 'length' => 16, 'fixed' => true, 'notnull' => false],
];

$GLOBALS['TL_DCA']['tl_module']['fields']['postsBioPage'] = [
    'inputType' => 'pageTree',
    'foreignKey' => 'tl_page.title',
    'eval' => ['fieldType' => 'radio', 'tl_class' => 'clr'],
    'sql' => ['type' => 'integer', 'unsigned' => true, 'default' => 0],
    'relation' => ['type' => 'hasOne', 'load' => 'lazy'],
];
