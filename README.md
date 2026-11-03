# Milli — Culture Magazine Starter Kit

A modern culture and lifestyle magazine theme for Statamic 5 and 6, featuring news articles, magazine editions, culture blogs, workshops, career listings, pricing plans, and FAQ — all managed from the Control Panel.

Milli ships **nine catalogue packs** on one visual design: fashion, menswear, womenswear, kids, streetwear, jewellery, beauty, accessories, and homeware. Each pack sets the site name, brand colour, logo wordmark, and home hero copy.

## After install — choose a catalogue

Starter-kit install is non-interactive. Run one command to apply a niche demo pack:

```bash
php please milli:setup fashion --force
# or: menswear | womenswear | kids | streetwear | jewellery | beauty | accessories | homeware
```

Changing the Setting → Catalogue select alone does not swap colours — run `php please milli:setup {slug}` for that.

## Theme Sections (page_builder types)

| Section | Handle | Used on |
|---|---|---|
| Home Hero | `home_hero` | Home |
| Home Hero (variant two) | `home_hero_two` | Home Two |
| Home Hero (variant three) | `home_hero_three` | Home Three |
| Cultural Stories | `stories` | Home, About |
| About Intro | `about_intro` | About |
| About Content Box | `about_content` | About |
| About Stories | `about_stories` | About |
| About Description | `about_description` | About |
| Artist / Magazine Feature | `artist` | Home Two |
| Magazine Feature | `magazine_feature` | Home Three |
| Marquee Text | `marquee` | Home Three |
| Career Intro | `career_intro` | Career |
| News Slider | `news_slider` | Home |
| News Listing | `news_listing` | News |
| Editions Slider | `editions_slider` | Home, Home Three |
| Editions Gallery | `editions_gallery` | Editions |
| Editions Grid | `editions_grid` | Home Two |
| Culture Slider | `culture_slider` | Home, Home Two |
| Culture Listing | `culture_listing` | Culture |
| Culture Blog Grid | `culture_grid` | Culture Grid |
| Latest Blog Grid | `latest_blog` | Latest Blog |
| Blog Slider (large) | `blog_slider` | Home Three |
| Workshops Slider | `workshops_slider` | Home Two |
| Workshops Listing | `workshops_listing` | Workshops |
| Open Positions | `careers` | Career |
| Hiring Process | `hiring_process` | Career |
| Career Contact Section | `career_contact` | Career |
| Career Apply Popup | `career_form` | Career |
| Pricing Plans | `pricing` | Pricing, Home Three |
| FAQ | `faqs` | FAQ |
| Newsletter | `newsletter` | All home pages, About |
| Contact Form | `contact_form` | Contact |
| Map | `map` | Contact |
| All Pages Hero | `all_pages` | All pages |
| Unique Homes | `unique_homes` | All pages |
| Inner Pages Showcase | `pages_showcase` | All pages |
| Feature Banner | `feature_banner` | All pages |
| Site Features | `site_features` | All pages |
| Custom Template | `custom_template` | All pages |
| Pages Link Index | `pages_index` | All pages |

## Collections

- **Pages** — Marketing pages built with page_builder sections
- **News** — News articles with star-shaped feature images
- **Culture** — Culture blog posts with categories and authors
- **Editions** — Magazine edition covers (paired images)
- **Workshops** — Workshop listings with star images, detail pages, and galleries
- **Careers** — Job listings (title, type, short description)
- **Plans** — Subscription pricing plans with features
- **FAQs** — Questions and answers for the FAQ accordion

## Forms

This kit ships three forms. **Statamic Pro is required** when using more than one form.

| Form | Handle | Fields |
|---|---|---|
| Contact | `contact` | name, email, phone, body |
| Newsletter | `newsletter` | email |
| Career Application | `career` | name, email, phone, cover_letter, resume |

All forms submit via AJAX with success and error feedback. No `js="true"` is used on form tags.

## Globals

- **Setting** — Site name, logo, phone, email, address, social links, map embed, form button/success text, SEO defaults
- **Footer** — Footer text and copyright

Setting globals ship in both layouts: `data:` in `content/globals/setting.yaml` (Statamic 5) and `content/globals/default/setting.yaml` (Statamic 6). Footer follows the same pattern.

## Resume Uploads

Career application resumes are stored on a **private disk** (`storage/app/resumes/`), outside the public directory. Server-side validation enforces PDF/DOC/DOCX mime types and a 5 MB size limit. The `MilliServiceProvider` configures this disk at boot. New applications stay unpublished until an editor reviews them.

## Navigation

- `header_menu` — Main header navigation
- `footer_menu` — Footer link column
- `footer_support` — Footer support links column

## Installation

Follow the [Starter Kit installation instructions](https://statamic.dev/starter-kits/installing-a-starter-kit).  
Requires **Statamic 5.x or 6.x** (PHP 8.2+ for Statamic 5; PHP 8.4+ for Statamic 6).

```bash
statamic new my-site webbycrown/milli-statamic-theme

# or into an existing site
php please starter-kit:install webbycrown/milli-statamic-theme
```

The install post-hook adds `MilliServiceProvider` to `bootstrap/providers.php` when possible (it does not replace `AppServiceProvider`). If it is missing, register it yourself:

```php
// bootstrap/providers.php
App\Providers\MilliServiceProvider::class,
```

Then refresh the content cache:

```bash
php please stache:refresh
```

## Third-party Assets

See [THIRD_PARTY.md](THIRD_PARTY.md) for all bundled scripts, fonts, and their licences.

## Support

Questions and issues: [github.com/webbycrown/milli-statamic-theme/issues](https://github.com/webbycrown/milli-statamic-theme/issues) or [WebbyCrown](https://www.webbycrown.com/custom-statamic-development-services-company/).

## Changelog

### v1.0.0

- Compatible with Statamic 5 and 6 (`statamic/cms: ^5.0 || ^6.0`)
- Setting and Footer globals ship in both the Statamic 5 `data:` layout and the Statamic 6 `content/globals/default/` layout
- Install post-hook registers `MilliServiceProvider` in `bootstrap/providers.php`
- Page template and Theme sections for marketing pages; news, editions, culture, workshops, careers, plans, and FAQs as collections
