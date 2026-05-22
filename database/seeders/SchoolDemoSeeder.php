<?php

namespace Database\Seeders;

use App\Models\Blog;
use App\Models\Download;
use App\Models\Event;
use App\Models\GalleryFolder;
use App\Models\GalleryItem;
use App\Models\HallOfFame;
use App\Models\ImportantLink;
use App\Models\Member;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * School-specific demo data for J.N. Nazareth English School.
 * Idempotent — safe to run more than once.
 *
 *   php artisan db:seed --class=SchoolDemoSeeder
 */
class SchoolDemoSeeder extends Seeder
{
    public function run(): void
    {
        $adminId = User::where('email', 'admin@demo.com')->value('id')
                 ?? User::min('id') ?? 1;

        $this->students();
        $this->teachers();
        $this->downloads($adminId);
        $this->importantLinks();
        $this->galleries($adminId);
        $this->hallOfFame();
        $this->newsAndEvents($adminId);
        $this->aboutPeople();
        $this->certificates();
        $this->curriculum();
        $this->calendar();

        $this->command?->info('School demo data seeded.');
    }

    /* ── Students ─────────────────────────────────────────── */
    private function students(): void
    {
        $first = ['Aman', 'Mary', 'David', 'Priya', 'Joseph', 'Esther', 'Daniel', 'Grace',
                  'Samuel', 'Ruth', 'John', 'Hoineng', 'Lalrin', 'Thangboi', 'Nengnei',
                  'Seiboi', 'Chingboi', 'Lhing', 'Paul', 'Rebecca', 'Mangboi', 'Niang',
                  'Kimboi', 'Hat', 'Vung', 'Cing', 'Gin', 'Lun', 'Mun', 'Don', 'Sian', 'Tual'];
        $last  = ['Singh', 'Haokip', 'Lalrin', 'Kipgen', 'Devi', 'Doungel', 'Touthang',
                  'Guite', 'Vaiphei', 'Hangsing', 'Lhungdim', 'Chongloi'];
        $fathers = ['Rajesh', 'John', 'Thang', 'Suresh', 'Paul', 'Mang', 'Sei', 'Lal', 'Hoi', 'Gin'];

        $plan = [
            'Preparatory' => ['A'], 'LKG' => ['A'], 'UKG' => ['A'],
            'Class I' => ['A', 'B'], 'Class II' => ['A', 'B'], 'Class III' => ['A', 'B'],
            'Class IV' => ['A'], 'Class V' => ['A'], 'Class VI' => ['A'],
            'Class VII' => ['A'], 'Class VIII' => ['A'], 'Class IX' => ['A'], 'Class X' => ['A'],
        ];

        $i = 0;
        foreach ($plan as $class => $sections) {
            foreach ($sections as $section) {
                for ($roll = 1; $roll <= 3; $roll++) {
                    $i++;
                    $name = $first[$i % count($first)] . ' ' . $last[$i % count($last)];
                    Member::firstOrCreate(
                        ['name' => $name, 'class' => $class, 'section' => $section, 'roll_number' => (string) $roll],
                        [
                            'academic_year' => '2025-26',
                            'father_name'   => $fathers[$i % count($fathers)] . ' ' . $last[$i % count($last)],
                            'mother_name'   => 'Mrs. ' . $last[$i % count($last)],
                            'parent_phone'  => '98' . str_pad((string) (60000000 + $i * 137), 8, '0', STR_PAD_LEFT),
                            'address'       => 'Khengjang, Churachandpur, Manipur',
                            'date_of_birth' => now()->subYears(rand(4, 16))->subDays(rand(0, 360))->toDateString(),
                            'admission_date'=> now()->subYears(rand(0, 4))->toDateString(),
                            'role'          => 'Student',
                            'status'        => 'active',
                            'is_active'     => true,
                        ]
                    );
                }
            }
        }
    }

