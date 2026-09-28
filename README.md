# OpenConsent-EU

Free. Open source. Privacy first.

**Lightweight, free, and open-source cookie consent management for WordPress websites.**

OpenConsent EU is a WordPress cookie consent plugin designed to help websites implement privacy-friendly cookie and tracking consent controls for visitors in the European Union, including Ireland.

## Current Features

- Visitors can accept all offered categories, reject optional categories, or choose categories individually.
- Necessary storage is always active. Optional categories start unselected and do not run until chosen.
- Visitors can reopen **Cookie settings**, change their choice, or withdraw optional consent.
- Choices are stored in the visitor's browser, not sent to this plugin's server. They expire after the configured period or when the consent version changes.
- Administrators can edit banner copy, the privacy-information link, category names and purposes, and appearance under **Settings → OpenConsent EU**. Configuration is stored in the site's WordPress database.
- Appearance settings include light, dark, or device-based color mode; editable light/dark palettes; a font selector; and a base font size limited to 12–32 px. The default font stack ends in `sans-serif`.
- WordPress scripts explicitly mapped to an optional category are held until that category is allowed. This includes WordPress inline scripts associated with mapped handles.
- Optional Google Consent Mode v2 signals can be enabled in General settings; the default is off.
- The `openconsent_eu_categories` PHP filter allows developers to add or adjust categories. The browser dispatches an `openconsent:consent-changed` event when a choice changes.
- GitHub release updates are offered through WordPress when a newer public release is available.

The interface uses vanilla CSS and JavaScript and does not require Bootstrap, jQuery, or a frontend framework.

When enabled, Google Consent Mode starts with optional storage denied and maps analytics, preferences, and marketing choices to their corresponding Google signals. This sends consent signals; it does not install Google tags or configure Google Analytics for the site.

## Consent Management

OpenConsent EU keeps optional WordPress-enqueued scripts configured in the plugin inactive until the visitor allows their mapped category. Optional choices are unselected until the visitor makes a choice.

Visitors can:

- Accept all
- Reject non-essential cookies
- Select individual categories
- Change their preferences later
- Withdraw previously given consent

A persistent **Cookie settings** control remains available after a choice. Withdrawing optional consent saves a necessary-only choice and reloads the page so mapped scripts do not run on the next page load. Scripts already executed may have set cookies that require separate site-specific deletion.

## Cookie Categories

### Necessary

Cookies required for the website to function properly.

- Always enabled
- Cannot normally be disabled through the consent interface

### Preferences

Cookies used to remember visitor preferences.

- Disabled until consent
- Can be enabled by the visitor

### Analytics

Cookies and technologies used to understand website usage.

- Disabled until consent
- Can be enabled by the visitor

### Marketing

Cookies and technologies used for advertising, remarketing, and related purposes.

- Disabled until consent
- Can be enabled by the visitor

### Other

Additional non-essential cookies or technologies that do not fit another category.

- Disabled until consent
- Can be enabled by the visitor

## Script Blocking

Administrators can map WordPress script handles to optional categories under **Settings → OpenConsent EU → General**. Each line uses `handle:category` format, for example:

```text
site_analytics:analytics
advertising_pixel:marketing
```

Only mapped scripts registered through WordPress are handled. The plugin does not detect arbitrary hard-coded tags in themes, page builders, or remote content; review the site's actual integrations and test its script behavior.

## Google Consent Mode

Google Consent Mode v2 signals are optional and disabled by default. When enabled, the plugin emits denied defaults in `wp_head`, then maps visitor choices for analytics, preferences, and marketing to Google consent signals. Enable it only when those defaults run before your Google tags. This does not install or configure Google tags.

## Admin Settings

The WordPress administrator can configure:

- Banner wording and a privacy-information URL
- Built-in category labels, purposes, and whether optional categories are offered
- Light/dark/system color mode, palette colors, font family, and font size
- Consent validity period and consent version
- WordPress script handles and their consent categories

## Appearance Customization

Administrators can customize the consent interface without editing theme files.

