# Codex Handoff

Updated: 2026-09-28

## Current State

The 1.1.0 work is complete in the working tree and has not been committed or released. The base branch is `main` at the `v1.0.0` release.

## Completed Work

- Moved OpenConsent EU to a top-level admin menu while retaining its settings slug, capability check, tabs, callback, and page-scoped admin stylesheet.
- Added Visitor information settings with enabled-by-default official EU privacy-rights resources, editable copy and links, and opt-in neutral project attribution.
- Added a collapsed native `<details>` privacy-information section with safely escaped links that open with `_blank` and `noopener noreferrer`.
- Added consent-banner, persistent-settings-button, and dialog transitions with reduced-motion support.
- Implemented dialog keyboard focus management, reliable post-close focus restoration, and a safe fallback close path for browsers without native dialog support.
- Added a consent configuration fingerprint so category, purpose, and script-mapping changes invalidate prior consent without changing the localStorage object structure.
- Added mapping validation, protected mapped categories from accidental disabling, ordered script activation, and a timeout so one stalled third-party script cannot block later mapped scripts forever.
- Added PHP settings and headless-browser acceptance tests.

## Verification Completed

- PHP syntax checks pass for all plugin, admin, and test PHP files.
- `php tests/php-settings.php` passes.
- `node --check assets/js/banner.js` and `node --check tests/browser-consent.mjs` pass.
- `node tests/browser-consent.mjs` passes.
- `git diff --check` passes.

Do not claim legal compliance or certification. Do not commit, push, or create a release unless the maintainer asks.
