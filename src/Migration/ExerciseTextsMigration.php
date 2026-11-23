<?php

declare(strict_types=1);

namespace MilicaLazic\Migration;

use Contao\CoreBundle\Migration\AbstractMigration;
use Contao\CoreBundle\Migration\MigrationResult;
use Doctrine\DBAL\Connection;

/**
 * One-off (22. 11. 2026): texts that used to be in the code move to the back end.
 *
 * - Exercise: the heading texts go into the element's fields, and each topic becomes a
 *   nested "exercise_topic" element (choice, illustration, steps, reflection, link).
 *   Before, all of it was in public/layout/js/elements/exercise.js.
 * - Topic grid: the contact card ("Ne vidite svoju temu ovde?") gets its own fields.
 *   Before, the text was in translations/site.*.yaml (topics.cta_*).
 *
 * Runs while an exercise element still has the old illustrations folder (exerciseFolder)
 * and clears it afterwards, so it runs only once and never overwrites later edits.
 * The texts below are the ones that were on the site on 22. 11. 2026.
 */
class ExerciseTextsMigration extends AbstractMigration
{
    private const CONTENT = [
        'sr' => [
            'a' => [
                'eyebrow' => 'Mali eksperiment · 1 minut',
                'introTitle' => 'Šta je kod vas *sada* prisutno?',
                'introText' => 'Geštalt terapija počinje od onoga što se dešava ovde i sada. Izaberite ono što vam je najbliže i probajte kratku vežbu.',
            ],
            'b' => [
                'eyebrow' => 'Eksperiment svesnosti',
                'introTitle' => 'Šta vam je danas najbliže?',
                'introText' => 'Probajte sada, polako, jedno po jedno',
            ],
            'exerciseEndEyebrow' => 'Upravo ste uradili nešto važno',
            'exerciseEndTitle' => 'Zastali ste i primetili. To je srž geštalt terapije.',
            'introPrimaryText' => 'Zakažite prvi razgovor',
        ],
        'en' => [
            'a' => [
                'eyebrow' => 'A small experiment · 1 minute',
                'introTitle' => 'What is present for you *right now*?',
                'introText' => 'Gestalt therapy starts from what is happening here and now. Choose what feels closest to you and try a short exercise.',
            ],
            'b' => [
                'eyebrow' => 'An awareness experiment',
                'introTitle' => 'What feels closest to you today?',
                'introText' => 'Try it now, slowly, one at a time',
            ],
            'exerciseEndEyebrow' => 'You have just done something important',
            'exerciseEndTitle' => 'You paused and noticed. That is the heart of Gestalt therapy.',
            'introPrimaryText' => 'Book a first conversation',
        ],
    ];

