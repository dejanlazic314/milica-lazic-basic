<?php

declare(strict_types=1);

$GLOBALS['TL_LANG']['tl_content']['quote'] = ['Quote', 'Wrap words in *asterisks* to highlight them, e.g. "where we are *now*".'];
$GLOBALS['TL_LANG']['tl_content']['quoteSource'] = ['Source', 'Shown below the quote.'];
$GLOBALS['TL_LANG']['tl_content']['quoteText'] = ['Text', 'Short paragraph below the source.'];
$GLOBALS['TL_LANG']['tl_content']['quoteLink'] = ['Link', 'Optional link below the text.'];
$GLOBALS['TL_LANG']['tl_content']['quoteImage'] = ['Background photo', 'Optional. Without a photo the section is dark sage.'];

$GLOBALS['TL_LANG']['tl_content']['layout_legend'] = 'Layout';
$GLOBALS['TL_LANG']['tl_content']['heroLayout'] = ['Layout', 'Background photo: the photo fills the block and the text sits on it (home page). Text and illustration: text left, drawing right. Text only: no image. The form reloads and shows the fields of the layout.'];
$GLOBALS['TL_LANG']['tl_content']['heroLayouts'] = ['background' => 'Background photo', 'split' => 'Text and illustration', 'plain' => 'Text only'];
$GLOBALS['TL_LANG']['tl_content']['heroWidth'] = ['Text width', 'Narrow: legal pages and posts. Wide: a filter row below the lead.'];
$GLOBALS['TL_LANG']['tl_content']['heroWidths'] = ['default' => 'Default', 'narrow' => 'Narrow', 'wide' => 'Wide (with filter)'];
$GLOBALS['TL_LANG']['tl_content']['introTitle'] = ['Heading (H1)', 'Wrap words in *asterisks* to set them in italics, e.g. "Šta je geštalt *terapija*?".'];
$GLOBALS['TL_LANG']['tl_content']['introText'] = ['Lead', 'One or two sentences below the heading.'];
$GLOBALS['TL_LANG']['tl_content']['introMeta'] = ['Meta line', 'Small text below the buttons, e.g. "Uživo u Beogradu i online · Autorka: [Milica Lazić](/o-meni)". [Text](link) becomes a link, *words* italic.'];
$GLOBALS['TL_LANG']['tl_content']['introPrimaryUrl'] = ['Main button link', 'Apricot button. Anchors like "#kontakt" work too.'];
$GLOBALS['TL_LANG']['tl_content']['introPrimaryText'] = ['Main button text', ''];
$GLOBALS['TL_LANG']['tl_content']['introSecondaryUrl'] = ['Second button link', 'Outline button.'];
$GLOBALS['TL_LANG']['tl_content']['introSecondaryText'] = ['Second button text', ''];
$GLOBALS['TL_LANG']['tl_content']['introImage'] = ['Image', 'Alt text comes from the file manager.'];
$GLOBALS['TL_LANG']['tl_content']['eyebrow'] = ['Eyebrow', 'Small uppercase line above the heading, e.g. "Beograd · uživo i online".'];
$GLOBALS['TL_LANG']['tl_content']['topicsVariant'] = ['Layout', 'Home page: heading, lead and corner ornament. Overview: note and link below the cards.'];
$GLOBALS['TL_LANG']['tl_content']['topicsVariants'] = ['home' => 'Home page', 'overview' => 'Overview ("Oblasti rada")'];
$GLOBALS['TL_LANG']['tl_content']['topicsParent'] = ['Topics page', 'The cards are the published subpages of this page, with their card text and illustration.'];
$GLOBALS['TL_LANG']['tl_content']['exerciseVariant'] = ['Variant', 'A: choose a topic, then its steps one by one with a breathing circle. B: the steps on a list. Both are on the home page while testing. The topics are the "Exercise topic" elements inside.'];
$GLOBALS['TL_LANG']['tl_content']['exerciseVariants'] = ['a' => 'A (questions, breathing circle)', 'b' => 'B (steps)'];
$GLOBALS['TL_LANG']['tl_content']['end_legend'] = 'End of the exercise';

$GLOBALS['TL_LANG']['tl_content']['boxStyle'] = ['Style', 'White: a white box with a border, e.g. "Ukratko". Dark: dark sage, e.g. the crisis numbers on a topic page.'];
$GLOBALS['TL_LANG']['tl_content']['boxStyles'] = ['white' => 'White', 'dark' => 'Dark'];
$GLOBALS['TL_LANG']['tl_content']['boxTitle'] = ['Title', 'Optional first line in bold, e.g. "Ako imate misli o samopovređivanju…".'];

