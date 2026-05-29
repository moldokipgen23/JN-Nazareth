# Deployment Rules

- **Live server is production** — never push database files, .env, or demo data
- **Code only**: controllers, views, routes, middleware, config, migrations
- Always add any database file to `.gitignore` before committing
- Don't run seeders on live
- Users may need to re-login after sessions/cookie changes
