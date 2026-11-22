<?php

declare(strict_types=1);

/*
 * Home page hero: shares title, lead, buttons and meta (status line) with the page intro.
 */
$GLOBALS['TL_DCA']['tl_content']['palettes']['hero'] = '{type_legend},type;{text_legend},eyebrow,introTitle,introText,introMeta;{link_legend},introPrimaryUrl,introPrimaryText,introSecondaryUrl,introSecondaryText;{image_legend},introImage;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID;{invisible_legend:hide},invisible,start,stop';
