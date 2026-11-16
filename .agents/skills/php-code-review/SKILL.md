---
name: php-code-review
description: Apply when implementing, refactoring, or reviewing PHP code in the Nexi Checkout SDK. Check compatibility, regression and new-feature test coverage, and Composer vulnerabilities when relevant.
---

# PHP code quality

1. Read the affected code, callers, and nearby tests. Follow existing conventions
   and the current Composer and tool configurations.
2. Preserve PHP 8.1 support and public signatures, including parameter names used
   by named arguments. Keep concrete HTTP clients and dev-only dependencies out
   of `lib/`.
3. Keep types and PHPStan array shapes accurate. Preserve integer monetary units,
   opaque string IDs, and exception chains.
4. Add regression tests for bug fixes and behavior tests for new functionality.
   Cover relevant success, failure, and boundary cases in the closest existing
   test suite. For review-only tasks, identify missing coverage without editing.
5. Apply [php-dependency-audit](../php-dependency-audit/SKILL.md) as part of code
   review to check Composer packages for CVEs and security advisories. Also apply
   it when dependencies change or a package vulnerability is suspected.
6. Verify implementation changes with `make phpunit`, `make phpstan-check`,
   `make ecs-check`, and `make rector-check`, running each independently.
   For review-only requests,
   report concrete defects with file/line, consequence, and a focused fix; do not
   edit code unless asked.
