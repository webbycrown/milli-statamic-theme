<?php

/**
 * Marketplace banners for Milli (not exported by starter-kit.yaml).
 * Run: php scripts/generate_banners.php
 */

$root = dirname(__DIR__);
$img = $root.'/public/assets/milli-original';
$fallback = $root.'/public/assets/image';
$out = $root.'/public/assets/images/marketplace';
$bold = $root.'/storage/fonts/LiberationSans-Bold.ttf';
$reg = $root.'/storage/fonts/LiberationSans-Regular.ttf';

@mkdir($out, 0775, true);

$sage = [87, 111, 114];      // #576F72
$soft = [232, 228, 217];     // #E8E4D9
$ink = [31, 31, 31];
$white = [255, 255, 255];

$domain = 'milli-statamic.webbydemo.in';
$repo = 'github.com/webbycrown/milli-statamic-theme';

function src(string $name, string $img, string $fallback): string
{
    $a = $img.'/'.$name;
    if (is_file($a)) {
        return $a;
    }
    $b = $fallback.'/'.$name;
    if (is_file($b)) {
        return $b;
    }
    throw new RuntimeException('Missing image: '.$name);
}

function loadCover(string $path, int $w, int $h)
{
    $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
    $blob = file_get_contents($path);
    $from = @imagecreatefromstring($blob);
    if ($from === false) {
        throw new RuntimeException('Cannot read '.$path);
    }
    $sw = imagesx($from);
    $sh = imagesy($from);
    $scale = max($w / $sw, $h / $sh);
    $nw = (int) round($sw * $scale);
    $nh = (int) round($sh * $scale);
    $dst = imagecreatetruecolor($w, $h);
    imagecopyresampled($dst, $from, (int) (($w - $nw) / 2), (int) (($h - $nh) / 2), 0, 0, $nw, $nh, $sw, $sh);
    imagedestroy($from);

    return $dst;
}

function rgb($im, array $c)
{
    return imagecolorallocate($im, $c[0], $c[1], $c[2]);
}

function overlayPanel($im, int $w, int $h, array $sage): void
{
    $panel = imagecreatetruecolor($w, $h);
    imagealphablending($panel, false);
    imagesavealpha($panel, true);
    $c = imagecolorallocatealpha($panel, $sage[0], $sage[1], $sage[2], 40);
    imagefilledrectangle($panel, 0, 0, $w, $h, $c);
    imagecopymerge($im, $panel, 0, 0, 0, 0, $w, $h, 58);
    imagedestroy($panel);
}

function writeBanner(
    string $src,
    string $dest,
    int $w,
    int $h,
    string $kicker,
    string $title,
    string $line,
    string $domain,
    string $tags,
    string $bold,
    string $reg,
    array $sage,
    array $soft,
    array $white
): void {
    $im = loadCover($src, $w, $h);
    overlayPanel($im, $w, $h, $sage);

    $softC = rgb($im, $soft);
    $whiteC = rgb($im, $white);
    imagefilledrectangle($im, 0, 0, (int) max(10, $w * 0.012), $h, $softC);

    $pad = (int) ($w * 0.06);
    $y = (int) ($h * 0.20);
    imagettftext($im, max(14, $w * 0.018), 0, $pad, $y, $softC, $bold, strtoupper($kicker));
    $y += (int) ($h * 0.14);
    imagettftext($im, max(36, $w * 0.07), 0, $pad, $y, $whiteC, $bold, $title);
    $y += (int) ($h * 0.08);
    imagettftext($im, max(15, $w * 0.024), 0, $pad, $y, $whiteC, $reg, $line);
    $y += (int) ($h * 0.09);
    imagettftext($im, max(14, $w * 0.02), 0, $pad, $y, $softC, $bold, $domain);

    imagettftext($im, max(12, $w * 0.015), 0, $pad, (int) ($h * 0.88), $whiteC, $reg, $tags);

    imagejpeg($im, $dest, 90);
    imagedestroy($im);
    echo 'wrote '.basename($dest)."\n";
}

