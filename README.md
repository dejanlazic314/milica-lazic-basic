# milica-lazic-basic

Website of Milica Lazić, gestalt psychotherapist (Belgrade and online), built on **Contao 5.7 LTS**.

> Work in progress.

## Stack

- Contao 5.7 LTS, PHP 8.3+
- Content elements as Contao 5 fragment controllers (`#[AsContentElement]`) with Twig templates
- Plain CSS (custom properties) in separate files, versioned with `asset()` – no Sass, no build step
- Small vanilla JS as ES modules, no frontend libraries
- Prettier and `.editorconfig` for one code style (`npm run format`)
- Local development with [DDEV](https://ddev.com)

## Local development

Requires Docker and [DDEV](https://ddev.com).

```sh
ddev start
ddev composer install
ddev exec bin/console contao:migrate
ddev exec bin/console contao:user:create   # first backend user
```

The site runs at https://milica-lazic.ddev.site (run `mkcert -install` once for a trusted certificate), the backend at `/contao`, outgoing mail goes to Mailpit (`ddev mailpit`).

### Checks

```sh
npm install && npx playwright install chromium

node tests/visual/snapshot.mjs baseline   # before a change: shots of all pages and states
node tests/visual/snapshot.mjs check      # after it: pixel difference per shot
node tests/visual/behaviour.mjs           # scripts with animations on, JS errors
node tests/visual/compare.mjs <selector> <site-url> <design-url> [name]   # one section vs. the design
```

`snapshot.mjs` shoots every page in `tests/visual/pages.json` at 375, 768 and 1280 px, plus the
interactive states in `tests/visual/states.mjs` (menus, exercise, form errors, cookie settings).
A change that should not change the look must end with `0 different`. Images go to `test-results/visual/`.

## Deployment

[Deployer 7](https://deployer.org) with its Contao recipe (`deploy.php`), run from the DDEV container:

```sh
ddev auth ssh
ddev exec vendor/bin/dep deploy production
```

The host configuration is not part of this repository.

## Structure

| Path | Content |
|---|---|
| `src/` | PHP code, namespace `MilicaLazic\` (content element controllers, listeners) |
| `contao/dca/` | DCA; `tl_content.php` has the fields several elements share |
| `contao/elements/` | Palette and own fields of each content element, one file per element |
| `contao/languages/` | Backend labels |
| `templates/` | Twig templates; `page/layout/milica.html.twig` is the page layout (Contao "modern" layout type) |
| `public/layout/css/` | `base/` tokens and plain HTML · `layout/` page, header, footer · `components/` pieces used in many places · `sections/` one file per element or page · `states.css` hover and focus · `backend.css` element previews in the backend. The load order is the list in the page layout. |
| `public/layout/js/` | One ES module per feature (`header.js`, `reveal.js`, `circles.js`, `cookies.js`, `form.js`, `exercise.js`, `accordion.js`) |
| `src/Migration/` | One-off database migrations, run by `contao:migrate` |
| `config/` | Symfony / Contao configuration |
| `translations/` | Text used in templates (`site.sr.yaml`, `site.en.yaml`) |
| `tests/visual/` | Snapshots of the site, behaviour checks, comparison with the static design |

## License

Proprietary. The source is public for reference only; no permission is granted to use, copy or redistribute it.
