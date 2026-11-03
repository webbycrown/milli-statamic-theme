<?php

namespace App\Support;

use Illuminate\Support\Facades\File;
use Statamic\Facades\Stache;
use Statamic\Facades\YAML;

class MilliIndustry
{
    public const SLUGS = [
        'original',
        'fashion',
        'menswear',
        'womenswear',
        'kids',
        'streetwear',
        'jewellery',
        'beauty',
        'accessories',
        'homeware',
    ];

    /** Primary / soft / dark hex per catalogue so each demo reads a different brand colour. */
    public const THEME_COLORS = [
        'original' => ['#576F72', '#E8E4D9', '#1F1F1F'],
        'fashion' => ['#247A4D', '#EBF4F1', '#1B5E3B'],
        'menswear' => ['#1B4F72', '#E8F1F8', '#143B56'],
        'womenswear' => ['#9D174D', '#FCE8F0', '#7A123C'],
        'kids' => ['#EA580C', '#FFF1E6', '#C2410C'],
        'streetwear' => ['#DC2626', '#FEE2E2', '#B91C1C'],
        'jewellery' => ['#B45309', '#FEF3C7', '#92400E'],
        'beauty' => ['#BE185D', '#FCE7F3', '#9D174D'],
        'accessories' => ['#0369A1', '#E0F2FE', '#075985'],
        'homeware' => ['#0F766E', '#CCFBF1', '#115E59'],
    ];

    private const LABELS = [
        'original' => 'Original',
        'fashion' => 'Fashion',
        'menswear' => 'Menswear',
        'womenswear' => 'Womenswear',
        'kids' => 'Kids',
        'streetwear' => 'Streetwear',
        'jewellery' => 'Jewellery',
        'beauty' => 'Beauty',
        'accessories' => 'Accessories',
        'homeware' => 'Homeware',
    ];

    private const HERO = [
        'original' => [
            'heading' => 'Explore the world of culture and art',
            'text' => 'Milli is a culture magazine dedicated to exploring art, design, music, and the stories that shape contemporary living. Discover fresh perspectives on creativity and cultural expression.',
        ],
        'fashion' => [
            'heading' => 'Fashion culture, told in print',
            'text' => 'Milli Fashion is a culture magazine for clothes, runway notes, and the people who wear them. Same Milli layout — this catalogue leans green.',
        ],
        'menswear' => [
            'heading' => 'Menswear stories, cut clean',
            'text' => 'Milli Menswear covers shirts, coats, and the desks they live on. One Milli design; this catalogue reads in deep blue.',
        ],
        'womenswear' => [
            'heading' => 'Womenswear in soft focus',
            'text' => 'Milli Womenswear follows dresses, knits, and evening pieces. Same sections as every Milli catalogue — rose accents mark this one.',
        ],
        'kids' => [
            'heading' => 'Kids culture, bright and light',
            'text' => 'Milli Kids looks at play clothes, small studios, and weekend colour. The Milli theme stays; orange marks this catalogue.',
        ],
        'streetwear' => [
            'heading' => 'Streetwear drops and desk notes',
            'text' => 'Milli Streetwear covers graphics, cargos, and late-night fittings. Same Milli chrome — red is the brand colour here.',
        ],
        'jewellery' => [
            'heading' => 'Jewellery close to the skin',
            'text' => 'Milli Jewellery writes about chains, rings, and workshop light. One Milli magazine frame; amber is this catalogue’s colour.',
        ],
        'beauty' => [
            'heading' => 'Beauty colour, close up',
            'text' => 'Milli Beauty tracks cream, colour, and dressing-table notes. The layout matches every Milli demo — pink marks this niche.',
        ],
        'accessories' => [
            'heading' => 'Accessories that finish the page',
            'text' => 'Milli Accessories covers bags, belts, and small carry pieces. Same Milli structure; sky blue is the catalogue accent.',
        ],
        'homeware' => [
            'heading' => 'Homeware for the table and room',
            'text' => 'Milli Homeware looks at bowls, linen, and quiet interiors. One Milli theme — teal sets this catalogue apart.',
        ],
    ];