    /* ── Teachers ─────────────────────────────────────────── */
    private function teachers(): void
    {
        $rows = [
            ['Mr. T. Lhungdim',     'Principal',            'Administration',          ['Class IX', 'Class X']],
            ['Mrs. Esther Haokip',  'Vice Principal',       'English, Social Studies', ['Class VII', 'Class VIII']],
            ['Mr. David Kipgen',    'Senior Teacher',       'Mathematics, Science',    ['Class IX', 'Class X']],
            ['Mrs. Grace Doungel',  'Teacher',              'English, Moral Science',  ['Class V', 'Class VI']],
            ['Mr. Samuel Guite',    'Teacher',              'Science, Computer',       ['Class VII', 'Class VIII']],
            ['Mrs. Ruth Vaiphei',   'Primary Teacher',      'All Subjects',            ['Class I', 'Class II', 'Class III']],
            ['Miss Niang Touthang', 'Kindergarten Teacher', 'Foundation Learning',     ['Preparatory', 'LKG', 'UKG']],
            ['Mr. Paul Chongloi',   'Sports & PT Teacher',  'Physical Education',      ['Class IV', 'Class V', 'Class VI']],
        ];
        $photos = [
            'https://images.unsplash.com/photo-1560250097-0b93528c311a?w=400&h=400&fit=crop&q=80',
            'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=400&h=400&fit=crop&q=80',
            'https://images.unsplash.com/photo-1556157382-97eda2d62296?w=400&h=400&fit=crop&q=80',
            'https://images.unsplash.com/photo-1580489944761-15a19d654956?w=400&h=400&fit=crop&q=80',
            'https://images.unsplash.com/photo-1568602471122-7832951cc4c5?w=400&h=400&fit=crop&q=80',
            'https://images.unsplash.com/photo-1544723795-3fb6469f5b39?w=400&h=400&fit=crop&q=80',
            'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=400&h=400&fit=crop&q=80',
            'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400&h=400&fit=crop&q=80',
        ];
        foreach ($rows as $n => $r) {
            Teacher::firstOrCreate(
                ['name' => $r[0]],
                [
                    'designation' => $r[1],
                    'subjects'    => $r[2],
                    'classes'     => $r[3],
                    'phone'       => '98' . str_pad((string) (62000000 + $n * 311), 8, '0', STR_PAD_LEFT),
                    'email'       => Str::slug($r[0], '.') . '@jnnazareth.edu',
                    'photo'       => $photos[$n] ?? null,
                    'sort_order'  => $n + 1,
                    'is_active'   => true,
                ]
            );
        }
    }

    /* ── Downloads (Notices / Results / Textbooks) ────────── */
    private function downloads(int $adminId): void
    {
        $pdf = 'https://pdfobject.com/pdf/sample.pdf';
        $rows = [
            ['Annual Examination Date Sheet 2025-26', 'Notice'],
            ['Winter Vacation Notice',                'Notice'],
            ['Circular — Parent-Teacher Meeting',     'Circular'],
            ['Admission Form 2026-27',                'Admission Form'],
            ['Half-Yearly Result — Primary Section',  'Result'],
            ['Half-Yearly Result — High School',      'Result'],
            ['Prescribed Textbook List — Class I-V',  'Textbook'],
            ['Prescribed Textbook List — Class VI-X', 'Textbook'],
            ['Syllabus Overview 2025-26',             'Syllabus'],
        ];
        foreach ($rows as $n => $r) {
            Download::firstOrCreate(
                ['title' => $r[0]],
                [
                    'category'     => $r[1],
                    'file_path'    => $pdf,
                    'file_type'    => 'pdf',
                    'file_size'    => round(rand(180, 2400) / 1000, 1) . ' MB',
                    'is_published' => true,
                    'sort_order'   => $n + 1,
                    'uploaded_by'  => $adminId,
                ]
            );
        }
    }

    /* ── Important Links ──────────────────────────────────── */
    private function importantLinks(): void
    {
        $rows = [
            ['Board of Secondary Education, Manipur', 'https://bsem.nic.in', 'Official board website for Class 10 examinations.'],
            ['Council of Higher Secondary Education', 'https://cohsem.nic.in', 'Higher secondary education board of Manipur.'],
            ['Department of Education (Schools), Manipur', 'https://manipureducation.gov.in', 'State school education department.'],
            ['DIKSHA — National Learning Platform', 'https://diksha.gov.in', 'Free digital learning content for students and teachers.'],
            ['Scholarships Portal (NSP)', 'https://scholarships.gov.in', 'National Scholarship Portal for eligible students.'],
            ['CBSE Academic Resources', 'https://cbseacademic.nic.in', 'Reference academic resources and model papers.'],
        ];
        foreach ($rows as $n => $r) {
            ImportantLink::firstOrCreate(
                ['title' => $r[0]],
                ['url' => $r[1], 'description' => $r[2], 'is_published' => true, 'sort_order' => $n + 1]
            );
        }
    }