// Labels of shared fields for elements where they mean something else (ElementFieldsListener)
$GLOBALS['TL_LANG']['tl_content']['labels']['card'] = [
    'introTitle' => ['Heading', 'The heading of the card (H3).'],
    'introPrimaryUrl' => ['Link', 'Optional arrow link below the text.'],
    'introPrimaryText' => ['Link text', ''],
];
$GLOBALS['TL_LANG']['tl_content']['labels']['hero'] = [
    'introImage' => ['Image', 'Background photo: the photo behind the text. Text and illustration: the drawing on the right (SVG). Alt text comes from the file manager.'],
];
$GLOBALS['TL_LANG']['tl_content']['labels']['exercise'] = [
    'introTitle' => ['Heading', 'Wrap words in *asterisks* to set them in italics, e.g. "Šta je kod vas *sada* prisutno?".'],
    'introText' => ['Text below the heading', 'Variant A: below the heading. Variant B: the line above the steps, e.g. "Probajte sada, polako, jedno po jedno".'],
    'introPrimaryUrl' => ['Contact link', 'Button at the end. Empty: the contact page of the root page. "#kontakt" goes to the contact block on the same page.'],
    'introPrimaryText' => ['Contact button text', 'E.g. "Zakažite prvi razgovor".'],
];
$GLOBALS['TL_LANG']['tl_content']['labels']['exercise_topic'] = [
    'introTitle' => ['Choice', 'What the visitor picks, e.g. "Nemir koji ne prestaje".'],
    'introImage' => ['Illustration', 'Line drawing next to the choice (SVG).'],
    'listitems' => ['Steps', 'One question or step per line, in order. Usually three.'],
    'introText' => ['Reflection at the end', 'Shown after the last step.'],
    'introPrimaryUrl' => ['Topic page', 'Link at the end, e.g. the page "Anksioznost i stres".'],
    'introPrimaryText' => ['Link text', 'E.g. "Terapija za anksioznost".'],
];
$GLOBALS['TL_LANG']['tl_content']['exerciseEndEyebrow'] = ['End: eyebrow', 'Variant A only, e.g. "Upravo ste uradili nešto važno".'];
$GLOBALS['TL_LANG']['tl_content']['exerciseEndTitle'] = ['End: heading', 'Shown with the reflection of the chosen topic, e.g. "Zastali ste i primetili. To je srž geštalt terapije."'];
$GLOBALS['TL_LANG']['tl_content']['cta_legend'] = 'Contact card at the end';
$GLOBALS['TL_LANG']['tl_content']['ctaQuote'] = ['Card heading', 'E.g. "Ne vidite svoju temu ovde?". Leave empty to hide the card. The card links to the contact page of the root page.'];
$GLOBALS['TL_LANG']['tl_content']['ctaText'] = ['Card text', ''];
$GLOBALS['TL_LANG']['tl_content']['ctaLinkText'] = ['Card link text', 'E.g. "Pišite mi →".'];
$GLOBALS['TL_LANG']['tl_content']['cardsVariant'] = ['Layout', 'How the group and its cards look.'];
$GLOBALS['TL_LANG']['tl_content']['cardsVariants'] = ['how' => '"Kako izgleda saradnja" (dark section)'];
$GLOBALS['TL_LANG']['tl_content']['cardIcon'] = ['Icon', 'Symbol from the icon set of the design.'];
$GLOBALS['TL_LANG']['tl_content']['cardIcons'] = ['i-chat' => 'Conversation', 'i-calendar' => 'Calendar', 'i-sprout' => 'Sprout', 'i-clock' => 'Clock', 'i-lock' => 'Lock', 'i-video' => 'Video', 'i-location' => 'Location', 'i-phone' => 'Phone', 'i-email' => 'E-mail', 'i-check' => 'Check'];
$GLOBALS['TL_LANG']['tl_content']['aboutVariant'] = ['Layout', 'Home page: eyebrow, H2, credentials, ornament. Page intro: breadcrumb, H1, two buttons.'];
$GLOBALS['TL_LANG']['tl_content']['aboutVariants'] = ['home' => 'Home page (#o-meni)', 'intro' => 'Top of the page "O meni"'];
$GLOBALS['TL_LANG']['tl_content']['credentials'] = ['Credentials', 'One per line as "Label: value", e.g. "Edukacija: Geštalt institut".'];
$GLOBALS['TL_LANG']['tl_content']['ornamentImage'] = ['Ornament', 'Decorative line drawing in the corner (SVG, no alt text).'];
$GLOBALS['TL_LANG']['tl_content']['bandVariant'] = ['Layout', 'Online: white band with the branch ornament. CTA: apricot band at the end of a page; without a link the button goes to the contact page.'];
$GLOBALS['TL_LANG']['tl_content']['bandVariants'] = ['online' => 'Online (white, ornament)', 'cta' => 'Call to action (apricot)'];
$GLOBALS['TL_LANG']['tl_content']['faqVariant'] = ['Layout', 'Home page: featured question and the questions marked "Show on home page". FAQ page: all questions, one group per category, with FAQ structured data.'];
$GLOBALS['TL_LANG']['tl_content']['faqVariants'] = ['home' => 'Home page (#pitanja)', 'page' => 'FAQ page'];
$GLOBALS['TL_LANG']['tl_content']['faqCategories'] = ['FAQ categories', 'Categories in this order.'];
$GLOBALS['TL_LANG']['tl_content']['featured_legend'] = 'Featured question (home page)';
$GLOBALS['TL_LANG']['tl_content']['featuredTitle'] = ['Featured question', 'Heading of the apricot box, e.g. "Kako izgleda prva seansa?". The text field below is its answer.'];
$GLOBALS['TL_LANG']['tl_content']['newsArchives'] = ['News archives', 'The three latest posts from these archives are shown.'];
$GLOBALS['TL_LANG']['tl_content']['map_legend'] = 'Map';
$GLOBALS['TL_LANG']['tl_content']['mapEmbedUrl'] = ['Map embed URL', 'Google Maps → Share → Embed a map → the src of the iframe. Loaded only after the visitor allows maps.'];
$GLOBALS['TL_LANG']['tl_content']['mapLinkUrl'] = ['Map link', 'Link "Otvori u Google mapama" (opens in a new tab, no cookies).'];

