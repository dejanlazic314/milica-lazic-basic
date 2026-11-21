<?php

declare(strict_types=1);

$GLOBALS['TL_DCA']['tl_content']['palettes']['quote_photo'] = '{type_legend},type;{text_legend},quote,quoteSource,quoteText;{link_legend},quoteLink,linkTitle;{image_legend},quoteImage;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID;{invisible_legend:hide},invisible,start,stop';

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

// Own link and image fields: the core "url" and "singleSRC" fields are mandatory.
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

$GLOBALS['TL_DCA']['tl_content']['fields']['introTitle'] = [
    'inputType' => 'text',
    'eval' => ['mandatory' => true, 'maxlength' => 255, 'tl_class' => 'clr long'],
    'sql' => ['type' => 'string', 'length' => 255, 'default' => ''],
];

$GLOBALS['TL_DCA']['tl_content']['fields']['introText'] = [
    'inputType' => 'textarea',
    'eval' => ['rows' => 3, 'tl_class' => 'clr'],
    'sql' => ['type' => 'text', 'notnull' => false],
];

$GLOBALS['TL_DCA']['tl_content']['fields']['introMeta'] = [
    'inputType' => 'text',
    'eval' => ['maxlength' => 255, 'tl_class' => 'clr long'],
    'sql' => ['type' => 'string', 'length' => 255, 'default' => ''],
];

foreach (['introPrimaryUrl', 'introSecondaryUrl'] as $field) {
    $GLOBALS['TL_DCA']['tl_content']['fields'][$field] = [
        'inputType' => 'text',
        'eval' => ['rgxp' => 'url', 'decodeEntities' => true, 'maxlength' => 2048, 'dcaPicker' => true, 'tl_class' => 'w50 clr'],
        'sql' => ['type' => 'string', 'length' => 2048, 'default' => ''],
    ];
}

foreach (['introPrimaryText', 'introSecondaryText'] as $field) {
    $GLOBALS['TL_DCA']['tl_content']['fields'][$field] = [
        'inputType' => 'text',
        'eval' => ['maxlength' => 64, 'tl_class' => 'w50'],
        'sql' => ['type' => 'string', 'length' => 64, 'default' => ''],
    ];
}

$GLOBALS['TL_DCA']['tl_content']['fields']['introImage'] = [
    'inputType' => 'fileTree',
    'eval' => ['filesOnly' => true, 'fieldType' => 'radio', 'extensions' => '%contao.image.valid_extensions%', 'tl_class' => 'clr'],
    'sql' => ['type' => 'binary', 'length' => 16, 'fixed' => true, 'notnull' => false],
];

/*
 * Home page hero: shares title, lead, buttons and meta (status line) with the page intro.
 */
$GLOBALS['TL_DCA']['tl_content']['palettes']['hero'] = '{type_legend},type;{text_legend},eyebrow,introTitle,introText,introMeta;{link_legend},introPrimaryUrl,introPrimaryText,introSecondaryUrl,introSecondaryText;{image_legend},introImage;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID;{invisible_legend:hide},invisible,start,stop';

$GLOBALS['TL_DCA']['tl_content']['fields']['eyebrow'] = [
    'inputType' => 'text',
    'eval' => ['maxlength' => 128, 'tl_class' => 'w50'],
    'sql' => ['type' => 'string', 'length' => 128, 'default' => ''],
];

/*
 * Topic grid: cards from the subpages of a page. Shares heading, lead, note and link fields.
 */
$GLOBALS['TL_DCA']['tl_content']['palettes']['topic_grid'] = '{type_legend},type,topicsVariant;{config_legend},topicsParent;{text_legend},introTitle,introText,introMeta;{link_legend},introPrimaryUrl,introPrimaryText;{image_legend},ornamentImage;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID;{invisible_legend:hide},invisible,start,stop';