    public static function apply(string $industry): bool
    {
        $industry = strtolower($industry);
        if (! in_array($industry, self::SLUGS, true)) {
            return false;
        }

        $pack = base_path('content/industries/'.$industry);
        if (! is_dir($pack)) {
            return false;
        }

        $meta = YAML::file($pack.'/setting.yaml')->parse() ?? [];
        $label = $meta['industry_label'] ?? ((self::LABELS[$industry] ?? ucfirst($industry)).' magazine');
        $word = self::LABELS[$industry] ?? ucfirst($industry);
        $colors = self::THEME_COLORS[$industry] ?? self::THEME_COLORS['fashion'];

        $configPath = base_path('content/globals/setting.yaml');
        $variablesPath = base_path('content/globals/default/setting.yaml');
        File::ensureDirectoryExists(dirname($variablesPath));

        $config = YAML::file($configPath)->parse() ?? [];
        if (! empty($config['data']) && is_array($config['data'])) {
            $data = $config['data'];
        } else {
            $data = YAML::file($variablesPath)->parse() ?? [];
        }

        $data['industry'] = $industry;
        $data['industry_label'] = $label;
        $data['site_name'] = $meta['site_name'] ?? ($industry === 'original' ? 'Milli' : ('Milli '.$word));
        if (! empty($meta['seo_description'])) {
            $data['seo_description'] = $meta['seo_description'];
        } elseif ($industry === 'original') {
            $data['seo_description'] = 'The original Milli culture-magazine demo — classic sage accent, original photos, and default home copy.';
        } else {
            $data['seo_description'] = 'A '.$word.' culture-magazine demo on the Milli theme. Same layout; this catalogue uses its own brand colour.';
        }

        $data['theme_color'] = $meta['theme_color'] ?? $colors[0];
        $data['theme_color_soft'] = $meta['theme_color_soft'] ?? $colors[1];
        $data['theme_color_dark'] = $meta['theme_color_dark'] ?? $colors[2];

        self::applyLogos($industry, $word, $data);
        self::applyThemeAssets($industry);

        $config['title'] = $config['title'] ?? 'Setting';
        $config['data'] = $data;
        File::put($configPath, YAML::dump($config));
        File::put($variablesPath, YAML::dump($data));

        $hero = self::HERO[$industry] ?? self::HERO['fashion'];
        self::applyHomeHero(
            (string) ($meta['hero_heading'] ?? $hero['heading']),
            (string) ($meta['hero_text'] ?? $hero['text'])
        );

        if (class_exists(Stache::class)) {
            try {
                Stache::refresh();
            } catch (\Throwable $e) {
                // Catalogue files are already written.
            }
        }

        return true;
    }

    private static function applyLogos(string $industry, string $word, array &$data): void
    {
        $imageDir = public_path('assets/image');
        File::ensureDirectoryExists($imageDir);

        if ($industry === 'original') {
            self::restoreOriginalLogos($imageDir);
            $data['logo'] = 'image/logo.svg';
            $data['logo_white'] = 'image/white-logo.svg';
            $data['site_name'] = 'Milli';

            return;
        }

        $fill = $data['theme_color'] ?? '#247A4D';
        $dark = self::logoSvg($word, $fill);
        $white = self::logoSvg($word, '#FFFFFF');

        $darkName = $industry.'-logo.svg';
        $whiteName = $industry.'-logo-white.svg';

        File::put($imageDir.DIRECTORY_SEPARATOR.$darkName, $dark);
        File::put($imageDir.DIRECTORY_SEPARATOR.$whiteName, $white);
        @chmod($imageDir.DIRECTORY_SEPARATOR.$darkName, 0644);
        @chmod($imageDir.DIRECTORY_SEPARATOR.$whiteName, 0644);

        // Classic filenames + PNG loader fallbacks stay on-niche.
        File::put($imageDir.DIRECTORY_SEPARATOR.'logo.svg', $dark);
        File::put($imageDir.DIRECTORY_SEPARATOR.'white-logo.svg', $white);
        File::put($imageDir.DIRECTORY_SEPARATOR.'logo-'.$industry.'.svg', $dark);
        File::put($imageDir.DIRECTORY_SEPARATOR.'logo-'.$industry.'-white.svg', $white);
        @chmod($imageDir.DIRECTORY_SEPARATOR.'logo.svg', 0644);
        @chmod($imageDir.DIRECTORY_SEPARATOR.'white-logo.svg', 0644);

        $data['logo'] = 'image/'.$darkName;
        $data['logo_white'] = 'image/'.$whiteName;
        $data['site_name'] = $data['site_name'] ?? ('Milli '.$word);
    }

