# Agent Instructions

## Read first
- **`CHANGELOG.md`** — session-by-session log of what changed, why, and current state. Read this BEFORE making any changes so you know what's already been done.
- **`TODO.md`** — original roadmap (most items already shipped).
- **`FEATURES.md`** — full feature inventory.

## Deployment rules
- **Live server is production** — never push database files, .env, or demo data.
- **Code only**: controllers, views, routes, middleware, config, migrations.
- Always add any database file to `.gitignore` before committing.
- Don't run seeders on live.
- Users may need to re-login after sessions/cookie changes.

## Workflow expectations
- Branch: `main` (only). All commits go straight to main.
- After making changes, run `php artisan view:cache` locally to verify Blade compiles.
- Migrations are additive only (no `drop column` on live tables).
- For UI bugs, check `storage/logs/laravel.log` on the server first — paste the actual error rather than guessing.

## After every session
Update `CHANGELOG.md` with a new dated section summarizing:
- New features added
- Bugs fixed
- Schema changes
- Files most changed
- Known limitations carried forward