$GLOBALS['TL_LANG']['tl_content']['style_legend'] = 'Background and spacing';
$GLOBALS['TL_LANG']['tl_content']['list_legend'] = 'Rows and chips';
$GLOBALS['TL_LANG']['tl_content']['sectionLayout'] = ['Layout', 'Section layouts from the design. Cards, steps and FAQ lists go inside as nested elements.'];
$GLOBALS['TL_LANG']['tl_content']['sectionLayouts'] = [
    'stack' => 'Heading + content (cards in a grid)',
    'recognition' => '"Možda prepoznajete" (cards + closing line)',
    'approach' => 'Approach (numbered steps)',
    'my_path' => 'Heading left, text right ("Moj put")',
    'preparation' => 'Text left, dark card right',
    'diaspora' => 'Text + time zones',
    'education' => 'Education (rows + chips)',
    'location' => 'Location + map',
    'two_cards' => 'Two cards',
];
$GLOBALS['TL_LANG']['tl_content']['sectionStyle'] = ['Background', 'Default is the page background (cream).'];
$GLOBALS['TL_LANG']['tl_content']['sectionStyles'] = ['white' => 'White', 'warm' => 'Warm (apricot)'];
$GLOBALS['TL_LANG']['tl_content']['sectionSpacing'] = ['Spacing', 'Default: full padding top and bottom.'];
$GLOBALS['TL_LANG']['tl_content']['sectionSpacings'] = ['no_top' => 'No space above', 'tight' => 'No space above, small below', 'small' => 'Small above and below'];
$GLOBALS['TL_LANG']['tl_content']['sectionGrid'] = ['Grid', 'How the nested cards are laid out (layouts "Heading + content" and "Možda prepoznajete").'];
$GLOBALS['TL_LANG']['tl_content']['sectionGrids'] = ['grid--2' => '2 columns', 'grid--3' => '3 columns', 'grid--3-tablet' => '3 columns (stacked on tablet)', 'grid--4' => '4 columns', 'after-message' => 'Steps "Šta se dešava kada mi pišete"'];
$GLOBALS['TL_LANG']['tl_content']['sectionRows'] = ['Rows', 'One per line, columns separated by "|". Education: "year | name | place". Time zones: "city | difference". Preparation: one checklist item per line.'];
$GLOBALS['TL_LANG']['tl_content']['sectionChips'] = ['Chips', 'One per line (education: memberships and languages).'];
$GLOBALS['TL_LANG']['tl_content']['cardVariant'] = ['Look', 'Outline: a light frame for a dark section ("Kako izgleda saradnja"). Plain: no background (inside "Šta se dešava kada mi pišete").'];
$GLOBALS['TL_LANG']['tl_content']['cardVariants'] = ['white' => 'White', 'dark' => 'Dark sage', 'green' => 'Light green', 'sand' => 'Sand', 'outline' => 'Outline (on dark)', 'plain' => 'Plain'];
$GLOBALS['TL_LANG']['tl_content']['cardSize'] = ['Size', 'Small: smaller heading ("Možda prepoznajete"). Large: more room, for a single card next to a text. Compact: sans heading and smaller text, for cards with an icon.'];
$GLOBALS['TL_LANG']['tl_content']['cardSizes'] = ['default' => 'Default', 'small' => 'Small', 'large' => 'Large', 'compact' => 'Compact'];
$GLOBALS['TL_LANG']['tl_content']['showCircles'] = ['Show the circles diagram', 'Thoughts, feelings, body (light version) below the heading.'];
$GLOBALS['TL_LANG']['tl_content']['bandSpacer'] = ['Space above', 'Adds a small gap when the band follows a white section.'];

$GLOBALS['TL_LANG']['tl_content']['legalValidFrom'] = ['Valid from', 'Shown as "Važi od …". Empty: not shown.'];
$GLOBALS['TL_LANG']['tl_content']['legalUpdated'] = ['Last changed', 'Change it whenever the text changes.'];
