<?php

namespace Database\Seeders;

use App\Helpers\Settings;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class SchoolSettingsSeeder extends Seeder
{
    public function run(): void
    {
        // Roles — admin = super admin, staff = editor
        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'staff']);

        // School super-admin account
        $admin = User::firstOrCreate(
            ['email' => 'admin@jnnazareth.edu.in'],
            ['name' => 'Admin', 'password' => Hash::make('changeme123')]
        );
        $admin->syncRoles('admin');

        Settings::setMany([
            // ── General ──────────────────────────────────────────────
            'site_name'         => 'J.N. Nazareth English School',
            'school_name'       => 'J.N. Nazareth English School',
            'school_tagline'    => 'Quality Education for a Better Future',
            'site_tagline'      => 'Quality Education for a Better Future',
            'school_established'=> '1996',
            'school_board'      => 'Manipur Board',
            'school_classes'    => 'Preparatory to Class X',
            'school_logo'       => '',
            'logo'              => '',

            // ── Contact ──────────────────────────────────────────────
            'contact_address'   => 'Khengjang, B.P.O. Koite, Churachandpur – 795128, Manipur',
            'address_short'     => 'Khengjang, Koite, Churachandpur, Manipur',
            'contact_phone'     => '+91 98628 80292',
            'contact_email'     => 'info@jnnazareth.edu.in',
            'whatsapp'          => '919862880292',
            'social_whatsapp'   => '919862880292',
            'map_embed_url'     => '',
            'map_directions_url'=> '',

            // ── Social ───────────────────────────────────────────────
            'social_facebook'  => '',
            'social_youtube'   => '',
            'social_instagram' => '',
            'social_telegram'  => '',

            // ── Homepage ─────────────────────────────────────────────
            'hero_title'       => 'Quality Education for a Better Future',
            'hero_subtitle'    => 'A government-recognised English-medium school nurturing students from Preparatory to Class X in Churachandpur, Manipur.',
            'hero_tagline'     => 'Quality Education for a Better Future',
            'hero_image'       => '',
            'principal_name'   => 'Ngamboi Kipgen',
            'principal_photo'  => '',
            'principal_message'=> 'It is my privilege to welcome you to J.N. Nazareth English School. Since 1996 we have been committed to providing quality, value-based education that prepares every child for a brighter future. We believe in nurturing not only academic excellence but also character, discipline, and confidence.',
            'ticker_text'      => 'Admissions Open for the New Session — Enquire Now',
            'admission_open'   => '1',
            'stats_students'   => '500+',
            'stats_teachers'   => '25+',
            'stats_classes'    => '12',
            'stats_years'      => '30+',
            'stats_board'      => 'Manipur Board',

            // ── Academics ────────────────────────────────────────────
            'fee_structure_text'     => 'Fee details are available at the school office. Please contact us for the current fee structure.',
            'school_timing_weekday'  => '8:00 AM – 3:00 PM',
            'school_timing_saturday' => '8:00 AM – 12:00 PM',
            'academic_year'          => '2026–2027',
        ]);
    }
}
