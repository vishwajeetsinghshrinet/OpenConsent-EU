# OpenConsent-EU
Free. Open source. Privacy first.

**Lightweight, free, and open-source cookie consent management for WordPress websites.**

OpenConsent EU is a WordPress cookie consent plugin designed to help websites implement privacy-friendly cookie and tracking consent controls for visitors in the European Union, including Ireland.

## Features

* 🍪 Cookie consent banner
* 🇪🇺 Designed for EU privacy requirements
* 🇮🇪 Ireland-friendly configuration
* 🔒 Blocks non-essential scripts until consent is given
* ✅ Accept all cookies
* ❌ Reject non-essential cookies
* ⚙️ Manage cookie preferences
* 📊 Separate consent categories:

  * Necessary
  * Preferences
  * Analytics
  * Marketing
  * Other
* 💾 Remembers the visitor's consent choice
* 🔄 Supports consent withdrawal and preference changes
* 🔢 Consent versioning
* 🎨 Fully customizable banner appearance
* 🖋️ Change font family
* 🔠 Change font sizes
* 🎨 Customize colors
* 📐 Customize banner width, spacing, borders, and radius
* 📱 Responsive design for mobile, tablet, and desktop
* ♿ Accessibility-focused interface
* 🚀 Lightweight frontend assets
* 🧩 Developer-friendly hooks and JavaScript events
* 🔐 No visitor IP address required for consent storage
* 🚫 No advertising or tracking by OpenConsent EU
* ☁️ No external SaaS account required
* 💰 100% free
* 🔓 Open source
* 🔄 GitHub-based updates

## Consent Management

OpenConsent EU is designed around the principle that non-essential cookies and tracking technologies should not be activated before the visitor provides the appropriate consent.

Visitors can:

* Accept all
* Reject non-essential cookies
* Select individual categories
* Change their preferences later
* Withdraw previously given consent

A persistent **Cookie Settings** option can remain available so visitors can change their choices at any time.

## Cookie Categories

### Necessary

Cookies required for the website to function properly.

* Always enabled
* Cannot normally be disabled through the consent interface

### Preferences

Cookies used to remember visitor preferences.

* Disabled until consent
* Can be enabled by the visitor

### Analytics

Cookies and technologies used to understand website usage.

* Disabled until consent
* Can be enabled by the visitor

### Marketing

Cookies and technologies used for advertising, remarketing, and related purposes.

* Disabled until consent
* Can be enabled by the visitor

### Other

Additional non-essential cookies or technologies that do not fit another category.

* Disabled until consent
* Can be enabled by the visitor

## Script Blocking

OpenConsent EU can prevent configured non-essential scripts from executing until the required consent has been granted.

Example:

```html
<script type="text/plain" data-cookie-category="analytics">
    // Analytics code
</script>
```

The script can be activated only after the visitor provides the required consent.

## Admin Settings

The WordPress administrator can configure:

* General settings
* Consent categories
* Cookies
* Services
* Scripts
* Banner appearance
* Typography
* Colors
* Buttons
* Position
* Size
* Border radius
* Shadows
* Mobile behavior
* Consent duration
* Consent version
* Advanced settings
* Developer options

## Appearance Customization

Administrators can customize the consent interface without editing theme files.

Available options include:

* Font family
* Font size
* Heading size
* Text color
* Background color
* Button colors
* Button text colors
* Border color
* Border radius
* Card width
* Padding
* Shadow
* Banner position
* Custom CSS

## Privacy

OpenConsent EU is designed to minimize unnecessary data collection.

The plugin:

* Does not require visitor IP addresses to remember consent
* Does not send consent data to an external SaaS service
* Does not require an external account
* Does not contain advertising
* Does not track visitors for its own purposes
* Stores consent preferences locally where possible

## Developer Features

OpenConsent EU is designed to be extensible.

Future developer features may include:

* WordPress PHP hooks
* WordPress filters
* JavaScript events
* Custom consent categories
* Custom integrations
* REST API support
* WP-CLI support
* Theme integration
* Plugin integration

## GitHub Updates

OpenConsent EU is developed and maintained through GitHub.

Releases will use version tags such as:

```text
v1.0.0
v1.0.1
v1.1.0
v2.0.0
```

The plugin can check for official releases and provide updates through WordPress.

Only official project releases should be trusted and installed.

## Security

Security is a core part of OpenConsent EU.

The project aims to use:

* WordPress sanitization
* WordPress escaping
* WordPress nonces
* Capability checks
* Secure settings handling
* Dependency minimization
* Automated security checks
* Protected GitHub branches
* Pull-request-based development
* Code review
* Secret scanning
* Dependency scanning

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

* [ ] Initial cookie consent banner
* [ ] Consent categories
* [ ] Script blocking
* [ ] Preference center
* [ ] Consent versioning
* [ ] Appearance customizer
* [ ] Accessibility improvements
* [ ] GitHub updater
* [ ] Google Consent Mode support
* [ ] Multilingual support
* [ ] Cookie scanner
* [ ] Cookie/service database
* [ ] Regional compliance profiles
* [ ] Developer API
* [ ] WP-CLI support
* [ ] WordPress.org release

Features may change as the project develops.

## Compliance Notice

OpenConsent EU is a technical tool intended to help website owners implement cookie and tracking consent mechanisms.

It does **not** guarantee that a website is legally compliant.

Website owners are responsible for:

* Identifying the cookies and tracking technologies used on their website
* Configuring services correctly
* Providing appropriate privacy information
* Obtaining valid consent where required
* Respecting applicable laws and regulations
* Keeping their configuration up to date

Legal requirements can vary between EU member states and may change over time.

## License

OpenConsent EU is free and open-source software licensed under:

**GNU General Public License v2.0 or later (GPL-2.0-or-later)**

See the `LICENSE` file for the full license.

## Project Goals

OpenConsent EU aims to be:

* **Free**
* **Open source**
* **Lightweight**
* **Privacy-focused**
* **Secure**
* **Transparent**
* **Developer-friendly**
* **Easy to configure**
* **Easy to maintain**
* **Free from unnecessary bloat**

> **OpenConsent EU — Open, lightweight cookie consent for WordPress.**
