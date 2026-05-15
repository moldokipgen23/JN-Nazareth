<?php

namespace Database\Seeders;

use App\Models\Blog;
use App\Models\Event;
use App\Models\Member;
use App\Models\Page;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Roles
        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'staff']);
        Role::firstOrCreate(['name' => 'viewer']);

        // Admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@demo.com'],
            ['name' => 'Admin', 'password' => Hash::make('password')]
        );
        $admin->syncRoles('admin');

        // Staff user
        $staff = User::firstOrCreate(
            ['email' => 'staff@demo.com'],
            ['name' => 'Staff Member', 'password' => Hash::make('password')]
        );
        $staff->syncRoles('staff');

        // Demo pages
        $pages = [
            ['slug' => 'home',
             'title' => 'Welcome to Our Community',
             'content' => '<p>We are a community built on connection, service, and shared purpose. Everyone is welcome — explore our site to learn more about what we do.</p>',
             'updated_by' => $admin->id],
            ['slug' => 'about',
             'title' => 'About Us',
             'content' => '<p>Our organisation exists to bring people together, support members, and serve the wider community.</p><h2>Our Vision</h2><p>To be a place where everyone feels welcome, valued, and able to contribute.</p><h2>Our Mission</h2><p>To run meaningful programmes, host inclusive events, and create space for shared experiences.</p>',
             'updated_by' => $admin->id],
        ];
        foreach ($pages as $p) Page::firstOrCreate(['slug' => $p['slug']], $p);

        // Demo members (generic roles that fit any vertical)
        $members = [
            ['name' => 'Alex Carter',     'role' => 'Director',       'phone' => '+1 555 000 0001', 'email' => 'director@demo.com'],
            ['name' => 'Sam Rivera',      'role' => 'Coordinator',    'phone' => '+1 555 000 0002', 'email' => 'coord@demo.com'],
            ['name' => 'Jordan Lee',      'role' => 'Volunteer Lead', 'phone' => '+1 555 000 0003', 'email' => 'vol@demo.com'],
            ['name' => 'Taylor Morgan',   'role' => 'Member',         'phone' => '+1 555 000 0004', 'email' => null],
            ['name' => 'Casey Nguyen',    'role' => 'Member',         'phone' => '+1 555 000 0005', 'email' => null],
            ['name' => 'Robin Patel',     'role' => 'Member',         'phone' => '+1 555 000 0006', 'email' => 'robin@demo.com'],
            ['name' => 'Drew Kim',        'role' => 'Member',         'phone' => null,              'email' => 'drew@demo.com'],
            ['name' => 'Avery Johnson',   'role' => 'Member',         'phone' => '+1 555 000 0007', 'email' => null],
        ];
        foreach ($members as $m) {
            Member::firstOrCreate(['name' => $m['name']], array_merge($m, [
                'address'    => '123 Main Street, Your City',
                'created_at' => now()->subDays(rand(1, 180)),
            ]));
        }

        // Demo blog posts (mixed multi-vertical content)
        $blogs = [
            ['title'        => 'Welcome to Our New Website',
             'content'      => '<p>We\'re thrilled to launch our refreshed website — a single home for news, events, photos, and updates from our community. Have a look around and tell us what you think.</p>',
             'published'    => true,
             'published_at' => now()->subDays(2)],
            ['title'        => 'Annual Gathering Recap',
             'content'      => '<p>Our annual gathering was a great success this year. More than 200 members joined us for a day of activities, talks, food, and fellowship. Thank you to everyone who came and helped make it happen.</p>',
             'published'    => true,
             'published_at' => now()->subDays(10)],
            ['title'        => 'Community Clean-Up Initiative',
             'content'      => '<p>This month we partnered with local residents and businesses to clean and beautify the neighbourhood. Over 50 volunteers picked up litter, painted fences, and planted flowers across three parks.</p>',
             'published'    => true,
             'published_at' => now()->subDays(18)],
            ['title'        => 'New Youth Programme Now Open',
             'content'      => '<p>Registration is now open for our new youth programme. Sessions run weekly and include skill-building, mentorship, and group activities. Spaces are limited.</p>',
             'published'    => true,
             'published_at' => now()->subDays(25)],
            ['title'        => 'Volunteer Appreciation Update',
             'content'      => '<p>None of what we do is possible without our volunteers. This quarter alone, 78 volunteers contributed more than 600 hours of service. We are deeply grateful.</p>',
             'published'    => true,
             'published_at' => now()->subDays(40)],
        ];
        foreach ($blogs as $b) {
            $slug = Str::slug($b['title']);
            Blog::firstOrCreate(['slug' => $slug], array_merge($b, [
                'slug'      => $slug,
                'author_id' => $admin->id,
            ]));
        }

        // Demo events
        $events = [
            ['title'       => 'Weekly Community Meet-Up',
             'location'    => 'Main Hall',
             'starts_at'   => now()->next('Sunday')->setTime(10, 0),
             'description' => 'Our regular weekly meet-up — open to all members and guests. Refreshments provided.'],
            ['title'       => 'Youth Activity Night',
             'location'    => 'Youth Centre',
             'starts_at'   => now()->next('Friday')->setTime(18, 0),
             'description' => 'Weekly activity night for ages 13–25. Games, discussion, and snacks.'],
            ['title'       => 'Founders Day Dinner',
             'location'    => 'Community Centre',
             'starts_at'   => now()->addDays(14)->setTime(18, 30),
             'description' => 'Celebrate our founding anniversary with dinner, speeches, and a look back at our journey.'],
            ['title'       => 'Volunteer Training Workshop',
             'location'    => 'Main Hall',
             'starts_at'   => now()->addDays(21)->setTime(9, 0),
             'description' => 'A morning of training for new and returning volunteers. Refreshments provided.'],
            ['title'       => 'Neighbourhood Service Day',
             'location'    => 'Central Park',
             'starts_at'   => now()->addDays(28)->setTime(8, 0),
             'description' => 'Join us as we serve the wider community — clean-up, food distribution, and outreach.'],
        ];
        foreach ($events as $e) {
            Event::firstOrCreate(['title' => $e['title']], array_merge($e, [
                'created_by' => $admin->id,
            ]));
        }

        $this->call([
            SiteSettingsSeeder::class,
            DemoDataSeeder::class,
        ]);
    }
}
