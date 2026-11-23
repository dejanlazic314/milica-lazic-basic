# milica-lazic-basic

Website of Milica Lazić, gestalt psychotherapist (Belgrade and online), built on **Contao 5.7 LTS**.

> Work in progress.

## Stack

- Contao 5.7 LTS, PHP 8.3+
- Content elements as Contao 5 fragment controllers (`#[AsContentElement]`) with Twig templates
- Plain CSS (custom properties) in cascade layers, one file per element, versioned with `asset()` – no Sass, no build step
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
node tests/visual/computed.mjs baseline   # computed styles of every element, also hovered and focused
node tests/visual/computed.mjs check
node tests/visual/assets.mjs              # every page loads the CSS its markup needs
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
| `public/layout/css/` | `base/` layers, tokens, fonts, plain HTML · `components/` pieces used in many places · `layout/` page, header, footer · `elements/` one file per content element (`<type>.css`) and per section layout or class (`section-<name>.css`) · `modules/` one file per front-end module · `backend.css` element previews in the backend. Every file puts its rules in a cascade layer (`base/layers.css`: base, page, components, layout, elements, states, utilities), so the order of the files does not matter. No `!important`. Font sizes in `rem`; headings and section spacing are fluid tokens (`clamp()` between 375 and 1024 px, `base/tokens.css`); breakpoints in `em`. |
| `public/layout/js/` | Same folders as the CSS, one ES module per feature: `layout/header.js`, `components/{reveal,cookies,circles}.js`, `elements/{exercise,faq_list,form}.js` |
| Loading CSS and JS | On every page: the list in `templates/page/layout/milica.html.twig`. Everything else comes with what uses it: `templates/content_element/_base.html.twig` calls `add_assets('elements/<type>')` (`src/Twig/AssetsExtension.php`), which adds `css/elements/<type>.css` and `js/elements/<type>.js` if they exist; included parts and modules call `add_assets()` themselves. A new element gets its CSS and JS by having files with its type name. |
| `src/Migration/` | One-off database migrations, run by `contao:migrate` |
| `config/` | Symfony / Contao configuration |
| `translations/` | Text used in templates (`site.sr.yaml`, `site.en.yaml`) |
| `tests/visual/` | Snapshots of the site, behaviour checks, comparison with the static design |

## License

Proprietary. The source is public for reference only; no permission is granted to use, copy or redistribute it.