    /* ── Photo Albums + images ────────────────────────────── */
    private function galleries(int $adminId): void
    {
        // Rename any old community-themed folders to school albums.
        $rename = [
            'Annual Gathering 2024'       => 'Annual Day Function 2025',
            'Holiday Celebration 2024'    => 'Christmas Celebration 2024',
            'Youth Camp 2024'             => 'Annual Sports Day 2025',
            'Founders Day Dinner'         => 'Independence Day 2024',
            'Community Clean-Up Day'      => 'Science Exhibition 2025',
            'Back-to-School Supply Drive' => "Children's Day Celebration",
            'Food Bank Outreach'          => 'Educational Field Trip',
        ];
        foreach ($rename as $old => $new) {
            GalleryFolder::where('name', $old)->update(['name' => $new, 'type' => 'programs']);
        }

        $albums = [
            'Annual Day Function 2025'   => 'Cultural performances, prize distribution and the yearly celebration.',
            'Christmas Celebration 2024' => 'Carols, nativity and festive joy across the school.',
            'Annual Sports Day 2025'     => 'Track events, races and team spirit on the school ground.',
            'Independence Day 2024'      => 'Flag hoisting, march-past and patriotic programmes.',
            'Science Exhibition 2025'    => 'Student projects, models and working experiments.',
            "Children's Day Celebration" => 'Games, fun activities and treats for every student.',
            'Educational Field Trip'     => 'Learning beyond the classroom on our annual study tour.',
        ];

        $pool = [
            'https://images.unsplash.com/photo-1503676260728-1c00da094a0b?w=900&h=600&fit=crop&q=80',
            'https://images.unsplash.com/photo-1577896851231-70ef18881754?w=900&h=600&fit=crop&q=80',
            'https://images.unsplash.com/photo-1427504494785-3a9ca7044f45?w=900&h=600&fit=crop&q=80',
            'https://images.unsplash.com/photo-1546410531-bb4caa6b424d?w=900&h=600&fit=crop&q=80',
            'https://images.unsplash.com/photo-1509062522246-3755977927d7?w=900&h=600&fit=crop&q=80',
            'https://images.unsplash.com/photo-1571260899304-425eee4c7efc?w=900&h=600&fit=crop&q=80',
            'https://images.unsplash.com/photo-1588072432836-e10032774350?w=900&h=600&fit=crop&q=80',
            'https://images.unsplash.com/photo-1564981797816-1043664bf78d?w=900&h=600&fit=crop&q=80',
            'https://images.unsplash.com/photo-1497486751825-1233686d5d80?w=900&h=600&fit=crop&q=80',
            'https://images.unsplash.com/photo-1610484826967-09c5720778c7?w=900&h=600&fit=crop&q=80',
        ];

        $sort = 1;
        foreach ($albums as $name => $desc) {
            $folder = GalleryFolder::firstOrCreate(
                ['name' => $name],
                [
                    'type'        => 'programs',
                    'description' => $desc,
                    'sort_order'  => $sort++,
                    'created_by'  => $adminId,
                    'cover_image' => $pool[($sort * 2) % count($pool)],
                ]
            );
            if ($folder->items()->count() > 0) {
                continue;
            }
            for ($p = 0; $p < 6; $p++) {
                $url = $pool[($sort + $p) % count($pool)];
                GalleryItem::create([
                    'gallery_folder_id' => $folder->id,
                    'title'             => $name . ' — Photo ' . ($p + 1),
                    'caption'           => $name,
                    'path'              => $url,
                    'uploaded_by'       => $adminId,
                ]);
            }
            if (! $folder->cover_image) {
                $folder->update(['cover_image' => $pool[$sort % count($pool)]]);
            }
        }
    }

