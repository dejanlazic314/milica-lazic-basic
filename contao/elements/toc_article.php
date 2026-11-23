<?php

declare(strict_types=1);

/*
 * Long page with a table of contents (Geštalt): parts nested inside, the TOC is built
 * from their headings and CSS IDs. Author bio.
 */
$GLOBALS['TL_DCA']['tl_content']['palettes']['toc_article'] = '{type_legend},type;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID;{invisible_legend:hide},invisible,start,stop';

$GLOBALS['TL_DCA']['tl_content']['palettes']['article_part'] = '{type_legend},type;{text_legend},introTitle,showCircles,text;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID;{invisible_legend:hide},invisible,start,stop';

// A part can be only a heading and the diagram (ElementFieldsListener)
$GLOBALS['TL_DCA']['tl_content']['optionalFields']['article_part'] = ['text'];

$GLOBALS['TL_DCA']['tl_content']['palettes']['bio'] = '{type_legend},type;{text_legend},text;{link_legend},introPrimaryUrl,introPrimaryText;{image_legend},introImage;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID;{invisible_legend:hide},invisible,start,stop';

$GLOBALS['TL_DCA']['tl_content']['fields']['showCircles'] = [
    'inputType' => 'checkbox',
    'eval' => ['tl_class' => 'clr'],
    'sql' => ['type' => 'boolean', 'default' => false],
];
