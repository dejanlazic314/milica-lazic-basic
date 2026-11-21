# milica-lazic-basic

Website of Milica Lazić, gestalt psychotherapist (Belgrade and online), built on **Contao 5.7 LTS**.

> Work in progress.

## Stack

- Contao 5.7 LTS, PHP 8.3+
- Content elements as Contao 5 fragment controllers (`#[AsContentElement]`) with Twig templates
- Plain CSS (custom properties) in separate files, versioned with `asset()` – no Sass, no build step
- Small vanilla JS, no frontend libraries
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

### Visual comparison

```sh
npm install && npx playwright install chromium
node tests/visual/compare.mjs <selector> <site-url> <design-url> [name]
```

Screenshots one section on the site and on a static design page at 375, 768 and 1280 px and reports the pixel difference. Images go to `test-results/visual/`.

## Structure

| Path | Content |
|---|---|
| `src/` | PHP code, namespace `MilicaLazic\` (content element controllers, listeners) |
| `contao/` | DCA and backend labels |
| `templates/` | Twig templates; `page/layout/milica.html.twig` is the page layout (Contao "modern" layout type) |
| `public/layout/` | CSS and JS, loaded by the page layout |
| `config/` | Symfony / Contao configuration |
| `translations/` | Text used in templates (`site.sr.yaml`, `site.en.yaml`) |
| `tests/visual/` | Screenshot comparison with the static design |

## License

Proprietary. The source is public for reference only; no permission is granted to use, copy or redistribute it.