    private static function restoreOriginalLogos(string $imageDir): void
    {
        $pack = public_path('assets/milli-original');
        foreach (['logo.svg', 'white-logo.svg', 'logo.png'] as $name) {
            $src = $pack.DIRECTORY_SEPARATOR.$name;
            if (is_file($src)) {
                File::copy($src, $imageDir.DIRECTORY_SEPARATOR.$name);
                @chmod($imageDir.DIRECTORY_SEPARATOR.$name, 0644);
            }
        }
    }

    private static function logoSvg(string $niche, string $fill): string
    {
        $nicheUpper = htmlspecialchars(strtoupper($niche), ENT_QUOTES | ENT_XML1);
        $fill = htmlspecialchars($fill, ENT_QUOTES | ENT_XML1);
        // Larger than original 165×42 slot so niche wordmarks read as big as the classic Milli mark.
        $len = strlen($niche);
        $nicheSize = $len > 11 ? '12' : ($len > 9 ? '13' : ($len > 7 ? '15' : '16'));
        $nicheTracking = $len > 11 ? '0.1em' : ($len > 9 ? '0.14em' : '0.2em');

        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="220" height="64" viewBox="0 0 220 64" role="img" aria-label="Milli {$nicheUpper}">
  <text x="110" y="34" text-anchor="middle" font-family="Georgia, 'Times New Roman', Times, serif" font-size="36" font-weight="700" letter-spacing="0.08em" fill="{$fill}">MILLI</text>
  <text x="110" y="56" text-anchor="middle" font-family="system-ui, -apple-system, 'Segoe UI', Arial, sans-serif" font-size="{$nicheSize}" font-weight="700" letter-spacing="{$nicheTracking}" fill="{$fill}">{$nicheUpper}</text>
</svg>
SVG;
    }

    /**
     * Build the photo pool from on-disk `{industry}-*` niche pack files
     * (hero first, then numbered / about / pool shots).
     *
     * @return list<string> Absolute paths
     */
    private static function resolvePool(string $industry, string $imageDir): array
    {
        $names = [];
        foreach (File::files($imageDir) as $file) {
            $name = $file->getFilename();
            if (! preg_match('/\.(webp|jpg|jpeg|png)$/i', $name)) {
                continue;
            }
            if (preg_match('/logo/i', $name)) {
                continue;
            }
            if (! preg_match('/^'.preg_quote($industry, '/').'-/i', $name)) {
                continue;
            }
            $names[] = $name;
        }

        usort($names, function (string $a, string $b) use ($imageDir) {
            $rank = static function (string $n) use ($imageDir): int {
                $size = @filesize($imageDir.DIRECTORY_SEPARATOR.$n) ?: 0;
                // Soft/tiny heroes lose to clear pool JPGs.
                if (str_contains($n, '-hero') && $size >= 120000) {
                    return 0;
                }
                // Prefer clear JPG pool/about shots over tiny numbered WebPs.
                if (str_contains($n, '-pool') && preg_match('/\.(jpe?g|png)$/i', $n)) {
                    return 1;
                }
                if (str_contains($n, '-about') && preg_match('/\.(jpe?g|png)$/i', $n)) {
                    return 2;
                }
                if (str_contains($n, '-hero')) {
                    return 3;
                }
                if (preg_match('/-\d{2}\.(jpe?g|png)$/i', $n)) {
                    return 4;
                }
                if (preg_match('/-\d{2}\.webp$/i', $n)) {
                    return 7;
                }
                if (str_contains($n, '-pool') || str_contains($n, '-about')) {
                    return 5;
                }

                return 6;
            };
            $ra = $rank($a);
            $rb = $rank($b);
            if ($ra !== $rb) {
                return $ra <=> $rb;
            }
            $sa = @filesize($imageDir.DIRECTORY_SEPARATOR.$a) ?: 0;
            $sb = @filesize($imageDir.DIRECTORY_SEPARATOR.$b) ?: 0;
            if ($sa !== $sb) {
                return $sb <=> $sa; // clearer / larger first
            }

            return strnatcasecmp($a, $b);
        });

        $pool = [];
        foreach ($names as $name) {
            $pool[] = $imageDir.DIRECTORY_SEPARATOR.$name;
        }

        return $pool;
    }

