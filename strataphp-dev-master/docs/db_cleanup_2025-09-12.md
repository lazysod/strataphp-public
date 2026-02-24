# Database Cleanup: Removal of Legacy Tables

**Date:** 12 September 2025
**Author:** lazysod

## Summary
This update removes unused legacy tables from the database install script (`db_instal.sql`) to improve database hygiene and maintainability. These tables were remnants of previous session and login management implementations and are no longer referenced in the codebase.

## Tables Removed
- `ban_ip`
- `cookie_login`
- `error_log`
- `ip_log`
- `login_sessions`
- `login_tracker`

## Reason for Removal
- The framework now uses the `user_sessions` table for all session and device management.
- Legacy tables were not referenced in any current code or migrations.
- Reduces clutter and potential confusion for future development.

## Impact
- New installations will not create these unused tables.
- Existing databases should drop these tables if present (optional for live systems).
- No impact on current session, login, or admin/user dashboard functionality.

## How to Drop Legacy Tables (Optional)
If you wish to remove these tables from an existing database, run the following SQL:

```sql
DROP TABLE IF EXISTS ban_ip, cookie_login, error_log, ip_log, login_sessions, login_tracker;
```

## References
- See commit: `adding new trimmed DB`
- Related files: `mysql/db_instal.sql`, migration history

---
For questions, contact the maintainer or check the repository documentation.
