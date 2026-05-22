# Project TODO

## ✅ Done — Teacher & Class Access
- Teachers are `users` with the `teacher` role. Profile fields on `users`:
  `phone`, `designation`, `photo`, `assigned_classes` (JSON list of class names).
- **No separate sections/classes table.** A class is just the existing student
  `class` string (`Member::classes()` list). A teacher is assigned class *names*.
- Sidebar MAIN group: Dashboard, Teachers (admin), Classes, Students, Documents.
  CONTENT group: News, Events, Hall of Fame, Gallery, Programmes, Videos,
  Downloads, Inquiries. ADMIN group: Customizer, Pages, Users, Activity Logs.
- Admin → **Teachers** manager (CRUD, tick which classes each teacher gets).
- **Classes** page (`/admin/classes`) — admin sees all classes, a teacher sees only
  their assigned classes; clicking one lists that class's students.
  Access is checked in `ClassroomController` (admin bypass, else `teachesClass()`).
- Teacher-only logins are redirected from the dashboard to `/admin/classes` and the
  sidebar hides all staff/admin modules for them.
- Site Customizer is now 2 tabs: **General & Settings** and **Homepage**
  (homepage section accordions, each on/off + editable).

## ⏳ Next Plan — Questions & Notes upload (Phase 2)
Deferred by request. To build:
1. **Migrations**
   - `questions` — id, `class` (string), `subject` (nullable), `term` (nullable),
     `semester` (nullable), title, body (text), file (nullable), `uploaded_by` FK→users.
   - `notes` — same structure.
2. **Models** — `Question`, `Note`; each `belongsTo` User (uploader).
3. **Teacher area** — inside `/admin/classes/{class}`, add Questions and Notes
   tabs: list + upload + delete, every query scoped by `class` and re-checking
   `teachesClass()` exactly like `ClassroomController@show`.
4. `term` / `semester` stay nullable for now — activate later.

## Misc cleanup (low priority)
- Demo accounts `admin@demo.com` / `staff@demo.com` still exist.
- Change `admin@jnnazareth.edu.in` password from the default.
- Unused legacy files: `HomeController`, `PublicController`, `resources/views/public/*`.