    /**
     * Overwrite every content raster under public/assets/image with this
     * catalogue's photo pack (Quikbasket-style). Logos, favicon, and UI chrome
     * stay; niche pack source files are not destroyed so setup can re-run.
     */
    private static function applyThemeAssets(string $industry): void
    {
        $imageDir = public_path('assets/image');
        if (! is_dir($imageDir)) {
            return;
        }

        if ($industry === 'original') {
            self::restoreOriginalTheme($imageDir);

            return;
        }

        $pool = self::resolvePool($industry, $imageDir);
        if ($pool === []) {
            return;
        }

        // Dedicated chooser / pack hero.
        self::copyAsThemeAsset($pool[0], $imageDir.DIRECTORY_SEPARATOR.$industry.'-hero.jpg');

        // Edition covers are rebuilt from the niche pool at original comic sizes (see applyCatalogueEditions).
        $protect = '/^(favicon|logo|loader-logo|footer_logo|haven-logo|haven-footer-logo|white-logo|arrow|btn-|menu|search|serch-|facebook|instagram|twitter|mail\.|phone|location|quote|shap|big-shape|mask|star-shap|star-imges|drag|Drag|ui-icons|group-icon|five-icon|edition-swiper-arrow|news-swiper|call-|Meassage|basket\.svg)/i';
        // All niche packs + Haven leftovers — never destroy while remapping theme slots.
        $poolSources = '/^(fashion-|menswear-|womenswear-|kids-|streetwear-|jewellery-|beauty-|accessories-|homeware-|original-|haven-|kiln-|lumen-|wick-|grove-|alder-|quill-|furn-|Pearl\.png)/i';
        $catalogueOwned = '/^(original|fashion|menswear|womenswear|kids|streetwear|jewellery|beauty|accessories|homeware)-(hero|logo)/i';

        $poolReals = [];
        foreach ($pool as $path) {
            $real = realpath($path);
            if ($real) {
                $poolReals[$real] = true;
            }
        }

        $i = 0;
        foreach (File::files($imageDir) as $file) {
            $name = $file->getFilename();
            if (! preg_match('/\.(webp|jpg|jpeg|png)$/i', $name)) {
                continue;
            }
            if (preg_match($protect, $name)
                || preg_match($poolSources, $name)
                || preg_match($catalogueOwned, $name)
                || preg_match('/-logo(\.svg|-white)/i', $name)) {
                continue;
            }
            $real = realpath($file->getPathname());
            if ($real && isset($poolReals[$real])) {
                continue;
            }

            self::copyAsThemeAsset($pool[$i % count($pool)], $file->getPathname());
            $i++;
        }

        // Pin primary surfaces to the start of the pool so hero/about/stories
        // always show the clearest on-niche shots (not a mid-loop leftover).
        $priority = [
            'hero-img.jpg',
            'mobile-hero.jpg',
            'about-intro.jpg',
            'about-content-img.jpg',
            'About.jpg',
            'stories-img-1.jpg',
            'stories-img-2.jpg',
            'stories-img-3.jpg',
            'stories-img-4.jpg',
            'stories-img-5.jpg',
            'culture-desc-img.jpg',
            'culture-img-1.jpg',
            'culture-img-2.jpg',
            'culture-img-3.jpg',
            'culture-post.jpg',
            'star-img-1.jpg',
            'star-img-2.jpg',
            'star-img-3.jpg',
            'News.jpg',
            'Latest Blog.jpg',
            'blog-img-1.png',
            'blog-img-2.png',
            'blog-img-3.png',
            'home-slider-1.png',
            'home-slider-2.png',
        ];
        foreach ($priority as $index => $target) {
            self::copyAsThemeAsset(
                $pool[$index % count($pool)],
                $imageDir.DIRECTORY_SEPARATOR.$target
            );
        }
        // Chooser card + pack hero stay on the first pool shot.
        self::copyAsThemeAsset($pool[0], $imageDir.DIRECTORY_SEPARATOR.$industry.'-hero.jpg');

        // Editions: magazine book covers at Original canvas sizes, clear niche photos.
        self::applyCatalogueEditions($imageDir, $pool, $industry);

        @chmod($imageDir, 0755);
        foreach (File::files($imageDir) as $file) {
            @chmod($file->getPathname(), 0644);
        }
    }