    /* ── Hall of Fame ─────────────────────────────────────── */
    private function hallOfFame(): void
    {
        $rows = [
            ['Lalboi Haokip',   'Class X Topper — 95.2%',          2025, true],
            ['Esther Kipgen',   'State-Level Athletics Gold',      2025, true],
            ['Thangboi Guite',  'District Science Quiz Winner',    2024, true],
            ['Niangboi Doungel','Best All-Rounder Student',        2024, true],
            ['David Singh',     'Inter-School Essay 1st Prize',    2024, false],
            ['Grace Vaiphei',   '100% Attendance — 3 Years',       2023, false],
        ];
        $photos = [
            'https://images.unsplash.com/photo-1503454537195-1dcabb73ffb9?w=500&h=500&fit=crop&q=80',
            'https://images.unsplash.com/photo-1521146764736-56c929d59c83?w=500&h=500&fit=crop&q=80',
            'https://images.unsplash.com/photo-1531891437562-4301cf35b7e4?w=500&h=500&fit=crop&q=80',
            'https://images.unsplash.com/photo-1544717297-fa95b6ee9643?w=500&h=500&fit=crop&q=80',
            'https://images.unsplash.com/photo-1522529599102-193c0d76b5b6?w=500&h=500&fit=crop&q=80',
            'https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?w=500&h=500&fit=crop&q=80',
        ];
        foreach ($rows as $n => $r) {
            HallOfFame::firstOrCreate(
                ['name' => $r[0], 'achievement_title' => $r[1]],
                [
                    'description' => $r[0] . ' brought pride to the school through outstanding dedication and performance.',
                    'year'        => $r[2],
                    'photo'       => $photos[$n],
                    'featured'    => $r[3],
                    'active'      => true,
                    'sort_order'  => $n + 1,
                ]
            );
        }
    }

    /* ── News / Notices / Events ──────────────────────────── */
    private function newsAndEvents(int $adminId): void
    {
        $posts = [
            ['Class X Board Results — 100% Pass', 'news',
             'We are proud to announce that every student of our High School cleared the Board of Secondary Education, Manipur examination this year, with several scoring distinction.',
             'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?w=1200&h=675&fit=crop&q=80'],
            ['Admissions Open for Session 2026-27', 'announcement',
             'Admissions are now open for classes Preparatory to Class X. Interested parents may visit the school office during working hours or enquire on WhatsApp.',
             'https://images.unsplash.com/photo-1503676260728-1c00da094a0b?w=1200&h=675&fit=crop&q=80'],
            ['Annual Sports Day — A Grand Success', 'news',
             'Our Annual Sports Day saw enthusiastic participation from every class with track events, races and team games celebrating fitness and sportsmanship.',
             'https://images.unsplash.com/photo-1461896836934-ffe607ba8211?w=1200&h=675&fit=crop&q=80'],
            ['Winter Vacation Notice', 'notice',
             'The school will remain closed for winter vacation. Classes will resume as per the academic calendar. Holiday assignments have been shared with students.',
             'https://images.unsplash.com/photo-1491002052546-bf38f186af56?w=1200&h=675&fit=crop&q=80'],
        ];
        foreach ($posts as $i => $p) {
            $slug = Str::slug($p[0]);
            Blog::firstOrCreate(['slug' => $slug], [
                'title'        => $p[0],
                'post_type'    => $p[1],
                'content'      => '<p>' . $p[2] . '</p>',
                'image'        => $p[3],
                'published'    => true,
                'is_featured'  => $i === 0,
                'published_at' => now()->subDays(($i + 1) * 5),
                'author_id'    => $adminId,
            ]);
        }

        $events = [
            ['Parent-Teacher Meeting', 'School Assembly Hall', 7,
             'Parents are requested to attend the term parent-teacher meeting to discuss student progress.'],
            ['Annual Day Function 2026', 'School Ground', 30,
             'A grand evening of cultural performances, prize distribution and celebration of the school year.'],
            ['Half-Yearly Examinations Begin', 'All Classrooms', 21,
             'Half-yearly examinations for all classes. The detailed date sheet is available in Downloads.'],
            ['Independence Day Celebration', 'School Ground', 45,
             'Flag hoisting, march-past and patriotic programmes. All students and parents are welcome.'],
        ];
        foreach ($events as $e) {
            Event::firstOrCreate(['title' => $e[0]], [
                'description' => $e[3],
                'location'    => $e[1],
                'image'       => 'https://images.unsplash.com/photo-1577896851231-70ef18881754?w=1200&h=675&fit=crop&q=80',
                'starts_at'   => now()->addDays($e[2])->setTime(10, 0),
                'created_by'  => $adminId,
            ]);
        }
    }