    // Topics in the order of the old script; "page" is the alias of the topic page
    private const TOPICS = [
        'sr' => [
            [
                'choice' => 'Nemir koji ne prestaje',
                'image' => 'tema-anksioznost.svg',
                'steps' => [
                    'Spustite stopala na pod. Primetite podlogu ispod sebe.',
                    'Gde u telu sada osećate nemir? U grudima, stomaku, grlu? Samo primetite, ne menjajte ništa.',
                    'Udahnite malo sporije nego inače. Šta se dešava sa tim mestom?',
                ],
                'reflection' => 'Nemir je često signal, a ne neprijatelj. U terapiji zajedno istražujemo šta pokušava da vam kaže, umesto da se samo borite protiv njega.',
                'linkText' => 'Terapija za anksioznost',
                'page' => 's-cim-radim/anksioznost',
            ],
            [
                'choice' => 'Umor koji san ne popravlja',
                'image' => 'tema-burnout.svg',
                'steps' => [
                    'Primetite kako sedite. Da li se telo naslanja ili se drži?',
                    'Pustite ramena da se spuste za centimetar. Šta se promenilo?',
                    'Zapitajte se: kome ili čemu danas kažete „da“, a zapravo želite da kažete „ne“?',
                ],
                'reflection' => 'Iscrpljenost često govori o granicama koje su dugo bile pomerane. U terapiji istražujemo gde se energija gubi i šta vam je zaista potrebno.',
                'linkText' => 'Terapija za burnout',
                'page' => 's-cim-radim/burnout',
            ],
            [
                'choice' => 'Isti sukob, iznova',
                'image' => 'tema-odnosi.svg',
                'steps' => [
                    'Setite se jedne uznemiravajuće situacije, na primer poslednje.',
                    'Šta ste tada osećali u telu, pre nego što ste bilo šta rekli?',
                    'Šta ste zapravo želeli da druga osoba čuje?',
                ],
                'reflection' => 'Obrasci u odnosima se ponavljaju jer su nekada imali smisla. Kada ih primetimo, dobijamo izbor da odgovorimo drugačije.',
                'linkText' => 'Terapija za odnose',
                'page' => 's-cim-radim/odnosi',
            ],
            [
                'choice' => 'Na raskrsnici sam',
                'image' => 'tema-promene.svg',
                'steps' => [
                    'Zamislite dva puta ispred sebe. Ne birajte, samo ih pogledajte.',
                    'Na koji put telo lakše kreće? Gde se nešto steže?',
                    'Šta biste izgubili, a šta dobili, ako ništa ne promenite?',
                ],
                'reflection' => 'Promene retko traže brzu odluku. Češće traže da čujemo sve delove sebe, i one koji žele dalje i one koji se plaše.',
                'linkText' => 'Životne promene i krize',
                'page' => 's-cim-radim/gubitak-i-promene',
            ],
            [
                'choice' => 'Kao roditelj, ne snalazim se',
                'image' => 'tema-roditeljstvo.svg',
                'steps' => [
                    'Setite se trenutka sa detetom koji vas je juče ili danas pogodio.',
                    'Šta ste vi osećali? Ne dete, nego vi.',
                    'Da li vam je to osećanje poznato od ranije, iz vremena kad ste i sami bili dete?',
                ],
                'reflection' => 'Roditeljstvo nas često vraća u sopstveno detinjstvo. Kada razumemo šta se u nama budi i zašto, lakše ostajemo prisutni uz dete.',
                'linkText' => 'Podrška roditeljima',
                'page' => 's-cim-radim/roditeljstvo',
            ],
        ],
        'en' => [
            [
                'choice' => 'Restlessness that won’t stop',
                'image' => 'tema-anksioznost.svg',
                'steps' => [
                    'Put your feet on the floor. Notice the ground beneath you.',
                    'Where in your body do you feel the restlessness right now? In your chest, your stomach, your throat? Just notice, don’t change anything.',
                    'Breathe in a little more slowly than usual. What happens in that place?',
                ],
                'reflection' => 'Restlessness is often a signal, not an enemy. In therapy we explore together what it is trying to tell you, instead of only fighting it.',
                'linkText' => 'Therapy for anxiety',
                'page' => 'what-i-work-with/anxiety',
            ],
            [
                'choice' => 'Tiredness that sleep doesn’t fix',
                'image' => 'tema-burnout.svg',
                'steps' => [
                    'Notice how you are sitting. Is your body leaning back or holding itself up?',
                    'Let your shoulders drop by a centimetre. What has changed?',
                    'Ask yourself: to whom or what do you say “yes” today, when you actually want to say “no”?',
                ],
                'reflection' => 'Exhaustion often speaks of boundaries that have been pushed for a long time. In therapy we explore where your energy goes and what you really need.',
                'linkText' => 'Therapy for burnout',
                'page' => 'what-i-work-with/burnout',
            ],
            [
                'choice' => 'The same conflict, again',
                'image' => 'tema-odnosi.svg',
                'steps' => [
                    'Think of one upsetting situation, for example the most recent one.',
                    'What did you feel in your body then, before you said anything?',
                    'What did you actually want the other person to hear?',
                ],
                'reflection' => 'Patterns in relationships repeat because they once made sense. When we notice them, we get the choice to respond differently.',
                'linkText' => 'Therapy for relationships',
                'page' => 'what-i-work-with/relationships',
            ],
            [
                'choice' => 'I’m at a crossroads',
                'image' => 'tema-promene.svg',
                'steps' => [
                    'Imagine two paths in front of you. Don’t choose, just look at them.',
                    'Which path does your body move towards more easily? Where does something tighten?',
                    'What would you lose, and what would you gain, if you changed nothing?',
                ],
                'reflection' => 'Change rarely asks for a quick decision. More often it asks us to hear every part of ourselves, the parts that want to move on and the parts that are afraid.',
                'linkText' => 'Life changes and crises',
                'page' => 'what-i-work-with/loss-and-change',
            ],
            [
                'choice' => 'As a parent, I’m struggling',
                'image' => 'tema-roditeljstvo.svg',
                'steps' => [
                    'Think of a moment with your child, yesterday or today, that hit you.',
                    'What did you feel? Not your child, you.',
                    'Do you know this feeling from before, from the time when you were a child yourself?',
                ],
                'reflection' => 'Parenthood often takes us back to our own childhood. When we understand what wakes up in us and why, it is easier to stay present with our child.',
                'linkText' => 'Support for parents',
                'page' => 'what-i-work-with/parenting',
            ],
        ],
    ];