    /**
     * Build edition magazine-book covers at Original canvas sizes using clear niche photos.
     *
     * @param  list<string>  $pool
     */
    private static function applyCatalogueEditions(string $imageDir, array $pool, string $industry): void
    {
        if ($pool === []) {
            return;
        }

        $word = self::LABELS[$industry] ?? ucfirst($industry);
        $accent = self::THEME_COLORS[$industry][0] ?? '#576F72';

        // Render 2× for sharpness on retina; browser CSS scales down.
        $canvases = [
            'edition-img-1.jpg' => [982, 1226],
            'edition-img-2.jpg' => [656, 864],
            'edition-img-3.jpg' => [806, 1042],
            'edition-img-4.jpg' => [1200, 1500],
            'edition-img-5.jpg' => [956, 1330],
            'edition-img-6.jpg' => [1004, 1312],
            'edition-pase-1.png' => [960, 1212],
            'edition-pase-2.png' => [680, 600],
            'edition-pase-3.png' => [680, 760],
            'edition-pase-4.png' => [880, 1020],
            'Editions-swiper-img-1.png' => [680, 880],
            'Editions-swiper-img-2.png' => [680, 740],
            'Editions-swiper-img-3.png' => [680, 600],
            'Editions-swiper-img-4.png' => [680, 1020],
            'Editions-swiper-img-5.png' => [680, 880],
            'Editions-swiper-img-6.png' => [680, 740],
            'Editions-swiper-img-7.png' => [680, 600],
            'Editions-swiper-img-8.png' => [680, 1020],
            'Editions.jpg' => [960, 1440],
        ];

        $i = 0;
        foreach ($canvases as $name => [$w, $h]) {
            self::renderEditionBookCover(
                $pool[$i % count($pool)],
                $imageDir.DIRECTORY_SEPARATOR.$name,
                $w,
                $h,
                $word,
                $accent,
                $i
            );
            $i++;
        }
    }