    /* ── About Us — Administration / SMC / PTA ────────────── */
    private function aboutPeople(): void
    {
        $admin = [
            ['Mr. T. Lhungdim',    'Principal'],
            ['Mrs. Esther Haokip', 'Vice Principal'],
            ['Mr. David Kipgen',   'Academic Coordinator'],
            ['Mrs. Ruth Vaiphei',  'Headmistress (Primary)'],
            ['Mr. Samuel Guite',   'Examination In-Charge'],
        ];
        $smc = [
            ['Rev. K. Thang',     'Chairman'],
            ['Mr. L. Doungel',    'Secretary'],
            ['Mrs. N. Kipgen',    'Member'],
            ['Mr. P. Haokip',     'Member'],
            ['Mr. S. Guite',      'Member'],
            ['Mrs. G. Vaiphei',   'Member'],
        ];
        $pta = [
            ['Mr. James Singh',   'President'],
            ['Mrs. Mary Doungel', 'Vice President'],
            ['Mr. John Touthang', 'Secretary'],
            ['Mrs. Grace Kipgen', 'Treasurer'],
            ['Mr. Paul Haokip',   'Member'],
            ['Mrs. Ruth Guite',   'Member'],
        ];

        $pack = fn (array $rows) => array_map(
            fn ($r) => ['name' => $r[0], 'role' => $r[1], 'photo' => ''],
            $rows
        );

        $this->setting('about_administration_members', json_encode($pack($admin)));
        $this->setting('about_smc_members', json_encode($pack($smc)));
        $this->setting('about_pta_members', json_encode($pack($pta)));
    }

    /* ── Certificates ─────────────────────────────────────── */
    private function certificates(): void
    {
        $img = [
            'https://images.unsplash.com/photo-1606326608606-aa0b62935f2b?w=900&h=1200&fit=crop&q=80',
            'https://images.unsplash.com/photo-1568667256549-094345857637?w=900&h=1200&fit=crop&q=80',
            'https://images.unsplash.com/photo-1614332287897-cdc485fa562d?w=900&h=1200&fit=crop&q=80',
            'https://images.unsplash.com/photo-1622372738946-62e02505feb3?w=900&h=1200&fit=crop&q=80',
        ];
        $certs = [
            ['title' => 'School Recognition Certificate', 'file' => $img[0], 'active' => true],
            ['title' => 'Fire Safety Certificate',        'file' => $img[1], 'active' => true],
            ['title' => 'Building Safety Certificate',    'file' => $img[2], 'active' => true],
            ['title' => 'Water & Sanitation Report',      'file' => $img[3], 'active' => true],
        ];
        $this->setting('about_certs_list', json_encode($certs));
    }

    /* ── Curriculum cards ─────────────────────────────────── */
    private function curriculum(): void
    {
        $items = [
            ['icon' => 'fas fa-language',       'title' => 'English',          'desc' => 'Reading, writing, grammar and spoken-English skills built from the foundation years.'],
            ['icon' => 'fas fa-calculator',     'title' => 'Mathematics',      'desc' => 'Concept-based learning from basic numeracy to board-level mathematics.'],
            ['icon' => 'fas fa-flask',          'title' => 'Science',          'desc' => 'Physics, Chemistry and Biology with practical, activity-based teaching.'],
            ['icon' => 'fas fa-globe-asia',     'title' => 'Social Studies',   'desc' => 'History, Geography and Civics that build awareness of the world.'],
            ['icon' => 'fas fa-laptop-code',    'title' => 'Computer Science', 'desc' => 'Digital literacy and basic computing introduced from the primary level.'],
            ['icon' => 'fas fa-book-open',      'title' => 'Moral Science',    'desc' => 'Values, ethics and character formation rooted in the school motto.'],
            ['icon' => 'fas fa-palette',        'title' => 'Arts & Craft',     'desc' => 'Drawing, craft and creativity to nurture imagination and expression.'],
            ['icon' => 'fas fa-person-running', 'title' => 'Physical Education', 'desc' => 'Sports, games and fitness for healthy, well-rounded students.'],
        ];
        $this->setting('acad_curriculum_items', json_encode($items));
    }

    /* ── Academic calendar images ─────────────────────────── */
    private function calendar(): void
    {
        $images = [
            ['file' => 'https://images.unsplash.com/photo-1506784983877-45594efa4cbe?w=1000&h=1400&fit=crop&q=80', 'caption' => 'Term 1 — April to September 2025'],
            ['file' => 'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?w=1000&h=1400&fit=crop&q=80', 'caption' => 'Term 2 — October 2025 to March 2026'],
            ['file' => 'https://images.unsplash.com/photo-1517842645767-c639042777db?w=1000&h=1400&fit=crop&q=80', 'caption' => 'Examination & Holiday Schedule'],
        ];
        $this->setting('acad_calendar_images', json_encode($images));
    }

    /* ── Helper: write a site setting (clears the settings cache) ── */
    private function setting(string $key, string $value): void
    {
        \App\Helpers\Settings::set($key, $value);
    }
}
