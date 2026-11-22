<?php

declare(strict_types=1);

/*
 * Exercise (variant A or B) with its topics as nested "exercise_topic" elements.
 * Heading texts, ending and button are fields here; the topics (choice, illustration,
 * steps, reflection, link) are the nested elements. Fixed labels (buttons, "korak 1 od 3")
 * are in translations/site.*.yaml (exercise.*). The contact link falls back to the
 * contact page of the root page.
 */
$GLOBALS['TL_DCA']['tl_content']['palettes']['exercise'] = '{type_legend},type,exerciseVariant;{text_legend},eyebrow,introTitle,introText;{end_legend},exerciseEndEyebrow,exerciseEndTitle;{link_legend},introPrimaryUrl,introPrimaryText;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID;{invisible_legend:hide},invisible,start,stop';

$GLOBALS['TL_DCA']['tl_content']['palettes']['exercise_topic'] = '{type_legend},type;{text_legend},introTitle,introImage,listitems,introText;{link_legend},introPrimaryUrl,introPrimaryText;{protected_legend:hide},protected;{invisible_legend:hide},invisible,start,stop';

$GLOBALS['TL_DCA']['tl_content']['fields']['exerciseVariant'] = [
    'inputType' => 'select',
    'options' => MilicaLazic\Controller\ContentElement\ExerciseController::VARIANTS,
    'reference' => &$GLOBALS['TL_LANG']['tl_content']['exerciseVariants'],
    'eval' => ['tl_class' => 'w50'],
    'sql' => ['type' => 'string', 'length' => 1, 'default' => 'a'],
];

$GLOBALS['TL_DCA']['tl_content']['fields']['exerciseEndEyebrow'] = [
    'inputType' => 'text',
    'eval' => ['maxlength' => 128, 'tl_class' => 'w50'],
    'sql' => ['type' => 'string', 'length' => 128, 'default' => ''],
];

$GLOBALS['TL_DCA']['tl_content']['fields']['exerciseEndTitle'] = [
    'inputType' => 'text',
    'eval' => ['maxlength' => 255, 'tl_class' => 'clr long'],
    'sql' => ['type' => 'string', 'length' => 255, 'default' => ''],
];

// Before 22. 11. 2026 the illustrations came from a folder. No longer in a palette; the column
// stays so src/Migration/ExerciseTextsMigration.php can still read it on older databases.
$GLOBALS['TL_DCA']['tl_content']['fields']['exerciseFolder'] = [
    'sql' => ['type' => 'binary', 'length' => 16, 'fixed' => true, 'notnull' => false],
];