    /**
     * Magazine book cover: clear photo, masthead, spine edge — catalogue-typed, not Original clone.
     */
    private static function renderEditionBookCover(
        string $source,
        string $dest,
        int $width,
        int $height,
        string $word,
        string $accentHex,
        int $variant
    ): void {
        if (! is_file($source) || $width < 1 || $height < 1) {
            return;
        }

        if (! function_exists('imagecreatefromstring')) {
            self::copyAsThemeAsset($source, $dest);

            return;
        }

        $blob = @file_get_contents($source);
        $photo = $blob !== false ? @imagecreatefromstring($blob) : false;
        if ($photo === false) {
            self::copyAsThemeAsset($source, $dest);

            return;
        }

        $sw = imagesx($photo);
        $sh = imagesy($photo);
        if ($sw < 1 || $sh < 1) {
            imagedestroy($photo);

            return;
        }

        $canvas = imagecreatetruecolor($width, $height);
        $white = imagecolorallocate($canvas, 250, 248, 243);
        imagefilledrectangle($canvas, 0, 0, $width, $height, $white);

        [$ar, $ag, $ab] = self::hexToRgb($accentHex);
        $accent = imagecolorallocate($canvas, $ar, $ag, $ab);
        $ink = imagecolorallocate($canvas, 31, 31, 31);
        $muted = imagecolorallocate($canvas, 90, 90, 90);

        // Page / book margins + spine strip on the left.
        $pad = max(10, (int) round(min($width, $height) * 0.045));
        $spine = max(6, (int) round($width * 0.035));
        $mast = max(36, (int) round($height * 0.11));
        $foot = max(22, (int) round($height * 0.06));

        imagefilledrectangle($canvas, 0, 0, $spine, $height, $accent);

        $innerX = $spine + $pad;
        $innerY = $pad + $mast;
        $innerW = $width - $innerX - $pad;
        $innerH = $height - $innerY - $pad - $foot;
        if ($innerW < 8 || $innerH < 8) {
            imagedestroy($photo);
            imagedestroy($canvas);

            return;
        }

        // Cover-crop photo into the plate.
        $scale = max($innerW / $sw, $innerH / $sh);
        $rw = (int) ceil($sw * $scale);
        $rh = (int) ceil($sh * $scale);
        $resized = imagecreatetruecolor($rw, $rh);
        imagecopyresampled($resized, $photo, 0, 0, 0, 0, $rw, $rh, $sw, $sh);
        imagedestroy($photo);
        $sx = (int) max(0, ($rw - $innerW) / 2);
        $sy = (int) max(0, ($rh - $innerH) / 2);
        // Variant nudges crop so paired covers don't look identical.
        $sx = min($rw - $innerW, max(0, $sx + (($variant % 3) - 1) * (int) ($innerW * 0.08)));
        $sy = min($rh - $innerH, max(0, $sy + (($variant % 2)) * (int) ($innerH * 0.05)));
        imagecopy($canvas, $resized, $innerX, $innerY, $sx, $sy, $innerW, $innerH);
        imagedestroy($resized);

        // Thin frame around the photo plate.
        $frame = imagecolorallocate($canvas, 230, 226, 218);
        imagerectangle($canvas, $innerX - 1, $innerY - 1, $innerX + $innerW, $innerY + $innerH, $frame);

        // Masthead.
        $milliSize = max(14, (int) round($width * 0.085));
        $nicheSize = max(10, (int) round($width * 0.038));
        $font = 5; // GD built-in; scale via imagestring for portability
        imagestring($canvas, 5, $innerX, (int) ($pad * 0.55), 'MILLI', $ink);
        $niche = strtoupper($word);
        imagestring($canvas, 3, $innerX, (int) ($pad * 0.55) + 16, $niche, $accent);

        // Larger masthead using TTF when available.
        $ttfCandidates = [
            '/usr/share/fonts/truetype/dejavu/DejaVuSerif-Bold.ttf',
            '/usr/share/fonts/truetype/liberation/LiberationSerif-Bold.ttf',
            '/usr/share/fonts/truetype/freefont/FreeSerifBold.ttf',
        ];
        $ttf = null;
        foreach ($ttfCandidates as $candidate) {
            if (is_file($candidate)) {
                $ttf = $candidate;
                break;
            }
        }
        if ($ttf && function_exists('imagettftext')) {
            imagefilledrectangle($canvas, $spine + 1, 0, $width, $pad + $mast - 4, $white);
            imagettftext($canvas, $milliSize * 0.72, 0, $innerX, $pad + (int) ($milliSize * 0.85), $ink, $ttf, 'MILLI');
            imagettftext(
                $canvas,
                $nicheSize * 0.9,
                0,
                $innerX,
                $pad + (int) ($milliSize * 0.85) + (int) ($nicheSize * 1.35),
                $accent,
                $ttf,
                $niche
            );
        }

        // Footer issue line.
        $issue = 'VOL.'.($variant + 1).'  ·  EDITION';
        if ($ttf && function_exists('imagettftext')) {
            imagettftext(
                $canvas,
                max(9, (int) ($width * 0.028)),
                0,
                $innerX,
                $height - (int) ($pad * 0.55),
                $muted,
                $ttf,
                $issue
            );
        } else {
            imagestring($canvas, 2, $innerX, $height - $pad - 4, $issue, $muted);
        }

        // Outer border.
        imagerectangle($canvas, 0, 0, $width - 1, $height - 1, $ink);

        $ext = strtolower(pathinfo($dest, PATHINFO_EXTENSION));
        if ($ext === 'png') {
            imagepng($canvas, $dest, 5);
        } else {
            imagejpeg($canvas, $dest, 92);
        }
        imagedestroy($canvas);
        @chmod($dest, 0644);
    }