    private const TOPIC_CARD = [
        'sr' => [
            'ctaQuote' => 'Ne vidite svoju temu ovde?',
            'ctaText' => 'Ovo su oblasti s kojima se najčešće srećem, ali nisu jedine. Možemo zajedno proceniti da li sam prava osoba za vas. Ako nisam, rado ću vam preporučiti nekoga ko jeste.',
            'ctaLinkText' => 'Pišite mi →',
        ],
        'en' => [
            'ctaQuote' => 'Don\'t see your topic here?',
            'ctaText' => 'These are the areas I meet most often, but they are not the only ones. Together we can see whether I\'m the right person for you. If I\'m not, I\'ll gladly recommend someone who is.',
            'ctaLinkText' => 'Write to me →',
        ],
    ];

    public function __construct(private readonly Connection $connection)
    {
    }

    public function shouldRun(): bool
    {
        $schema = $this->connection->createSchemaManager();

        if (!$schema->tablesExist(['tl_content'])) {
            return false;
        }

        $columns = array_change_key_case($schema->listTableColumns('tl_content'));

        // The new fields must exist first (contao:migrate adds them, then runs this again)
        foreach (['exercisefolder', 'exerciseendtitle', 'ctaquote', 'listitems'] as $column) {
            if (!isset($columns[$column])) {
                return false;
            }
        }

        return (bool) $this->connection->fetchOne("SELECT COUNT(*) FROM tl_content WHERE type = 'exercise' AND exerciseFolder IS NOT NULL");
    }

    public function run(): MigrationResult
    {
        $exercises = $this->connection->fetchAllAssociative("SELECT id, pid, ptable, exerciseVariant, exerciseFolder FROM tl_content WHERE type = 'exercise' AND exerciseFolder IS NOT NULL");

        foreach ($exercises as $exercise) {
            $language = $this->getLanguage((int) $exercise['id']);
            $folder = (string) $this->connection->fetchOne('SELECT path FROM tl_files WHERE uuid = ?', [$exercise['exerciseFolder']]);
            $this->migrateExercise((int) $exercise['id'], 'b' === $exercise['exerciseVariant'] ? 'b' : 'a', $language, $folder);
        }

        $grids = $this->connection->fetchAllAssociative("SELECT id FROM tl_content WHERE type = 'topic_grid' AND topicsVariant IN ('home', 'overview') AND ctaQuote = ''");

        foreach ($grids as $grid) {
            $this->connection->update('tl_content', self::TOPIC_CARD[$this->getLanguage((int) $grid['id'])], ['id' => $grid['id']]);
        }

        return $this->createResult(true, \sprintf('Moved the texts of %d exercises and %d topic grids into the back end.', \count($exercises), \count($grids)));
    }

    private function migrateExercise(int $id, string $variant, string $language, string $folder): void
    {
        $content = self::CONTENT[$language];

        $this->connection->update('tl_content', [
            ...$content[$variant],
            'exerciseEndEyebrow' => $content['exerciseEndEyebrow'],
            'exerciseEndTitle' => $content['exerciseEndTitle'],
            'introPrimaryText' => $content['introPrimaryText'],
            'exerciseFolder' => null,
        ], ['id' => $id]);

        foreach (self::TOPICS[$language] as $i => $topic) {
            $pageId = $this->connection->fetchOne('SELECT id FROM tl_page WHERE alias = ?', [$topic['page']]);
            $image = $folder ? $this->connection->fetchOne('SELECT uuid FROM tl_files WHERE path = ?', [$folder.'/'.$topic['image']]) : false;

            $this->connection->insert('tl_content', [
                'pid' => $id,
                'ptable' => 'tl_content',
                'sorting' => ($i + 1) * 128,
                'tstamp' => time(),
                'type' => 'exercise_topic',
                'introTitle' => $topic['choice'],
                'introImage' => $image ?: null,
                'listitems' => serialize($topic['steps']),
                'introText' => $topic['reflection'],
                'introPrimaryUrl' => $pageId ? '{{link_url::'.$pageId.'}}' : '/'.$topic['page'],
                'introPrimaryText' => $topic['linkText'],
            ]);
        }
    }

    // Language of the root page above the element ("sr" unless the root says "en")
    private function getLanguage(int $contentId): string
    {
        $row = $this->connection->fetchAssociative('SELECT pid, ptable FROM tl_content WHERE id = ?', [$contentId]);

        while ($row && 'tl_content' === $row['ptable']) {
            $row = $this->connection->fetchAssociative('SELECT pid, ptable FROM tl_content WHERE id = ?', [$row['pid']]);
        }

        $pageId = $row ? $this->connection->fetchOne('SELECT pid FROM tl_article WHERE id = ?', [$row['pid']]) : false;

        while ($pageId) {
            $page = $this->connection->fetchAssociative('SELECT pid, type, language FROM tl_page WHERE id = ?', [$pageId]);

            if (!$page || 'root' === $page['type']) {
                return str_starts_with((string) ($page['language'] ?? ''), 'en') ? 'en' : 'sr';
            }

            $pageId = $page['pid'];
        }

        return 'sr';
    }
}
