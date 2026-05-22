<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Settings;
use App\Http\Controllers\Controller;
use App\Models\BannerSlide;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SiteCustomizerController extends Controller
{
    /** Keys that are image uploads (stored on the public disk). */
    private array $imageKeys = [
        'school_logo', 'hero_image', 'about_emblem', 'about_image', 'principal_photo',
        'favicon', 'seo_og_image',
    ];

    /** URL slugs that may not be used as a login path (would clash with real routes). */
    private array $reservedPaths = [
        'admin', 'api', 'login', 'logout', 'register', 'dashboard', 'profile',
        'about', 'academics', 'admission', 'gallery', 'student-life', 'news',
        'contact', 'hall-of-fame', 'storage', 'sitemap.xml', 'robots.txt',
    ];

    public function index()
    {
        $slides = BannerSlide::orderBy('sort_order')->get();
        return view('admin.customizer.index', compact('slides'));
    }

    /**
     * Single save endpoint for every customizer accordion section.
     * Accepts whatever text fields the submitted form contains, plus
     * any image uploads, and persists them via the Settings helper.
     */
    public function save(Request $request)
    {
        $data = [];

        foreach ($request->except(['_token', '_method']) as $key => $value) {
            if (is_scalar($value) || is_null($value)) {
                $data[$key] = (string) $value;
            }
        }

        foreach ($this->imageKeys as $key) {
            if ($request->hasFile($key)) {
                // Favicons may be .ico — allow it alongside images.
                $request->validate([$key => 'file|mimes:jpg,jpeg,png,webp,gif,svg,ico|max:4096']);
                if (Settings::get($key)) {
                    Storage::disk('public')->delete(Settings::get($key));
                }
                $data[$key] = $request->file($key)->store('settings', 'public');
            }
        }

        // Sanitise the configurable login paths.
        $loginPathsChanged = false;
        foreach (['admin_login_path' => 'admin-portal', 'teacher_login_path' => 'teacher-portal'] as $key => $fallback) {
            if (! array_key_exists($key, $data)) {
                continue;
            }
            $slug = strtolower(trim($data[$key], " \t\n\r/"));
            $slug = preg_replace('/[^a-z0-9\-]/', '', $slug) ?: $fallback;
            if (in_array($slug, $this->reservedPaths, true)) {
                $slug = $fallback;
            }
            $data[$key] = $slug;
            if (Settings::get($key) !== $slug) {
                $loginPathsChanged = true;
            }
        }
        // The two login paths must not be identical.
        if (isset($data['admin_login_path'], $data['teacher_login_path'])
            && $data['admin_login_path'] === $data['teacher_login_path']) {
            $data['teacher_login_path'] = 'teacher-portal';
            $loginPathsChanged = true;
        }

        if (! empty($data)) {
            Settings::setMany($data);
        }

        // Login routes are built from these settings — rebuild the route cache.
        if ($loginPathsChanged) {
            \Illuminate\Support\Facades\Artisan::call('route:clear');
        }

        return back()->with('success', 'Saved successfully.');
    }

    /** AJAX — show/hide a homepage section. */
    public function toggleSection(Request $request)
    {
        $request->validate([
            'key'   => 'required|string|max:60',
            'value' => 'required|in:0,1',
        ]);
        Settings::set('sec_show_' . $request->key, $request->value);
        return response()->json(['success' => true]);
    }

    /** AJAX — save the drag-to-reorder order of a customizer tab's sections. */
    public function reorderSections(Request $request)
    {
        $request->validate([
            'area'    => 'required|string|in:home,admission,academic,studentlife,about',
            'order'   => 'required|array',
            'order.*' => 'string|max:40',
        ]);
        Settings::set($request->area . '_section_order', json_encode(array_values($request->order)));
        return response()->json(['success' => true]);
    }

    /**
     * Save the Admission page customizer tab.
     * Handles scalar settings plus the two dynamic repeatable lists
     * (information items and WhatsApp contacts). Only the keys present
     * in the request are touched, so each accordion form is independent.
     */
    public function saveAdmission(Request $request)
    {
        $data = [];

        // Scalar settings (page header, timings, form text, on/off toggles).
        foreach ($request->except(['_token', '_method', 'items', 'contacts']) as $key => $value) {
            if (is_scalar($value) || is_null($value)) {
                $data[$key] = (string) $value;
            }
        }

        // Dynamic list — admission information items.
        if ($request->has('items')) {
            $items = [];
            foreach ((array) $request->input('items') as $row) {
                $title = trim($row['title'] ?? '');
                if ($title === '') {
                    continue;
                }
                $items[] = [
                    'icon'   => trim($row['icon'] ?? 'fas fa-circle-info') ?: 'fas fa-circle-info',
                    'title'  => $title,
                    'desc'   => trim($row['desc'] ?? ''),
                    'active' => ! empty($row['active']),
                ];
            }
            $data['admission_items'] = json_encode($items);
        }

        // Dynamic list — WhatsApp contacts.
        if ($request->has('contacts')) {
            $contacts = [];
            foreach ((array) $request->input('contacts') as $row) {
                $number = preg_replace('/\D+/', '', $row['number'] ?? '');
                if ($number === '') {
                    continue;
                }
                $contacts[] = [
                    'name'   => trim($row['name'] ?? '') ?: 'WhatsApp',
                    'number' => $number,
                    'active' => ! empty($row['active']),
                ];
            }
            $data['admission_whatsapp'] = json_encode($contacts);
        }

        if (! empty($data)) {
            Settings::setMany($data);
        }

        return back()->with('success', 'Admission page saved.');
    }

    /**
     * Save the About Us → Certificates & Documents section.
     * The certificates are a dynamic repeatable list — each row has a
     * title, an optional file (PDF or image), and an active toggle.
     * Stored as a JSON list under "about_certs_list".
     */
    public function saveCertificates(Request $request)
    {
        $data = [];

        // Section heading + subtitle.
        foreach (['about_certs_label', 'about_certs_title', 'about_certs_sub'] as $key) {
            if ($request->has($key)) {
                $data[$key] = (string) $request->input($key);
            }
        }

        $certs    = [];
        $keptKeys = [];

        foreach ((array) $request->input('certs', []) as $idx => $row) {
            $title = trim($row['title'] ?? '');
            $file  = trim($row['file_existing'] ?? '');

            // Remove the current file.
            if (! empty($row['file_remove']) && $file !== '') {
                Storage::disk('public')->delete($file);
                $file = '';
            }

            // Upload / replace the file.
            $upload = $request->file("certs.{$idx}.file");
            if ($upload) {
                $request->validate([
                    "certs.{$idx}.file" => 'file|mimes:pdf,jpg,jpeg,png,webp|max:8192',
                ]);
                if ($file !== '') {
                    Storage::disk('public')->delete($file);
                }
                $file = $upload->store('certificates', 'public');
            }

            // Skip rows with no title and no file.
            if ($title === '' && $file === '') {
                continue;
            }

            if ($file !== '') {
                $keptKeys[] = $file;
            }

            $certs[] = [
                'title'  => $title,
                'file'   => $file,
                'active' => ! empty($row['active']),
            ];
        }

        // Delete files belonging to certificates removed from the list.
        $previous = json_decode((string) Settings::get('about_certs_list', ''), true);
        if (is_array($previous)) {
            foreach ($previous as $old) {
                $oldFile = trim((string) ($old['file'] ?? ''));
                if ($oldFile !== '' && ! in_array($oldFile, $keptKeys, true)) {
                    Storage::disk('public')->delete($oldFile);
                }
            }
        }

        $data['about_certs_list'] = json_encode($certs);

        Settings::setMany($data);

        return back()->with('success', 'Certificates saved.');
    }

    /**
     * Save an About Us people group — Administration, SMC or PTA.
     * Each group has heading fields plus 12 member slots
     * (name, role and an optional photo).
     */
    public function savePeople(Request $request)
    {
        $pkey = (string) $request->input('pkey');
        abort_unless(
            in_array($pkey, ['about_administration', 'about_smc', 'about_pta'], true),
            422
        );

        $data = [];

        // Section heading + subtitle.
        foreach (['_label', '_title', '_sub'] as $suffix) {
            $k = $pkey . $suffix;
            if ($request->has($k)) {
                $data[$k] = (string) $request->input($k);
            }
        }

        // Dynamic member list — name, role and an optional photo.
        $members  = [];
        $keptKeys = [];

        foreach ((array) $request->input('members', []) as $idx => $row) {
            $name = trim($row['name'] ?? '');
            if ($name === '') {
                continue;
            }

            $photo = trim($row['photo_existing'] ?? '');

            // Remove the current photo.
            if (! empty($row['photo_remove']) && $photo !== '') {
                Storage::disk('public')->delete($photo);
                $photo = '';
            }

            // Upload / replace the photo.
            $file = $request->file("members.{$idx}.photo");
            if ($file) {
                $request->validate(["members.{$idx}.photo" => 'image|max:4096']);
                if ($photo !== '') {
                    Storage::disk('public')->delete($photo);
                }
                $photo = $file->store('people', 'public');
            }

            if ($photo !== '') {
                $keptKeys[] = $photo;
            }

            $members[] = [
                'name'  => $name,
                'role'  => trim($row['role'] ?? ''),
                'photo' => $photo,
            ];
        }

        // Delete photos belonging to members removed from the list.
        $previous = json_decode((string) Settings::get($pkey . '_members', ''), true);
        if (is_array($previous)) {
            foreach ($previous as $old) {
                $oldPhoto = trim((string) ($old['photo'] ?? ''));
                if ($oldPhoto !== '' && ! in_array($oldPhoto, $keptKeys, true)) {
                    Storage::disk('public')->delete($oldPhoto);
                }
            }
        }

        $data[$pkey . '_members'] = json_encode($members);

        Settings::setMany($data);

        return back()->with('success', 'Members saved.');
    }

    /**
     * Save the Academic → Academic Calendar section.
     * A dynamic repeatable list of calendar images, each with an
     * optional caption. Stored as JSON under "acad_calendar_images".
     */
    public function saveCalendar(Request $request)
    {
        $data = [];

        foreach (['acad_calendar_title', 'acad_calendar_sub'] as $key) {
            if ($request->has($key)) {
                $data[$key] = (string) $request->input($key);
            }
        }

        $images   = [];
        $keptKeys = [];

        foreach ((array) $request->input('calendar', []) as $idx => $row) {
            $caption = trim($row['caption'] ?? '');
            $file    = trim($row['file_existing'] ?? '');

            if (! empty($row['file_remove']) && $file !== '') {
                Storage::disk('public')->delete($file);
                $file = '';
            }

            $upload = $request->file("calendar.{$idx}.file");
            if ($upload) {
                $request->validate(["calendar.{$idx}.file" => 'image|max:8192']);
                if ($file !== '') {
                    Storage::disk('public')->delete($file);
                }
                $file = $upload->store('calendar', 'public');
            }

            if ($file === '') {
                continue;
            }

            $keptKeys[] = $file;
            $images[]   = ['file' => $file, 'caption' => $caption];
        }

        $previous = json_decode((string) Settings::get('acad_calendar_images', ''), true);
        if (is_array($previous)) {
            foreach ($previous as $old) {
                $oldFile = trim((string) ($old['file'] ?? ''));
                if ($oldFile !== '' && ! in_array($oldFile, $keptKeys, true)) {
                    Storage::disk('public')->delete($oldFile);
                }
            }
        }

        $data['acad_calendar_images'] = json_encode($images);

        Settings::setMany($data);

        return back()->with('success', 'Academic calendar saved.');
    }

    /**
     * Save the Academic → Curriculum section.
     * A dynamic repeatable list of curriculum cards (icon, title,
     * description). Stored as JSON under "acad_curriculum_items".
     */
    public function saveCurriculum(Request $request)
    {
        $data = [];

        foreach (['acad_curriculum_title', 'acad_curriculum_sub'] as $key) {
            if ($request->has($key)) {
                $data[$key] = (string) $request->input($key);
            }
        }

        $items = [];
        foreach ((array) $request->input('curriculum', []) as $row) {
            $title = trim($row['title'] ?? '');
            if ($title === '') {
                continue;
            }
            $items[] = [
                'icon'  => trim($row['icon'] ?? 'fas fa-book') ?: 'fas fa-book',
                'title' => $title,
                'desc'  => trim($row['desc'] ?? ''),
            ];
        }

        $data['acad_curriculum_items'] = json_encode($items);

        Settings::setMany($data);

        return back()->with('success', 'Curriculum saved.');
    }

    // ── Banner slides ───────────────────────────────────────

    public function storeSlide(Request $request)
    {
        $request->validate([
            'image'       => 'required|image|max:4096',
            'title'       => 'nullable|string|max:150',
            'subtitle'    => 'nullable|string|max:300',
            'button_text' => 'nullable|string|max:60',
            'button_link' => 'nullable|string|max:200',
        ]);
        BannerSlide::create([
            'image'       => $request->file('image')->store('slides', 'public'),
            'title'       => $request->title,
            'subtitle'    => $request->subtitle,
            'button_text' => $request->button_text,
            'button_link' => $request->button_link,
            'sort_order'  => (BannerSlide::max('sort_order') ?? 0) + 1,
            'active'      => true,
        ]);
        return back()->with('success', 'Slide added.');
    }

    public function toggleSlide(BannerSlide $slide)
    {
        $slide->update(['active' => ! $slide->active]);
        return back()->with('success', 'Slide updated.');
    }

    public function destroySlide(BannerSlide $slide)
    {
        Storage::disk('public')->delete($slide->image);
        $slide->delete();
        return back()->with('success', 'Slide deleted.');
    }
}