$GLOBALS['TL_DCA']['tl_content']['fields']['topicsVariant'] = [
    'inputType' => 'select',
    'options' => MilicaLazic\Controller\ContentElement\TopicGridController::VARIANTS,
    'reference' => &$GLOBALS['TL_LANG']['tl_content']['topicsVariants'],
    'eval' => ['tl_class' => 'w50'],
    'sql' => ['type' => 'string', 'length' => 16, 'default' => 'home'],
];

$GLOBALS['TL_DCA']['tl_content']['fields']['topicsParent'] = [
    'inputType' => 'pageTree',
    'foreignKey' => 'tl_page.title',
    'eval' => ['mandatory' => true, 'fieldType' => 'radio', 'tl_class' => 'clr'],
    'sql' => ['type' => 'integer', 'unsigned' => true, 'default' => 0],
    'relation' => ['type' => 'hasOne', 'load' => 'lazy'],
];

/*
 * Exercise (variant A or B). The contact link falls back to the contact page of the root page.
 */
$GLOBALS['TL_DCA']['tl_content']['palettes']['exercise'] = '{type_legend},type,exerciseVariant;{config_legend},exerciseFolder,introPrimaryUrl;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID;{invisible_legend:hide},invisible,start,stop';

$GLOBALS['TL_DCA']['tl_content']['fields']['exerciseVariant'] = [
    'inputType' => 'select',
    'options' => MilicaLazic\Controller\ContentElement\ExerciseController::VARIANTS,
    'reference' => &$GLOBALS['TL_LANG']['tl_content']['exerciseVariants'],
    'eval' => ['tl_class' => 'w50'],
    'sql' => ['type' => 'string', 'length' => 1, 'default' => 'a'],
];

$GLOBALS['TL_DCA']['tl_content']['fields']['exerciseFolder'] = [
    'inputType' => 'fileTree',
    'eval' => ['fieldType' => 'radio', 'files' => false, 'mandatory' => true, 'tl_class' => 'clr'],
    'sql' => ['type' => 'binary', 'length' => 16, 'fixed' => true, 'notnull' => false],
];

/*
 * #gestalt section (nested elements below), card group (nested cards) and icon card.
 */
$GLOBALS['TL_DCA']['tl_content']['palettes']['gestalt_intro'] = '{type_legend},type;{text_legend},eyebrow,introTitle,introText,text;{link_legend},introPrimaryUrl,introPrimaryText;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID;{invisible_legend:hide},invisible,start,stop';

$GLOBALS['TL_DCA']['tl_content']['palettes']['card_group'] = '{type_legend},type,cardsVariant;{text_legend},introTitle;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID;{invisible_legend:hide},invisible,start,stop';

$GLOBALS['TL_DCA']['tl_content']['palettes']['icon_card'] = '{type_legend},type,cardIcon;{text_legend},introTitle,introText;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID;{invisible_legend:hide},invisible,start,stop';

$GLOBALS['TL_DCA']['tl_content']['fields']['cardsVariant'] = [
    'inputType' => 'select',
    'options' => MilicaLazic\Controller\ContentElement\CardGroupController::VARIANTS,
    'reference' => &$GLOBALS['TL_LANG']['tl_content']['cardsVariants'],
    'eval' => ['tl_class' => 'w50'],
    'sql' => ['type' => 'string', 'length' => 16, 'default' => 'how'],
];

$GLOBALS['TL_DCA']['tl_content']['fields']['cardIcon'] = [
    'inputType' => 'select',
    'options' => MilicaLazic\Controller\ContentElement\IconCardController::ICONS,
    'reference' => &$GLOBALS['TL_LANG']['tl_content']['cardIcons'],
    'eval' => ['includeBlankOption' => true, 'tl_class' => 'w50'],
    'sql' => ['type' => 'string', 'length' => 32, 'default' => ''],
];

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

// Decorative line drawing in a corner of a section (no alt text)
$GLOBALS['TL_DCA']['tl_content']['fields']['ornamentImage'] = [
    'inputType' => 'fileTree',
    'eval' => ['filesOnly' => true, 'fieldType' => 'radio', 'extensions' => 'svg', 'tl_class' => 'clr'],
    'sql' => ['type' => 'binary', 'length' => 16, 'fixed' => true, 'notnull' => false],
];