    /**
     * @return array{0:int,1:int,2:int}
     */
    private static function hexToRgb(string $hex): array
    {
        $hex = ltrim($hex, '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }
        if (strlen($hex) !== 6) {
            return [87, 111, 114];
        }

        return [
            hexdec(substr($hex, 0, 2)),
            hexdec(substr($hex, 2, 2)),
            hexdec(substr($hex, 4, 2)),
        ];
    }

    /**
     * Restore classic Milli theme rasters from public/assets/milli-original
     * (snapshot of the culture-magazine HTML images folder).
     */
    private static function restoreOriginalTheme(string $imageDir): void
    {
        $pack = public_path('assets/milli-original');
        if (! is_dir($pack)) {
            return;
        }

        File::ensureDirectoryExists($imageDir);
        foreach (File::files($pack) as $file) {
            $name = $file->getFilename();
            // Keep niche pack files that live beside the classic theme.
            if (preg_match('/^(fashion|menswear|womenswear|kids|streetwear|jewellery|beauty|accessories|homeware)-/i', $name)) {
                continue;
            }
            File::copy($file->getPathname(), $imageDir.DIRECTORY_SEPARATOR.$name);
            @chmod($imageDir.DIRECTORY_SEPARATOR.$name, 0644);
        }

        if (is_file($imageDir.DIRECTORY_SEPARATOR.'hero-img.jpg')) {
            File::copy(
                $imageDir.DIRECTORY_SEPARATOR.'hero-img.jpg',
                $imageDir.DIRECTORY_SEPARATOR.'original-hero.jpg'
            );
            @chmod($imageDir.DIRECTORY_SEPARATOR.'original-hero.jpg', 0644);
        }

        @chmod($imageDir, 0755);
    }

    private static function copyAsThemeAsset(string $source, string $dest): void
    {
        if (! is_file($source)) {
            return;
        }
        // Avoid clobbering the pool source when target equals source path.
        if (realpath($source) && realpath($source) === realpath($dest)) {
            return;
        }

        $srcExt = strtolower(pathinfo($source, PATHINFO_EXTENSION));
        $dstExt = strtolower(pathinfo($dest, PATHINFO_EXTENSION));

        if ($srcExt === $dstExt || $dstExt === '') {
            File::copy($source, $dest);
            @chmod($dest, 0644);

            return;
        }

        // Cross-format: prefer GD when available; otherwise raw copy still beats empty.
        if (function_exists('imagecreatefromstring') && function_exists('imagejpeg') && function_exists('imagepng')) {
            $blob = @file_get_contents($source);
            $img = $blob !== false ? @imagecreatefromstring($blob) : false;
            if ($img !== false) {
                if ($dstExt === 'jpg' || $dstExt === 'jpeg') {
                    imagejpeg($img, $dest, 88);
                } elseif ($dstExt === 'png') {
                    imagesavealpha($img, true);
                    imagepng($img, $dest, 6);
                } elseif ($dstExt === 'webp' && function_exists('imagewebp')) {
                    imagewebp($img, $dest, 88);
                } else {
                    File::copy($source, $dest);
                }
                imagedestroy($img);
                @chmod($dest, 0644);

                return;
            }
        }

        File::copy($source, $dest);
        @chmod($dest, 0644);
    }

    private static function applyHomeHero(string $heading, string $text): void
    {
        $homePath = base_path('content/collections/pages/home.md');
        if (! File::exists($homePath)) {
            return;
        }

        $home = File::get($homePath);
        if (! preg_match('/type: home_hero\n/', $home)) {
            return;
        }

        $home = preg_replace(
            '/(type: home_hero\n(?:.*\n)*?    heading: ).*/',
            '$1'.self::quote($heading),
            $home,
            1
        ) ?? $home;
        $home = preg_replace(
            '/(type: home_hero\n(?:.*\n)*?    text: ).*/',
            '$1'.self::quote($text),
            $home,
            1
        ) ?? $home;

        File::put($homePath, $home);
    }

    private static function quote(string $value): string
    {
        return '"'.str_replace(['\\', '"'], ['\\\\', '\\"'], $value).'"';
    }
}
