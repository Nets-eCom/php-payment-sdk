---
name: php-dependency-audit
description: Check Composer dependencies for CVEs and security advisories during Nexi Checkout SDK code reviews, dependency changes, or explicit audit requests.
---

# Composer dependency audit

1. Run audits in Docker by default using [php-isolated-checks](../php-isolated-checks/SKILL.md)
   for a fresh PHP 8.2 image and dependency installation. Do not audit host
   `vendor/` unless the user explicitly requests the local installed dependency set.
2. After installation, run `composer audit --locked --no-interaction` inside the
   same container before cleanup. Never copy or use an existing host lock; audit
   only the lock freshly generated inside the container and report fresh resolution.
   For an audit-only request, skip the quality tools. For combined checks, run the
   audit even if a quality check fails and retain both exit statuses. Do not
   generate or change the host lock just to audit.
3. Report affected package/version, CVE or advisory ID, severity, patched version
   when known, and runtime vs development scope. Recommend compatible updates;
   do not apply them during review-only tasks or suppress advisories.
4. Report missing dependencies or an unavailable advisory service as blocked,
   not passed. A clean result applies only to the audited dependency set. Reuse
   results from the current task unless dependencies have changed.