/*
 * Band: "online" (white with branch) or "cta" (apricot, end of subpages).
 */
$GLOBALS['TL_DCA']['tl_content']['palettes']['band'] = '{type_legend},type,bandVariant;{text_legend},introTitle,introText;{link_legend},introPrimaryUrl,introPrimaryText;{image_legend},ornamentImage;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID;{invisible_legend:hide},invisible,start,stop';

$GLOBALS['TL_DCA']['tl_content']['fields']['bandVariant'] = [
    'inputType' => 'select',
    'options' => MilicaLazic\Controller\ContentElement\BandController::VARIANTS,
    'reference' => &$GLOBALS['TL_LANG']['tl_content']['bandVariants'],
    'eval' => ['tl_class' => 'w50'],
    'sql' => ['type' => 'string', 'length' => 16, 'default' => 'cta'],
];

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

/*
 * Contact block: contact rows from the root page, map behind consent, form nested inside.
 */
$GLOBALS['TL_DCA']['tl_content']['palettes']['contact_block'] = '{type_legend},type,sectionStyle;{text_legend},eyebrow,introTitle,introText;{map_legend},mapEmbedUrl,mapLinkUrl;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID;{invisible_legend:hide},invisible,start,stop';

foreach (['mapEmbedUrl', 'mapLinkUrl'] as $field) {
    $GLOBALS['TL_DCA']['tl_content']['fields'][$field] = [
        'inputType' => 'text',
        'eval' => ['rgxp' => 'url', 'decodeEntities' => true, 'maxlength' => 2048, 'tl_class' => 'clr long'],
        'sql' => ['type' => 'string', 'length' => 2048, 'default' => ''],
    ];
}

/*
 * Generic subpage section (nested cards, steps, FAQ lists inside), card and approach step.
 */
$GLOBALS['TL_DCA']['tl_content']['palettes']['section'] = '{type_legend},type,sectionLayout;{style_legend},sectionStyle,sectionSpacing,sectionGrid;{text_legend},eyebrow,introTitle,introText,text,introMeta;{list_legend:hide},sectionRows,sectionChips;{map_legend:hide},mapEmbedUrl,mapLinkUrl;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID;{invisible_legend:hide},invisible,start,stop';

$GLOBALS['TL_DCA']['tl_content']['palettes']['card'] = '{type_legend},type,cardVariant,cardIcon;{text_legend},introTitle,text;{link_legend},introPrimaryUrl,introPrimaryText;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID;{invisible_legend:hide},invisible,start,stop';

$GLOBALS['TL_DCA']['tl_content']['palettes']['step'] = '{type_legend},type;{text_legend},introTitle,introText;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID;{invisible_legend:hide},invisible,start,stop';

$GLOBALS['TL_DCA']['tl_content']['fields']['sectionLayout'] = [
    'inputType' => 'select',
    'options' => array_keys(MilicaLazic\Controller\ContentElement\SectionController::LAYOUTS),
    'reference' => &$GLOBALS['TL_LANG']['tl_content']['sectionLayouts'],
    'eval' => ['tl_class' => 'w50'],
    'sql' => ['type' => 'string', 'length' => 16, 'default' => 'stack'],
];

$GLOBALS['TL_DCA']['tl_content']['fields']['sectionStyle'] = [
    'inputType' => 'select',
    'options' => MilicaLazic\Controller\ContentElement\SectionController::STYLES,
    'reference' => &$GLOBALS['TL_LANG']['tl_content']['sectionStyles'],
    'eval' => ['includeBlankOption' => true, 'tl_class' => 'w33'],
    'sql' => ['type' => 'string', 'length' => 16, 'default' => ''],
];

