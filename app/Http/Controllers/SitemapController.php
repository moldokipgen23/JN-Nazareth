<?php

namespace App\Http\Controllers;

use App\Helpers\Settings;
use App\Models\Blog;
use App\Models\GalleryFolder;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function sitemap(): Response
    {
        $now = now()->toAtomString();
        $urls = [];

        // Static public pages — current site routes.
        $staticPages = [
            ['/',                        '1.0', 'weekly'],
            ['/about',                   '0.8', 'monthly'],
            ['/academics',               '0.8', 'monthly'],
            ['/academics/fee-structure', '0.6', 'yearly'],
            ['/academics/school-timing', '0.6', 'yearly'],
            ['/academics/calendar',      '0.6', 'monthly'],
            ['/academics/curriculum',    '0.6', 'yearly'],
            ['/academics/textbooks',     '0.6', 'yearly'],
            ['/academics/results',       '0.6', 'monthly'],
            ['/admission',               '0.9', 'monthly'],
            ['/student-life',            '0.7', 'weekly'],
            ['/hall-of-fame',            '0.7', 'monthly'],
            ['/news',                    '0.8', 'weekly'],
            ['/contact',                 '0.7', 'yearly'],
        ];
        foreach ($staticPages as [$path, $priority, $freq]) {
            $urls[] = ['loc' => url($path), 'lastmod' => $now, 'priority' => $priority, 'changefreq' => $freq];
        }

        // News / notice / announcement posts (individual pages).
        Blog::where('published', true)->orderByDesc('created_at')->each(function ($blog) use (&$urls) {
            $urls[] = [
                'loc'        => url('/news/' . $blog->slug),
                'lastmod'    => optional($blog->updated_at)->toAtomString(),
                'priority'   => '0.7',
                'changefreq' => 'monthly',
            ];
        });

        // Photo albums (individual pages).
        GalleryFolder::orderByDesc('created_at')->each(function ($folder) use (&$urls) {
            $urls[] = [
                'loc'        => url('/gallery/folder/' . $folder->id),
                'lastmod'    => optional($folder->updated_at)->toAtomString(),
                'priority'   => '0.6',
                'changefreq' => 'monthly',
            ];
        });

        // Deduplicate by loc (keep first occurrence)
        $seen = [];
        $unique = [];
        foreach ($urls as $url) {
            if (!isset($seen[$url['loc']])) {
                $seen[$url['loc']] = true;
                $unique[] = $url;
            }
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        foreach ($unique as $url) {
            $xml .= "  <url>\n";
            $xml .= "    <loc>" . htmlspecialchars($url['loc']) . "</loc>\n";
            if (!empty($url['lastmod']))    $xml .= "    <lastmod>{$url['lastmod']}</lastmod>\n";
            if (!empty($url['changefreq'])) $xml .= "    <changefreq>{$url['changefreq']}</changefreq>\n";
            if (!empty($url['priority']))   $xml .= "    <priority>{$url['priority']}</priority>\n";
            $xml .= "  </url>\n";
        }
        $xml .= '</urlset>';

        return response($xml, 200, [
            'Content-Type'  => 'application/xml; charset=utf-8',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }

    public function robots(): Response
    {
        $robotsDirective = Settings::get('seo_robots', 'index, follow');
        $disallow = str_contains($robotsDirective, 'noindex') ? 'Disallow: /' : 'Disallow: /admin';

        $content = "User-agent: *\n";
        $content .= $disallow . "\n";
        $content .= "Allow: /\n";
        $content .= "\nSitemap: " . url('/sitemap.xml') . "\n";

        return response($content, 200, ['Content-Type' => 'text/plain']);
    }
}
