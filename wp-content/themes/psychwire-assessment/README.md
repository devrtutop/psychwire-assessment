# Psychwire Assessment

Block theme for the one-page site about Dr Alex Rivera. WordPress renders the page on the server. There is no build step and no plugin.

## Run it

The site is the Docker project `psychwire-assessment`.

- Site: http://localhost:8098
- Admin: http://localhost:8098/wp-admin
- Local admin user: `rivera`
- Local admin password: `Rivera-local-2026`

Activate **Psychwire Assessment** under Appearance if another theme is current.

## Where things live

- `inc/content.php` is the only copy of the biography, the three courses, and the labels used on the page. The visible HTML and the JSON-LD both read it.
- `inc/blocks.php` is the block registry. Add a folder under `blocks/` and its name to that list.
- `inc/seo.php` sets the document title, meta description, and JSON-LD (`Person`, `Course`, `WebPage`).
- `blocks/*/render.php` prints each region. WordPress captures that output, so these files print HTML directly.
- `templates/front-page.html` places header, main, and footer.
- `theme.json` holds the colour palette and Inter.
- `style.css` is the layout.

## Checked before handoff

- `php -l` on every theme PHP file: no syntax errors.
- View source of http://localhost:8098 includes, without running JavaScript: `header`, `nav`, `main`, `section`, `footer`, one `h1`, `h2` for Courses and Contact, `h3` for each course, the biography, three courses, `hello@example.com`, a skip link, the portrait alt text, the title `Dr Alex Rivera | Clinical Psychologist and Trainer`, the meta description, and JSON-LD for a Person and three Courses.
- Course links use the assessment placeholder `#`. “Load more courses” does not hide those three courses.
- Contrast of the Figma colours used for text is at least 5.9:1.
- The first render returned empty regions because `render.php` returned a string. WordPress only keeps output from those files. The templates now print HTML, and the page source contains the content.
