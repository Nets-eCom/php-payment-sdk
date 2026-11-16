# Working on this SDK

- Apply these skills in `.agents/skills/` automatically:
  - `php-code-review/SKILL.md`: when adding, modifying, or reviewing SDK code in
    `lib/`, tests in `tests/`, PHP example scripts, or PHP tool configuration such
    as `ecs.php` and `rector.php`.
  - `php-dependency-audit/SKILL.md`: as part of those code reviews, when changing
    Composer dependency requirements in `composer.json`, or when an audit is requested.
- Follow these skills during implementation; the user does not need to request
  a separate review or testing step.
- Use `.agents/skills/php-isolated-checks/SKILL.md` for requested Docker/isolation
  or different-PHP checks, or when local runtime incompatibility blocks verification.
  It is not required after every edit.
- Preserve unrelated work and public compatibility. Keep changes focused.
- For documentation-only changes, check content and links instead of running PHP tools.
- Report checks actually run and any failures or blockers. Never suppress errors
  or weaken checks to obtain a pass.
