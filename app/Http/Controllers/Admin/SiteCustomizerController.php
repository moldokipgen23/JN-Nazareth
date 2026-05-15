<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Settings;
use App\Http\Controllers\Controller;
use App\Models\BannerSlide;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SiteCustomizerController extends Controller
{
    public function index()
    {
        $slides = BannerSlide::orderBy('sort_order')->get();
        return view('admin.customizer.index', compact('slides'));
    }

    public function updateGeneral(Request $request)
    {
        $data = $request->validate([
            'site_name'       => 'required|string|max:100',
            'site_tagline'    => 'nullable|string|max:200',
            'contact_email'   => 'nullable|email|max:100',
            'contact_phone'   => 'nullable|string|max:50',
            'contact_address' => 'nullable|string|max:300',
            'footer_text'     => 'nullable|string|max:300',
            'social_facebook'  => 'nullable|url|max:200',
            'social_youtube'   => 'nullable|url|max:200',
            'social_instagram' => 'nullable|url|max:200',
            'social_twitter'   => 'nullable|url|max:200',
            'social_whatsapp'  => 'nullable|string|max:50',
            'cdn_base_url'            => 'nullable|url|max:300',
            'footer_quick_links_title' => 'nullable|string|max:60',
            'footer_contact_title'     => 'nullable|string|max:60',
            'footer_credit_text'       => 'nullable|string|max:150',
        ]);
        $data['footer_show_credit'] = $request->boolean('footer_show_credit') ? '1' : '0';
        Settings::setMany($data);
        return back()->with('success', 'General settings saved.');
    }

    public function updateAppearance(Request $request)
    {
        $data = $request->validate([
            'primary_color'   => 'required|string|max:20',
            'secondary_color' => 'required|string|max:20',
            'accent_color'    => 'required|string|max:20',
        ]);

        if ($request->hasFile('logo')) {
            $request->validate(['logo' => 'image|max:2048']);
            if (Settings::get('logo')) Storage::disk('public')->delete(Settings::get('logo'));
            $data['logo'] = $request->file('logo')->store('settings', 'public');
        }

        if ($request->hasFile('favicon')) {
            $request->validate(['favicon' => 'file|mimes:ico,png|max:512']);
            if (Settings::get('favicon')) Storage::disk('public')->delete(Settings::get('favicon'));
            $data['favicon'] = $request->file('favicon')->store('settings', 'public');
        }

        Settings::setMany($data);
        return back()->with('success', 'Appearance settings saved.');
    }

    public function updateSeo(Request $request)
    {
        $data = $request->validate([
            'seo_meta_title'       => 'nullable|string|max:200',
            'seo_meta_description' => 'nullable|string|max:400',
            'seo_meta_keywords'    => 'nullable|string|max:400',
            'seo_og_title'         => 'nullable|string|max:200',
            'seo_og_description'   => 'nullable|string|max:400',
            'seo_twitter_title'    => 'nullable|string|max:200',
            'seo_twitter_desc'     => 'nullable|string|max:400',
            'seo_robots'           => 'nullable|string|max:100',
            'seo_canonical_url'    => 'nullable|url|max:300',
            'seo_schema_org_name'  => 'nullable|string|max:200',
        ]);

        if ($request->hasFile('seo_og_image')) {
            $request->validate(['seo_og_image' => 'image|max:2048']);
            if (Settings::get('seo_og_image')) Storage::disk('public')->delete(Settings::get('seo_og_image'));
            $data['seo_og_image'] = $request->file('seo_og_image')->store('settings', 'public');
        }

        Settings::setMany($data);
        return back()->with('success', 'SEO settings saved.');
    }

    public function updateHero(Request $request)
    {
        $data = $request->validate([
            'hero_title'       => 'required|string|max:200',
            'hero_subtitle'    => 'nullable|string|max:400',
            'hero_cta_text'    => 'nullable|string|max:80',
            'hero_cta_link'    => 'nullable|string|max:200',
            'about_preview'    => 'nullable|string|max:500',
            'hero_youtube_url' => 'nullable|string|max:300',
        ]);
        Settings::setMany($data);
        return back()->with('success', 'Hero section saved.');
    }

    public function updateStory(Request $request)
    {
        $data = $request->validate([
            'leader_name'        => 'nullable|string|max:150',
            'leader_title'       => 'nullable|string|max:150',
            'leader_description' => 'nullable|string|max:1000',
            'story_title'        => 'nullable|string|max:200',
            'story_content'      => 'nullable|string|max:5000',
        ]);

        if ($request->hasFile('leader_photo')) {
            $request->validate(['leader_photo' => 'image|max:3072']);
            if (Settings::get('leader_photo')) Storage::disk('public')->delete(Settings::get('leader_photo'));
            $data['leader_photo'] = $request->file('leader_photo')->store('leaders', 'public');
        }

        Settings::setMany($data);
        return back()->with('success', 'Story section saved successfully.');
    }

    public function updateSections(Request $request)
    {
        $data = $request->validate([
            'stat_1_value' => 'nullable|string|max:60',
            'stat_1_label' => 'nullable|string|max:60',
            'stat_2_value' => 'nullable|string|max:60',
            'stat_2_label' => 'nullable|string|max:60',
            'stat_3_value' => 'nullable|string|max:60',
            'stat_3_label' => 'nullable|string|max:60',
            'stat_4_value' => 'nullable|string|max:60',
            'stat_4_label' => 'nullable|string|max:60',
            'story_video_url'      => 'nullable|string|max:300',
            'story_video_title'    => 'nullable|string|max:150',
            'story_video_subtitle' => 'nullable|string|max:300',
            'about_section_label' => 'nullable|string|max:80',
            'about_section_title' => 'nullable|string|max:200',
            'about_preview'       => 'nullable|string|max:1000',
            'about_btn_text'      => 'nullable|string|max:80',
            'about_btn_link'      => 'nullable|string|max:200',
            'sec_title_story'        => 'nullable|string|max:120',
            'sec_title_programs'     => 'nullable|string|max:120',
            'sec_sub_programs'       => 'nullable|string|max:400',
            'sec_title_map'          => 'nullable|string|max:120',
            'sec_title_videos'       => 'nullable|string|max:120',
            'sec_sub_videos'         => 'nullable|string|max:300',
            'sec_title_events'       => 'nullable|string|max:120',
            'sec_label_events'       => 'nullable|string|max:80',
            'sec_sub_events'         => 'nullable|string|max:300',
            'sec_title_gallery'      => 'nullable|string|max:120',
            'sec_label_gallery'      => 'nullable|string|max:80',
            'sec_sub_gallery'        => 'nullable|string|max:300',
            'sec_title_blog'         => 'nullable|string|max:120',
            'sec_label_blog'         => 'nullable|string|max:80',
            'sec_sub_blog'           => 'nullable|string|max:300',
            'sec_title_cta'          => 'nullable|string|max:120',
            'sec_sub_cta'            => 'nullable|string|max:400',
            'sec_title_location'     => 'nullable|string|max:120',
            'sec_title_hall_of_fame' => 'nullable|string|max:120',
            'sec_sub_hall_of_fame'   => 'nullable|string|max:400',
            'cta_btn1_text'   => 'nullable|string|max:80',
            'cta_btn1_link'   => 'nullable|string|max:200',
            'cta_btn2_text'   => 'nullable|string|max:80',
            'cta_btn2_link'   => 'nullable|string|max:200',
            'contact_address' => 'nullable|string|max:300',
            'contact_phone'   => 'nullable|string|max:50',
            'map_embed_url'        => 'nullable|string|max:1000',
            'map_section_subtitle' => 'nullable|string|max:300',
        ]);

        $allSectionKeys = ['stats','about','story','programs','map','videos','events','gallery','blog','cta','location','hall_of_fame','story-video','story_video'];
        foreach ($allSectionKeys as $key) {
            $data['sec_show_' . $key] = $request->boolean('sec_show_' . $key) ? '1' : '0';
        }

        Settings::setMany($data);
        return back()->with('success', 'Section settings saved.');
    }

    public function storeSlide(Request $request)
    {
        $request->validate([
            'image'       => 'required|image|max:4096',
            'title'       => 'nullable|string|max:150',
            'subtitle'    => 'nullable|string|max:300',
            'button_text' => 'nullable|string|max:60',
            'button_link' => 'nullable|string|max:200',
        ]);
        $path = $request->file('image')->store('slides', 'public');
        BannerSlide::create([
            'image'       => $path,
            'title'       => $request->title,
            'subtitle'    => $request->subtitle,
            'button_text' => $request->button_text,
            'button_link' => $request->button_link,
            'sort_order'  => BannerSlide::max('sort_order') + 1,
            'active'      => true,
        ]);
        return back()->with('success', 'Slide added.');
    }

    public function destroySlide(BannerSlide $slide)
    {
        Storage::disk('public')->delete($slide->image);
        $slide->delete();
        return back()->with('success', 'Slide deleted.');
    }

    public function toggleSlide(BannerSlide $slide)
    {
        $slide->update(['active' => !$slide->active]);
        return back()->with('success', 'Slide updated.');
    }

    public function updateSectionOrder(Request $request)
    {
        $request->validate(['order' => 'required|string']);
        $order = json_decode($request->order, true);
        if (!is_array($order)) {
            return response()->json(['error' => 'Invalid order'], 422);
        }
        Settings::set('home_section_order', json_encode(array_values($order)));
        return response()->json(['success' => true]);
    }

    public function toggleSection(Request $request)
    {
        $request->validate(['key' => 'required|string|max:60', 'value' => 'required|in:0,1']);
        Settings::set('sec_show_' . $request->key, $request->value ? '1' : '0');
        return response()->json(['success' => true]);
    }

    public function updateStats(Request $request)
    {
        $data = $request->validate([
            'stat_1_value' => 'nullable|string|max:60',
            'stat_1_label' => 'nullable|string|max:60',
            'stat_2_value' => 'nullable|string|max:60',
            'stat_2_label' => 'nullable|string|max:60',
            'stat_3_value' => 'nullable|string|max:60',
            'stat_3_label' => 'nullable|string|max:60',
            'stat_4_value' => 'nullable|string|max:60',
            'stat_4_label' => 'nullable|string|max:60',
        ]);
        Settings::setMany($data);
        return back()->with('success', 'Stats saved.');
    }

    public function updateLocation(Request $request)
    {
        $data = $request->validate([
            'location_map_link'      => 'nullable|string|max:500',
            'location_embed_url'     => 'nullable|string|max:1000',
            'location_route_steps'   => 'nullable|string',
            'location_nearby_places' => 'nullable|string',
        ]);
        foreach (['location_route_steps', 'location_nearby_places'] as $key) {
            if (!empty($data[$key])) {
                $decoded = json_decode($data[$key], true);
                if (!is_array($decoded)) {
                    return back()->withErrors([$key => 'Invalid data format.']);
                }
            }
        }
        Settings::setMany($data);
        return back()->with('success', 'Location section saved.');
    }
}