Available appearance options include:

- Font family
- Font size
- Editable light/dark background, text, accent, and border colors
- Device-based color mode

## Privacy

OpenConsent EU is designed to minimize unnecessary data collection.

The plugin:

- Does not require visitor IP addresses to remember consent
- Does not send consent data to an external SaaS service
- Does not require an external account
- Does not contain advertising
- Does not track visitors for its own purposes
- Stores visitor choices in browser local storage; choices are not sent to the plugin's server
- Does not automatically discover cookies or verify that a site's configuration is complete
- Does not create a server-side consent audit trail in this browser-local storage mode

## Developer Features

OpenConsent EU is designed to be extensible.

Available developer extension points include:

- The `openconsent_eu_categories` PHP filter
- The `openconsent:consent-changed` browser event

Further developer features may include:

- Additional WordPress PHP hooks and filters
- Custom consent categories
- Custom integrations
- REST API support
- WP-CLI support
- Theme integration
- Plugin integration

## GitHub Updates

OpenConsent EU checks the public GitHub repository for its latest stable release. When a release is newer than the installed version, WordPress displays it under **Dashboard → Updates** and offers the normal plugin update flow. GitHub responses are cached for six hours.

To publish an update, create a published, non-prerelease GitHub release with a version tag such as:

```text
v1.0.0
v1.0.1
v1.1.0
v2.0.0
```

Use a tag that matches the release version (for example, `v1.0.1` for version `1.0.1`). WordPress downloads GitHub's generated source archive for that release.

Updates require the repository to remain public. Only official project releases should be trusted and installed.

## Security

Security is a core part of OpenConsent EU.

The project aims to use:

- WordPress sanitization
- WordPress escaping
- WordPress nonces
- Capability checks
- Secure settings handling
- Dependency minimization
- Automated security checks
- Protected GitHub branches
- Pull-request-based development
- Code review
- Secret scanning
- Dependency scanning

Security vulnerabilities should be reported privately rather than publicly disclosed before they can be investigated and fixed.

See `SECURITY.md` for the security reporting process.

## Development

OpenConsent EU uses a protected `main` branch.

Development workflow:

```text
Feature branch
      ↓
Development
      ↓
Pull Request
      ↓
Security & Tests
      ↓
Code Review
      ↓
Approved
      ↓
main
      ↓
GitHub Release
```

The `main` branch is protected and should not receive unreviewed direct changes.

## Roadmap

Planned features may include:

- [x] Initial cookie consent banner and preference center
- [x] Built-in consent categories
- [x] Configured WordPress script blocking
- [x] Consent versioning and expiry
- [x] Appearance customizer
- [x] Keyboard-operable consent controls
- [x] GitHub updater
- [x] Optional Google Consent Mode v2 signals
- [ ] Multilingual support
- [ ] Cookie scanner
- [ ] Managed cookie and service catalogue
- [ ] Regional compliance profiles
- [ ] Developer API
- [ ] WP-CLI support
- [ ] WordPress.org release

Features may change as the project develops.

## Compliance Notice

OpenConsent EU is a technical tool intended to help website owners implement cookie and tracking consent mechanisms.

It does **not** guarantee that a website is legally compliant.

Website owners are responsible for:

- Identifying the cookies and tracking technologies used on their website
- Configuring services correctly
- Providing appropriate privacy information
- Obtaining valid consent where required
- Respecting applicable laws and regulations
- Keeping their configuration up to date

Legal requirements can vary between EU member states and may change over time.

## License

OpenConsent EU is free and open-source software licensed under:

**GNU General Public License v3.0 or later (GPL-3.0-or-later)**

See the `LICENSE` file for the full license.

## Project Goals

OpenConsent EU aims to be:

- **Free**
- **Open source**
- **Lightweight**
- **Privacy-focused**
- **Secure**
- **Transparent**
- **Developer-friendly**
- **Easy to configure**
- **Easy to maintain**
- **Free from unnecessary bloat**

> **OpenConsent EU — Open, lightweight cookie consent for WordPress.**
