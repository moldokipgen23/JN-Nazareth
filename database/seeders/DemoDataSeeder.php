<?php

namespace Database\Seeders;

use App\Models\BannerSlide;
use App\Models\GalleryItem;
use App\Models\HallOfFame;
use App\Models\Student;
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

        $slides = [
            ['title' => 'Welcome to Our Community',
             'subtitle' => 'A place to connect, share, and grow together.',
             'button_text' => 'Learn More', 'button_link' => '/about',
             'sort_order' => 1, 'active' => true,
             'image' => 'https://images.unsplash.com/photo-1529156069898-49953e39b3ac?w=1920&h=1080&fit=crop&q=80'],
            ['title' => 'Discover Our Programmes',
             'subtitle' => 'Get involved in events, classes, and activities all year round.',
             'button_text' => 'See What\'s On', 'button_link' => '/events',
             'sort_order' => 2, 'active' => true,
             'image' => 'https://images.unsplash.com/photo-1559027615-cd4628902d4a?w=1920&h=1080&fit=crop&q=80'],
            ['title' => 'Be Part of Our Story',
             'subtitle' => 'Everyone is welcome. Come as you are.',
             'button_text' => 'Join Us', 'button_link' => '/contact',
             'sort_order' => 3, 'active' => true,
             'image' => 'https://images.unsplash.com/photo-1582213782179-e0d53f98f2ca?w=1920&h=1080&fit=crop&q=80'],
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
            ['name' => 'Annual Gathering 2024',       'type' => 'gallery',  'description' => 'Photos from our biggest community event of the year.',                          'sort_order' => 1, 'created_by' => $adminId, 'cover_image' => 'https://images.unsplash.com/photo-1511632765486-a01980e01a18?w=800&h=600&fit=crop&q=80'],
            ['name' => 'Holiday Celebration 2024',    'type' => 'gallery',  'description' => 'Memories from our seasonal celebration with members and families.',             'sort_order' => 2, 'created_by' => $adminId, 'cover_image' => 'https://images.unsplash.com/photo-1543269865-cbf427effbad?w=800&h=600&fit=crop&q=80'],
            ['name' => 'Youth Camp 2024',             'type' => 'gallery',  'description' => 'Five days of activities, learning, and friendship at our annual youth camp.',  'sort_order' => 3, 'created_by' => $adminId, 'cover_image' => 'https://images.unsplash.com/photo-1529390079861-591de354faf5?w=800&h=600&fit=crop&q=80'],
            ['name' => 'Founders Day Dinner',         'type' => 'gallery',  'description' => 'Celebrating our founding anniversary with a special dinner and programme.',     'sort_order' => 4, 'created_by' => $adminId, 'cover_image' => 'https://images.unsplash.com/photo-1530103862676-de8c9debad1d?w=800&h=600&fit=crop&q=80'],
            ['name' => 'Community Clean-Up Day',      'type' => 'programs', 'description' => 'Volunteers came together to clean local public spaces and parks.',              'sort_order' => 1, 'created_by' => $adminId, 'cover_image' => 'https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?w=800&h=600&fit=crop&q=80'],
            ['name' => 'Back-to-School Supply Drive', 'type' => 'programs', 'description' => 'Collecting and distributing school supplies to local families.',                'sort_order' => 2, 'created_by' => $adminId, 'cover_image' => 'https://images.unsplash.com/photo-1497486751825-1233686d5d80?w=800&h=600&fit=crop&q=80'],
            ['name' => 'Food Bank Outreach',          'type' => 'programs', 'description' => 'Partnering with local food banks to support neighbours in need.',               'sort_order' => 3, 'created_by' => $adminId, 'cover_image' => 'https://images.unsplash.com/photo-1593113598332-cd288d649433?w=800&h=600&fit=crop&q=80'],
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
             'photo'             => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=400&h=400&fit=crop&q=80',
             'featured'          => true, 'active' => true, 'sort_order' => 1],
            ['name' => 'Dr. Marcus Wei',
             'achievement_title' => 'First Educational Programme Director',
             'description'       => 'Dr. Wei designed our first educational outreach programme, which now serves over 200 students each year through scholarships and mentoring.',
             'year'              => 2012,
             'photo'             => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400&h=400&fit=crop&q=80',
             'featured'          => true, 'active' => true, 'sort_order' => 2],
            ['name' => 'Amara Okafor',
             'achievement_title' => 'Lifetime Service Award',
             'description'       => 'Over twenty years of volunteering, Amara has led food drives, mentored youth, and organised community events that have touched thousands of lives.',
             'year'              => 2023,
             'photo'             => 'https://images.unsplash.com/photo-1531123897727-8f129e1688ce?w=400&h=400&fit=crop&q=80',
             'featured'          => true, 'active' => true, 'sort_order' => 3],
            ['name' => 'James Sullivan',
             'achievement_title' => 'Youth Programme Pioneer',
             'description'       => 'James launched the first youth leadership programme, which has graduated more than 150 young leaders into roles across the community.',
             'year'              => 2015,
             'photo'             => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=400&h=400&fit=crop&q=80',
             'featured'          => false, 'active' => true, 'sort_order' => 4],
            ['name' => 'Priya Sharma',
             'achievement_title' => 'Community Impact Award',
             'description'       => 'Priya led the community garden initiative, transforming an abandoned lot into a thriving space that feeds families and brings neighbours together.',
             'year'              => 2020,
             'photo'             => 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=400&h=400&fit=crop&q=80',
             'featured'          => false, 'active' => true, 'sort_order' => 5],
        ];
        foreach ($entries as $f) {
            HallOfFame::firstOrCreate(['name' => $f['name']], $f);
        }

        // Gallery items — populate every folder with 6 photos
        $folderPhotos = [
            'Annual Gathering 2024' => [
                'https://images.unsplash.com/photo-1511632765486-a01980e01a18?w=800&h=600&fit=crop&q=80',
                'https://images.unsplash.com/photo-1556761175-5973dc0f32e7?w=800&h=600&fit=crop&q=80',
                'https://images.unsplash.com/photo-1492684223066-81342ee5ff30?w=800&h=600&fit=crop&q=80',
                'https://images.unsplash.com/photo-1505373877841-8d25f7d46678?w=800&h=600&fit=crop&q=80',
                'https://images.unsplash.com/photo-1528605248644-14dd04022da1?w=800&h=600&fit=crop&q=80',
                'https://images.unsplash.com/photo-1517457373958-b7bdd4587205?w=800&h=600&fit=crop&q=80',
            ],
            'Holiday Celebration 2024' => [
                'https://images.unsplash.com/photo-1543269865-cbf427effbad?w=800&h=600&fit=crop&q=80',
                'https://images.unsplash.com/photo-1482275548304-a58859dc31b7?w=800&h=600&fit=crop&q=80',
                'https://images.unsplash.com/photo-1530103862676-de8c9debad1d?w=800&h=600&fit=crop&q=80',
                'https://images.unsplash.com/photo-1485872299712-c2dec6acc4d2?w=800&h=600&fit=crop&q=80',
                'https://images.unsplash.com/photo-1607344645866-009c320b63e0?w=800&h=600&fit=crop&q=80',
                'https://images.unsplash.com/photo-1543007630-9710e4a00a20?w=800&h=600&fit=crop&q=80',
            ],
            'Youth Camp 2024' => [
                'https://images.unsplash.com/photo-1529390079861-591de354faf5?w=800&h=600&fit=crop&q=80',
                'https://images.unsplash.com/photo-1502086223501-7ea6ecd79368?w=800&h=600&fit=crop&q=80',
                'https://images.unsplash.com/photo-1496080174650-637e3f22fa03?w=800&h=600&fit=crop&q=80',
                'https://images.unsplash.com/photo-1571260899304-425eee4c7efc?w=800&h=600&fit=crop&q=80',
                'https://images.unsplash.com/photo-1517486808906-6ca8b3f04846?w=800&h=600&fit=crop&q=80',
                'https://images.unsplash.com/photo-1503676260728-1c00da094a0b?w=800&h=600&fit=crop&q=80',
            ],
            'Founders Day Dinner' => [
                'https://images.unsplash.com/photo-1530103862676-de8c9debad1d?w=800&h=600&fit=crop&q=80',
                'https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=800&h=600&fit=crop&q=80',
                'https://images.unsplash.com/photo-1555244162-803834f70033?w=800&h=600&fit=crop&q=80',
                'https://images.unsplash.com/photo-1467810563316-b5476525c0f9?w=800&h=600&fit=crop&q=80',
                'https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=800&h=600&fit=crop&q=80',
                'https://images.unsplash.com/photo-1559329007-40df8a9345d8?w=800&h=600&fit=crop&q=80',
            ],
            'Community Clean-Up Day' => [
                'https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?w=800&h=600&fit=crop&q=80',
                'https://images.unsplash.com/photo-1542601906990-b4d3fb778b09?w=800&h=600&fit=crop&q=80',
                'https://images.unsplash.com/photo-1532629345422-7515f3d16bb6?w=800&h=600&fit=crop&q=80',
                'https://images.unsplash.com/photo-1607344645866-009c320b63e0?w=800&h=600&fit=crop&q=80',
                'https://images.unsplash.com/photo-1593113598332-cd288d649433?w=800&h=600&fit=crop&q=80',
                'https://images.unsplash.com/photo-1469571486292-0ba58a3f068b?w=800&h=600&fit=crop&q=80',
            ],
            'Back-to-School Supply Drive' => [
                'https://images.unsplash.com/photo-1497486751825-1233686d5d80?w=800&h=600&fit=crop&q=80',
                'https://images.unsplash.com/photo-1503676260728-1c00da094a0b?w=800&h=600&fit=crop&q=80',
                'https://images.unsplash.com/photo-1456513080510-7bf3a84b82f8?w=800&h=600&fit=crop&q=80',
                'https://images.unsplash.com/photo-1532619675605-1ede6c2ed2b0?w=800&h=600&fit=crop&q=80',
                'https://images.unsplash.com/photo-1503676593-a52119bf66f3?w=800&h=600&fit=crop&q=80',
                'https://images.unsplash.com/photo-1497633762265-9d179a990aa6?w=800&h=600&fit=crop&q=80',
            ],
            'Food Bank Outreach' => [
                'https://images.unsplash.com/photo-1593113598332-cd288d649433?w=800&h=600&fit=crop&q=80',
                'https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?w=800&h=600&fit=crop&q=80',
                'https://images.unsplash.com/photo-1607344645866-009c320b63e0?w=800&h=600&fit=crop&q=80',
                'https://images.unsplash.com/photo-1577563908411-5077b6dc7624?w=800&h=600&fit=crop&q=80',
                'https://images.unsplash.com/photo-1546552768-9e3a94b38a59?w=800&h=600&fit=crop&q=80',
                'https://images.unsplash.com/photo-1469571486292-0ba58a3f068b?w=800&h=600&fit=crop&q=80',
            ],
        ];
        foreach ($folderPhotos as $folderName => $photos) {
            $folder = GalleryFolder::where('name', $folderName)->first();
            if (!$folder) continue;

            {
                foreach ($photos as $i => $url) {
                    GalleryItem::firstOrCreate(
                        ['title' => $folderName . ' — Photo ' . ($i + 1)],
                        [
                            'gallery_folder_id' => $folder->id,
                            'title'             => $folderName . ' — Photo ' . ($i + 1),
                            'caption'           => 'Moment from ' . $folderName,
                            'path'              => $url,
                            'uploaded_by'       => $adminId,
                        ]
                    );
                }
            }
        }

        // Add photos to members
        $memberPhotos = [
            'Alex Carter'     => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400&h=400&fit=crop&q=80',
            'Sam Rivera'      => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=400&h=400&fit=crop&q=80',
            'Jordan Lee'      => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=400&h=400&fit=crop&q=80',
            'Taylor Morgan'   => 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=400&h=400&fit=crop&q=80',
            'Casey Nguyen'    => 'https://images.unsplash.com/photo-1531123897727-8f129e1688ce?w=400&h=400&fit=crop&q=80',
            'Robin Patel'     => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=400&h=400&fit=crop&q=80',
            'Drew Kim'        => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=400&h=400&fit=crop&q=80',
            'Avery Johnson'   => 'https://images.unsplash.com/photo-1487412720507-e7ab37603c6f?w=400&h=400&fit=crop&q=80',
        ];
        foreach ($memberPhotos as $name => $url) {
            Student::where('name', $name)->update(['photo' => $url]);
        }
    }
}
