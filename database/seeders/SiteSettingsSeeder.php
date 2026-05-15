<?php

namespace Database\Seeders;

use App\Helpers\Settings;
use Illuminate\Database\Seeder;

class SiteSettingsSeeder extends Seeder
{
    public function run(): void
    {
        Settings::setMany([
            // General
            'site_name'       => 'My Community Website',
            'site_tagline'    => 'Connecting people. Sharing stories. Building together.',
            'contact_email'   => 'hello@example.com',
            'contact_phone'   => '+1 555 000 1234',
            'contact_address' => '123 Main Street, Your City',
            'footer_text'     => '© ' . date('Y') . ' My Community Website. All rights reserved.',
            'social_facebook'  => '',
            'social_youtube'   => '',
            'social_instagram' => '',
            'social_twitter'   => '',
            'social_whatsapp'  => '',
            'cdn_base_url'             => '',
            'footer_quick_links_title' => 'Quick Links',
            'footer_contact_title'     => 'Contact Us',
            'footer_show_credit'       => '1',
            'footer_credit_text'       => 'Designed by Ehlom Digital',

            // Appearance
            'primary_color'   => '#2d6a4f',
            'secondary_color' => '#40916c',
            'accent_color'    => '#d4a017',
            'logo'            => '',
            'favicon'         => '',

            // SEO
            'seo_meta_title'       => 'My Community Website',
            'seo_meta_description' => 'A welcoming community website powered by Ehlom Blog CMS. Share news, events, photos and stories with your members.',
            'seo_meta_keywords'    => 'community, blog, events, gallery, news',
            'seo_robots'           => 'index, follow',
            'seo_canonical_url'    => '',
            'seo_og_title'         => 'My Community Website',
            'seo_og_description'   => 'Connecting people. Sharing stories. Building together.',
            'seo_og_image'         => '',
            'seo_twitter_title'    => 'My Community Website',
            'seo_twitter_desc'     => 'Connecting people. Sharing stories. Building together.',
            'seo_schema_org_name'  => 'My Community Website',

            // Hero
            'hero_title'       => 'Welcome to Our Community',
            'hero_subtitle'    => 'A place to connect, share, and grow together. Everyone is welcome.',
            'hero_cta_text'    => 'Learn More',
            'hero_cta_link'    => '/about',
            'hero_youtube_url' => '',

            // About preview
            'about_section_label' => 'Who We Are',
            'about_section_title' => 'A Community Built on Connection & Care',
            'about_preview'       => 'We are a vibrant community built around shared values, care for one another, and a commitment to service. Whether you are joining us for the first time or have been with us for years, you belong here.',
            'about_btn_text'      => 'Learn More About Us',
            'about_btn_link'      => '/about',
            'about_pills'         => 'Connect,Support,Serve,Grow',
            'about_image_1'       => 'https://images.unsplash.com/photo-1529156069898-49953e39b3ac?w=800&h=600&fit=crop&q=80',
            'about_image_2'       => 'https://images.unsplash.com/photo-1559027615-cd4628902d4a?w=800&h=600&fit=crop&q=80',

            // Story / Leadership
            'leader_name'        => 'Alex Carter',
            'leader_title'       => 'Director',
            'leader_description' => 'Alex leads our organisation with over fifteen years of community-building experience. Under their guidance, we have grown from a small group of volunteers into a thriving community of 500+ members running programmes across education, outreach, and youth development.',
            'leader_photo'       => 'https://images.unsplash.com/photo-1560250097-0b93528c311a?w=600&h=800&fit=crop&q=80',
            'story_title'        => 'Our Story',
            'story_content'      => "Our organisation began with a simple goal: to bring people together and make a difference. Over the years we've grown into a thriving community of members, volunteers, and supporters.\n\nFrom day one, we've been built on the principles of inclusion, service, and shared purpose. Whether through events, programmes, or everyday encounters, our mission has always been to enrich the lives of those around us.\n\nToday, we continue that work — running programmes, hosting events, and welcoming everyone who wishes to be part of our story.",

            // Stats
            'stat_1_value' => '500+',
            'stat_1_label' => 'Members',
            'stat_2_value' => '15+',
            'stat_2_label' => 'Years Active',
            'stat_3_value' => '12',
            'stat_3_label' => 'Programmes',
            'stat_4_value' => '200+',
            'stat_4_label' => 'Events Hosted',

            // Map / Location
            'location_map_link'  => 'https://maps.google.com',
            'location_embed_url' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3153.0!2d-122.4!3d37.77!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMzfCsDQ2JzEyLjAiTiAxMjLCsDI0JzAwLjAiVw!5e0!3m2!1sen!2sus!4v1600000000000!5m2!1sen!2sus',
            'map_embed_url'   => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3153.0!2d-122.4!3d37.77!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMzfCsDQ2JzEyLjAiTiAxMjLCsDI0JzAwLjAiVw!5e0!3m2!1sen!2sus!4v1600000000000!5m2!1sen!2sus',
            'map_section_subtitle' => 'Find us at our main location. We look forward to welcoming you.',

            // Video section
            'story_video_url'      => '',
            'story_video_title'    => '',
            'story_video_subtitle' => '',

            // Section visibility
            'sec_show_stats'         => '1',
            'sec_show_about'         => '1',
            'sec_show_story'         => '1',
            'sec_show_programs'      => '1',
            'sec_show_map'           => '1',
            'sec_show_videos'        => '1',
            'sec_show_events'        => '1',
            'sec_show_gallery'       => '1',
            'sec_show_blog'          => '1',
            'sec_show_cta'           => '1',
            'sec_show_location'      => '1',
            'sec_show_hall_of_fame'  => '1',

            // Section headings
            'sec_title_story'        => 'Leadership & Our Story',
            'sec_title_programs'     => 'Our Programmes',
            'sec_sub_programs'       => 'A look at the activities, programmes, and initiatives that bring our community to life.',
            'sec_title_map'          => 'Find Us',
            'sec_title_videos'       => 'Videos',
            'sec_sub_videos'         => 'Watch the latest videos from our community.',
            'sec_title_events'       => 'Upcoming Events',
            'sec_label_events'       => "What's On",
            'sec_sub_events'         => 'Stay connected with what\'s happening with us.',
            'sec_title_gallery'      => 'Photo Gallery',
            'sec_label_gallery'      => 'Memories',
            'sec_sub_gallery'        => 'A collection of moments from our programmes, events, and daily life.',
            'sec_title_blog'         => 'Latest News',
            'sec_label_blog'         => 'News & Updates',
            'sec_sub_blog'           => 'Stay informed with the latest news from our community.',
            'sec_title_cta'          => 'Join Our Community',
            'sec_sub_cta'            => 'You are always welcome. Come and be part of what we are building together.',
            'sec_title_location'     => 'How to Find Us',
            'sec_title_hall_of_fame' => 'Hall of Fame',
            'sec_sub_hall_of_fame'   => 'Honouring the people who have shaped our community.',

            // CTA buttons
            'cta_btn1_text' => 'Plan Your Visit',
            'cta_btn1_link' => '/about',
            'cta_btn2_text' => 'Contact Us',
            'cta_btn2_link' => '/contact',
        ]);
    }
}