function writeThumb(
    string $src,
    string $dest,
    int $size,
    string $title,
    string $domain,
    string $bold,
    string $reg,
    array $sage,
    array $soft,
    array $white
): void {
    $im = loadCover($src, $size, $size);
    overlayPanel($im, $size, $size, $sage);
    $softC = rgb($im, $soft);
    $whiteC = rgb($im, $white);
    imagefilledrectangle($im, 0, 0, 12, $size, $softC);
    imagettftext($im, 16, 0, 40, 90, $softC, $bold, 'STATAMIC KIT');
    imagettftext($im, 52, 0, 40, 175, $whiteC, $bold, $title);
    imagettftext($im, 15, 0, 40, 230, $softC, $bold, $domain);
    imagettftext($im, 15, 0, 40, 540, $whiteC, $reg, 'Culture · News · Editions');
    imagejpeg($im, $dest, 90);
    imagedestroy($im);
    echo 'wrote '.basename($dest)."\n";
}

$hero = src('hero-img.jpg', $img, $fallback);
$about = src('about-intro.jpg', $img, $fallback);
$editions = src('Editions.jpg', $img, $fallback);
$news = src('News.jpg', $img, $fallback);
$culture = src('culture-desc-img.jpg', $img, $fallback);
$stories = src('stories-img-1.jpg', $img, $fallback);
$aboutPage = src('About.jpg', $img, $fallback);

writeThumb($hero, "$out/00-milli-thumb.jpg", 600, 'MILLI', $domain, $bold, $reg, $sage, $soft, $white);

writeBanner($hero, "$out/01-milli-main.jpg", 1600, 900, 'Statamic starter kit', 'MILLI', 'Culture magazine. Ten catalogue demos.', $domain, 'Original  ·  Fashion  ·  Kids  ·  +7 niches', $bold, $reg, $sage, $soft, $white);

writeBanner($hero, "$out/02-milli-home.jpg", 1200, 800, 'Homepage', 'Culture in print', 'News, editions, stories, and workshops.', $domain, 'Home  ·  Hero  ·  Page builder', $bold, $reg, $sage, $soft, $white);

writeBanner($editions, "$out/03-milli-editions.jpg", 1200, 800, 'Editions', 'Magazine covers', 'Comic-book edition spreads and galleries.', $domain, 'Editions  ·  Slider  ·  Gallery', $bold, $reg, $sage, $soft, $white);

writeBanner($news, "$out/04-milli-news.jpg", 1200, 800, 'News', 'Articles & archive', 'Star-cut news cards with load more.', $domain, 'News  ·  Detail  ·  Pagination', $bold, $reg, $sage, $soft, $white);

writeBanner($culture, "$out/05-milli-culture.jpg", 1200, 800, 'Culture', 'Blogs & stories', 'Culture posts with full detail layouts.', $domain, 'Culture  ·  Grid  ·  Detail', $bold, $reg, $sage, $soft, $white);

writeBanner($aboutPage, "$out/06-milli-about.jpg", 1200, 800, 'About', 'The magazine desk', 'About, team culture, and careers.', $domain, 'About  ·  Career  ·  Apply', $bold, $reg, $sage, $soft, $white);

writeBanner($stories, "$out/07-milli-stories.jpg", 1200, 800, 'Stories', 'Round cultural frames', 'Home cultural stories section.', $domain, 'Stories  ·  Circles  ·  Explore', $bold, $reg, $sage, $soft, $white);

writeBanner($about, "$out/08-milli-catalogues.jpg", 1200, 800, 'Catalogues', 'Ten demos', 'Original + nine niche colours and packs.', $domain, 'original fashion menswear womenswear kids streetwear jewellery beauty accessories homeware', $bold, $reg, $sage, $soft, $white);

writeBanner($hero, "$out/09-milli-github.jpg", 1200, 800, 'GitHub', 'webbycrown/milli-statamic-theme', 'Starter kit source and issues.', $repo, 'Statamic 5  ·  Starter kit  ·  Marketplace', $bold, $reg, $sage, $soft, $white);

$zipPath = "$out/milli-banners.zip";
@unlink($zipPath);
if (class_exists('ZipArchive')) {
    $zip = new ZipArchive();
    if ($zip->open($zipPath, ZipArchive::CREATE) === true) {
        foreach (glob("$out/*.jpg") as $file) {
            $zip->addFile($file, basename($file));
        }
        $zip->close();
        echo "wrote milli-banners.zip\n";
    }
} else {
    $cwd = getcwd();
    chdir($out);
    passthru('zip -9 -j milli-banners.zip *.jpg', $code);
    chdir($cwd);
    echo $code === 0 ? "wrote milli-banners.zip\n" : "zip skipped (install zip or php-zip)\n";
}

echo "done\n";