$GLOBALS['TL_DCA']['tl_content']['fields']['sectionSpacing'] = [
    'inputType' => 'select',
    'options' => array_keys(MilicaLazic\Controller\ContentElement\SectionController::SPACINGS),
    'reference' => &$GLOBALS['TL_LANG']['tl_content']['sectionSpacings'],
    'eval' => ['includeBlankOption' => true, 'tl_class' => 'w33'],
    'sql' => ['type' => 'string', 'length' => 16, 'default' => ''],
];

$GLOBALS['TL_DCA']['tl_content']['fields']['sectionGrid'] = [
    'inputType' => 'select',
    'options' => MilicaLazic\Controller\ContentElement\SectionController::GRIDS,
    'reference' => &$GLOBALS['TL_LANG']['tl_content']['sectionGrids'],
    'eval' => ['includeBlankOption' => true, 'tl_class' => 'w33'],
    'sql' => ['type' => 'string', 'length' => 16, 'default' => ''],
];

foreach (['sectionRows', 'sectionChips'] as $field) {
    $GLOBALS['TL_DCA']['tl_content']['fields'][$field] = [
        'inputType' => 'textarea',
        'eval' => ['rows' => 4, 'tl_class' => 'clr'],
        'sql' => ['type' => 'text', 'notnull' => false],
    ];
}

$GLOBALS['TL_DCA']['tl_content']['fields']['cardVariant'] = [
    'inputType' => 'select',
    'options' => MilicaLazic\Controller\ContentElement\CardController::VARIANTS,
    'reference' => &$GLOBALS['TL_LANG']['tl_content']['cardVariants'],
    'eval' => ['tl_class' => 'w50'],
    'sql' => ['type' => 'string', 'length' => 16, 'default' => 'white'],
];

/*
 * Long page with a table of contents (Geštalt): parts nested inside, the TOC is built
 * from their headings and CSS IDs. Summary box ("Ukratko") and author bio.
 */
$GLOBALS['TL_DCA']['tl_content']['palettes']['toc_article'] = '{type_legend},type;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID;{invisible_legend:hide},invisible,start,stop';

$GLOBALS['TL_DCA']['tl_content']['palettes']['article_part'] = '{type_legend},type;{text_legend},introTitle,showCircles,text;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID;{invisible_legend:hide},invisible,start,stop';

$GLOBALS['TL_DCA']['tl_content']['palettes']['summary'] = '{type_legend},type;{text_legend},eyebrow,text;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID;{invisible_legend:hide},invisible,start,stop';

$GLOBALS['TL_DCA']['tl_content']['palettes']['bio'] = '{type_legend},type;{text_legend},text;{link_legend},introPrimaryUrl,introPrimaryText;{image_legend},introImage;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID;{invisible_legend:hide},invisible,start,stop';

$GLOBALS['TL_DCA']['tl_content']['fields']['showCircles'] = [
    'inputType' => 'checkbox',
    'eval' => ['tl_class' => 'clr'],
    'sql' => ['type' => 'boolean', 'default' => false],
];

/*
 * 404 page block: heading, two buttons, topic links and an illustration.
 */
$GLOBALS['TL_DCA']['tl_content']['palettes']['error_404'] = '{type_legend},type;{text_legend},eyebrow,introTitle,introText,introMeta;{link_legend},introPrimaryUrl,introPrimaryText,introSecondaryUrl,introSecondaryText;{config_legend},topicsParent;{image_legend},ornamentImage;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID;{invisible_legend:hide},invisible,start,stop';

// Band: small empty space above (topic pages, after a white section)
$GLOBALS['TL_DCA']['tl_content']['palettes']['band'] = str_replace(',bandVariant;', ',bandVariant,bandSpacer;', $GLOBALS['TL_DCA']['tl_content']['palettes']['band']);

$GLOBALS['TL_DCA']['tl_content']['fields']['bandSpacer'] = [
    'inputType' => 'checkbox',
    'eval' => ['tl_class' => 'w50 m12'],
    'sql' => ['type' => 'boolean', 'default' => false],
];

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
