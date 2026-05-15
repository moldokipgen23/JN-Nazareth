<?php

namespace Database\Seeders;

use App\Models\BannerSlide;
use App\Models\HallOfFame;
use App\Models\Video;
use App\Models\GalleryFolder;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@demo.com')->first()
                 ?? User::first();

        $adminId = $admin?->id ?? 1;

        // Banner slides (images uploaded later via admin panel)
        $slides = [
            ['title' => 'Welcome to Our Community',
             'subtitle' => 'A place to connect, share, and grow together.',
             'button_text' => 'Learn More', 'button_link' => '/about',
             'sort_order' => 1, 'active' => true, 'image' => ''],
            ['title' => 'Discover Our Programmes',
             'subtitle' => 'Get involved in events, classes, and activities all year round.',
             'button_text' => 'See What\'s On', 'button_link' => '/events',
             'sort_order' => 2, 'active' => true, 'image' => ''],
            ['title' => 'Be Part of Our Story',
             'subtitle' => 'Everyone is welcome. Come as you are.',
             'button_text' => 'Join Us', 'button_link' => '/contact',
             'sort_order' => 3, 'active' => true, 'image' => ''],
        ];
        foreach ($slides as $s) {
            BannerSlide::firstOrCreate(['title' => $s['title']], $s);
        }

        // Videos (replace with real YouTube URLs after install)
        $videos = [
            ['title'       => 'Welcome from Our Director',
             'youtube_url' => 'https://www.youtube.com/watch?v=QH2-TGUlwu4',
             'description' => 'A short message introducing our community and what we stand for.',
             'category'    => 'general', 'sort_order' => 1, 'active' => true, 'created_by' => $adminId],
            ['title'       => 'Highlights from Our Annual Gathering',
             'youtube_url' => 'https://www.youtube.com/watch?v=ktvTqknDobU',
             'description' => 'A look back at the highlights of our annual community gathering.',
             'category'    => 'event', 'sort_order' => 2, 'active' => true, 'created_by' => $adminId],
            ['title'       => 'Volunteer Programme Spotlight',
             'youtube_url' => 'https://www.youtube.com/watch?v=09R8_2nJtjg',
             'description' => 'Meet the volunteers who make our programmes possible.',
             'category'    => 'general', 'sort_order' => 3, 'active' => true, 'created_by' => $adminId],
            ['title'       => 'Youth Programme Recap',
             'youtube_url' => 'https://www.youtube.com/watch?v=1G4isv_Fylg',
             'description' => 'A recap of our recent youth programme — activities, learning, and fun.',
             'category'    => 'event', 'sort_order' => 4, 'active' => true, 'created_by' => $adminId],
        ];
        foreach ($videos as $v) {
            Video::firstOrCreate(['title' => $v['title']], $v);
        }

        // Gallery and Programme folders (mix of verticals to show breadth)
        $galleries = [
            ['name' => 'Annual Gathering 2024',       'type' => 'gallery',  'description' => 'Photos from our biggest community event of the year.',                          'sort_order' => 1, 'created_by' => $adminId],
            ['name' => 'Holiday Celebration 2024',    'type' => 'gallery',  'description' => 'Memories from our seasonal celebration with members and families.',             'sort_order' => 2, 'created_by' => $adminId],
            ['name' => 'Youth Camp 2024',             'type' => 'gallery',  'description' => 'Five days of activities, learning, and friendship at our annual youth camp.',  'sort_order' => 3, 'created_by' => $adminId],
            ['name' => 'Founders Day Dinner',         'type' => 'gallery',  'description' => 'Celebrating our founding anniversary with a special dinner and programme.',     'sort_order' => 4, 'created_by' => $adminId],
            ['name' => 'Community Clean-Up Day',      'type' => 'programs', 'description' => 'Volunteers came together to clean local public spaces and parks.',              'sort_order' => 1, 'created_by' => $adminId],
            ['name' => 'Back-to-School Supply Drive', 'type' => 'programs', 'description' => 'Collecting and distributing school supplies to local families.',                'sort_order' => 2, 'created_by' => $adminId],
            ['name' => 'Food Bank Outreach',          'type' => 'programs', 'description' => 'Partnering with local food banks to support neighbours in need.',               'sort_order' => 3, 'created_by' => $adminId],
        ];
        foreach ($galleries as $g) {
            GalleryFolder::firstOrCreate(['name' => $g['name']], $g);
        }

        // Hall of Fame (mix of verticals — community founder, educator, philanthropist, athlete, leader)
        $entries = [
            ['name' => 'Eleanor Hart',
             'achievement_title' => 'Founding Member',
             'description'       => 'Eleanor was instrumental in establishing the organisation. Her vision of an inclusive, service-driven community shaped everything we do today.',
             'year'              => 2008,
             'featured'          => true, 'active' => true, 'sort_order' => 1],
            ['name' => 'Dr. Marcus Wei',
             'achievement_title' => 'First Educational Programme Director',
             'description'       => 'Dr. Wei designed our first educational outreach programme, which now serves over 200 students each year through scholarships and mentoring.',
             'year'              => 2012,
             'featured'          => true, 'active' => true, 'sort_order' => 2],
            ['name' => 'Amara Okafor',
             'achievement_title' => 'Lifetime Service Award',
             'description'       => 'Over twenty years of volunteering, Amara has led food drives, mentored youth, and organised community events that have touched thousands of lives.',
             'year'              => 2023,
             'featured'          => true, 'active' => true, 'sort_order' => 3],
            ['name' => 'James Sullivan',
             'achievement_title' => 'Youth Programme Pioneer',
             'description'       => 'James launched the first youth leadership programme, which has graduated more than 150 young leaders into roles across the community.',
             'year'              => 2015,
             'featured'          => false, 'active' => true, 'sort_order' => 4],
            ['name' => 'Priya Sharma',
             'achievement_title' => 'Community Impact Award',
             'description'       => 'Priya led the community garden initiative, transforming an abandoned lot into a thriving space that feeds families and brings neighbours together.',
             'year'              => 2020,
             'featured'          => false, 'active' => true, 'sort_order' => 5],
        ];
        foreach ($entries as $f) {
            HallOfFame::firstOrCreate(['name' => $f['name']], $f);
        }
    }
}
