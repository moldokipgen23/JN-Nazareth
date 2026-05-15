<?php

namespace App\Http\Controllers;

use App\Helpers\Settings;
use App\Models\Blog;
use App\Models\Event;
use App\Models\Page;
use App\Models\GalleryFolder;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function sitemap(): Response
    {
        $now = now()->toAtomString();
        $urls = [];

        // Static pages
        $staticPages = [
            ['loc' => url('/'),        'lastmod' => $now, 'priority' => '1.0', 'changefreq' => 'weekly'],
            ['loc' => url('/about'),   'lastmod' => $now, 'priority' => '0.8', 'changefreq' => 'monthly'],
            ['loc' => url('/blogs'),   'lastmod' => $now, 'priority' => '0.8', 'changefreq' => 'weekly'],
            ['loc' => url('/events'),  'lastmod' => $now, 'priority' => '0.8', 'changefreq' => 'weekly'],
            ['loc' => url('/gallery'), 'lastmod' => $now, 'priority' => '0.7', 'changefreq' => 'weekly'],
            ['loc' => url('/videos'),  'lastmod' => $now, 'priority' => '0.7', 'changefreq' => 'weekly'],
        ];
        foreach ($staticPages as $p) $urls[] = $p;

        // Blog posts (individual pages)
        Blog::where('published', true)->orderByDesc('created_at')->each(function ($blog) use (&$urls) {
            $urls[] = [
                'loc'        => url('/blogs/' . $blog->slug),
                'lastmod'    => $blog->updated_at->toAtomString(),
                'priority'   => '0.7',
                'changefreq' => 'monthly',
            ];
        });

        // Events — /events is already added above; use most-recent updated_at as its lastmod
        $latestEvent = Event::orderByDesc('updated_at')->first();
        if ($latestEvent) {
            foreach ($urls as &$u) {
                if ($u['loc'] === url('/events')) {
                    $u['lastmod'] = $latestEvent->updated_at->toAtomString();
                    break;
                }
            }
            unset($u);
        }

        // Gallery folders (individual pages)
        GalleryFolder::orderByDesc('created_at')->each(function ($folder) use (&$urls) {
            $urls[] = [
                'loc'        => url('/gallery/folder/' . $folder->id),
                'lastmod'    => $folder->updated_at->toAtomString(),
                'priority'   => '0.6',
                'changefreq' => 'monthly',
            ];
        });

        // Custom pages
        Page::orderBy('title')->each(function ($page) use (&$urls) {
            $urls[] = [
                'loc'        => url('/page/' . $page->slug),
                'lastmod'    => $page->updated_at->toAtomString(),
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
