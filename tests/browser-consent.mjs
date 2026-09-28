import assert from "node:assert/strict";
import { readFile } from "node:fs/promises";
import { createServer } from "node:http";
import { mkdtemp, rm } from "node:fs/promises";
import { tmpdir } from "node:os";
import { join } from "node:path";
import { spawn } from "node:child_process";

const chrome = process.env.CHROME_BIN || "google-chrome";
const profile = await mkdtemp(join(tmpdir(), "openconsent-browser-"));
const script = await readFile(
  new URL("../assets/js/banner.js", import.meta.url),
  "utf8",
);
const css = await readFile(
  new URL("../assets/css/banner.css", import.meta.url),
  "utf8",
);

const page = `<!doctype html>
<html><head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="/banner.css">
<script>
window.OCEConsentConfig = {
  consentVersion: "test-1",
  expiryDays: 30,
  googleConsentMode: true,
  saveError: "Could not save your choice.",
  categories: {
    necessary: { required: true, enabled: true },
    analytics: { required: false, enabled: true },
    marketing: { required: false, enabled: true }
  }
};
window.__order = [];
window.__gtagCalls = [];
window.gtag = (...args) => window.__gtagCalls.push(args);
try {
  const saved = JSON.parse(localStorage.getItem("openconsent-eu-consent") || "null");
  if (saved && saved.version === OCEConsentConfig.consentVersion && saved.expiresAt > Date.now()) {
    document.documentElement.classList.add("oce-consent-recorded");
  }
} catch (_) {}
</script>
<div class="oce-widget" id="oce-widget" data-theme="light">
  <section class="oce-banner" id="oce-banner" aria-labelledby="oce-banner-title">
    <p class="oce-eyebrow"><span class="oce-indicator" aria-hidden="true"></span>Your privacy matters</p>
    <h2 class="oce-title" id="oce-banner-title">Choose your cookie settings</h2>
    <p class="oce-copy">Choose whether to allow optional categories configured for this site.</p>
    <div class="oce-actions">
      <button class="oce-button oce-button--primary" type="button" data-oce-accept>Accept all</button>
      <button class="oce-button" type="button" data-oce-reject>Reject optional</button>
      <button class="oce-button oce-button--text" type="button" data-oce-customize>Choose settings</button>
    </div>
  </section>
  <button class="oce-settings-link" id="oce-settings-link" type="button" hidden>Cookie settings</button>
  <dialog class="oce-dialog" id="oce-dialog" aria-labelledby="oce-dialog-title">
    <div class="oce-dialog__inner">
      <div class="oce-dialog__header"><h2 id="oce-dialog-title">Cookie preferences</h2><button type="button" data-oce-close>Close</button></div>
      <input type="checkbox" data-oce-category-input="necessary" checked disabled>
      <input type="checkbox" data-oce-category-input="analytics">
      <input type="checkbox" data-oce-category-input="marketing">
      <details class="oce-privacy-info"><summary>Learn about your privacy rights</summary>
        <a href="https://commission.europa.eu/law/law-topic/data-protection/information-individuals_en" target="_blank" rel="noopener noreferrer">Your data-protection rights in the EU<span class="oce-screen-reader-text"> (opens in a new tab)</span></a>
      </details>
      <div class="oce-actions oce-dialog__actions">
        <button type="button" data-oce-save>Save my choices</button>
        <button type="button" data-oce-reject>Reject optional</button>
        <button type="button" data-oce-accept>Accept all</button>
      </div>
      <button type="button" data-oce-withdraw>Withdraw optional consent</button>
      <p data-oce-save-error hidden></p>
    </div>
  </dialog>
</div>
<script type="text/plain" src="/analytics.js" data-oce-consent-category="analytics" data-oce-original-type="text/javascript"></script>
<script type="text/plain" data-oce-consent-category="analytics" data-oce-original-type="text/javascript">window.__order.push("inline");</script>
<script src="/banner.js"></script>
<script>
const waitFor = async (condition, message) => {
  for (let i = 0; i < 60; i++) {
    if (condition()) return;
    await new Promise(resolve => setTimeout(resolve, 25));
  }
  throw new Error(message);
};
const record = (key, value) => {
  const results = JSON.parse(sessionStorage.getItem("oce-results") || "{}");
  results[key] = String(value);
  sessionStorage.setItem("oce-results", JSON.stringify(results));
  document.body.dataset[key] = String(value);
};
window.addEventListener("load", async () => {
  try {
    const banner = document.getElementById("oce-banner");
    const button = document.getElementById("oce-settings-link");
    const dialog = document.getElementById("oce-dialog");
    const stage = sessionStorage.getItem("oce-stage") || "first";

    if (stage === "first") {
      record("firstVisitBanner", !banner.hidden);
      record("firstVisitSettingsHidden", button.hidden);
      record("privacyCollapsed", !document.querySelector(".oce-privacy-info").open);
      const resource = document.querySelector(".oce-privacy-info a");
      record("resourceSafe", resource.target === "_blank" && resource.rel === "noopener noreferrer");
      document.querySelector("[data-oce-customize]").click();
      await waitFor(() => dialog.open, "preferences dialog did not open");
      await new Promise(requestAnimationFrame);
      record("focusEnteredDialog", dialog.contains(document.activeElement));
      const first = document.querySelector("[data-oce-close]");
      first.focus();
      first.dispatchEvent(new KeyboardEvent("keydown", { key: "Tab", shiftKey: true, bubbles: true, cancelable: true }));
      record("focusTrapped", dialog.contains(document.activeElement));
      dialog.dispatchEvent(new Event("cancel", { cancelable: true }));
      await waitFor(() => !dialog.open, "Escape/cancel did not close the dialog");
      await waitFor(() => document.activeElement === document.querySelector("[data-oce-customize]"), "focus did not return; active=" + document.activeElement?.outerHTML + "; dialogOpen=" + dialog.open);
      record("focusReturned", document.activeElement === document.querySelector("[data-oce-customize]"));
      document.querySelector("[data-oce-customize]").click();
      await waitFor(() => dialog.open, "preferences dialog did not reopen");
      document.querySelector('[data-oce-category-input="analytics"]').checked = true;
      sessionStorage.setItem("oce-stage", "accepted");
      document.querySelector("[data-oce-save]").click();
      await waitFor(() => button.hidden === false && banner.hidden, "saved choice did not swap banner for settings button; banner=" + banner.hidden + "; button=" + button.hidden + "; dialog=" + dialog.open);
      await waitFor(() => window.__order.join(",") === "external,inline", "consented scripts did not preserve order");
      record("choicePersisted", JSON.parse(localStorage.getItem("openconsent-eu-consent")).categories.analytics);
      record("googleSignalUpdated", window.__gtagCalls.some(call => call[0] === "consent" && call[1] === "update" && call[2].analytics_storage === "granted"));
      await waitFor(() => document.activeElement === button, "focus did not return to Cookie settings after saving");
      record("settingsFocusedAfterSave", document.activeElement === button);
      await new Promise(resolve => setTimeout(resolve, 220));
      location.reload();
      return;
    }

    if (stage === "accepted") {
      await waitFor(() => window.__order.join(",") === "external,inline", "saved consent did not activate scripts after reload");
      record("returningBannerHidden", banner.hidden);
      record("returningSettingsVisible", !button.hidden);
      button.click();
      await waitFor(() => dialog.open, "persistent settings button did not reopen preferences");
      record("savedChoiceRestored", document.querySelector('[data-oce-category-input="analytics"]').checked);
      document.querySelector('[data-oce-category-input="analytics"]').checked = false;
      sessionStorage.setItem("oce-stage", "revoked");
      document.querySelector("[data-oce-save]").click();
      return;
    }

    if (stage === "revoked") {
      record("revocationPersisted", !JSON.parse(localStorage.getItem("openconsent-eu-consent")).categories.analytics);
      record("revokedScriptStayedInert", window.__order.length === 0);
      record("revocationKeptSettingsButton", !button.hidden && banner.hidden);
      localStorage.setItem("openconsent-eu-consent", JSON.stringify({ version: "old", categories: { analytics: true }, expiresAt: Date.now() + 86400000 }));
      sessionStorage.setItem("oce-stage", "stale");
      location.reload();
      return;
    }

    record("staleVersionShowsBanner", !banner.hidden && button.hidden);
    record("staleVersionCleared", localStorage.getItem("openconsent-eu-consent") === null);
    document.querySelector("[data-oce-reject]").click();
    record("rejectOptionalPersists", !JSON.parse(localStorage.getItem("openconsent-eu-consent")).categories.analytics);
    Object.assign(document.body.dataset, JSON.parse(sessionStorage.getItem("oce-results") || "{}"));
    document.body.dataset.testResult = "passed";
  } catch (error) {
    document.body.dataset.testResult = "failed";
    document.body.dataset.testError = JSON.stringify({
      message: error.message,
      stack: error.stack,
      stage: sessionStorage.getItem("oce-stage"),
      banner: Boolean(document.getElementById("oce-banner")),
      settingsButton: Boolean(document.getElementById("oce-settings-link")),
      dialog: Boolean(document.getElementById("oce-dialog")),
    });
  }
});
</script>
</body></html>`;

const server = createServer((request, response) => {
  if (request.url === "/banner.js") {
    response.writeHead(200, { "Content-Type": "text/javascript" });
    response.end(script);
    return;
  }
  if (request.url === "/banner.css") {
    response.writeHead(200, { "Content-Type": "text/css" });
    response.end(css);
    return;
  }
  if (request.url === "/analytics.js") {
    response.writeHead(200, { "Content-Type": "text/javascript" });
    response.end('window.__order.push("external");');
    return;
  }
  response.writeHead(200, { "Content-Type": "text/html" });
  response.end(page);
});

const browser = process.env.CHROME_BIN || "google-chrome";
const output = await new Promise((resolve, reject) => {
  server.listen(0, "127.0.0.1", () => {
    const url = `http://127.0.0.1:${server.address().port}/`;
    const process = spawn(
      browser,
      [
        "--headless",
        "--no-sandbox",
        "--disable-gpu",
        "--disable-dev-shm-usage",
        `--user-data-dir=${profile}`,
        "--virtual-time-budget=5000",
        "--dump-dom",
        url,
      ],
      { stdio: ["ignore", "pipe", "pipe"] },
    );
    let stdout = "";
    let stderr = "";

    process.stdout.on("data", (chunk) => {
      stdout += chunk;
    });
    process.stderr.on("data", (chunk) => {
      stderr += chunk;
    });
    process.on("error", reject);
    process.on("close", (code) =>
      code === 0
        ? resolve(stdout)
        : reject(new Error(stderr || `Chrome exited ${code}`)),
    );
  });
});

server.close();
await rm(profile, { recursive: true, force: true });

const expected = {
  firstVisitBanner: "true",
  firstVisitSettingsHidden: "true",
  privacyCollapsed: "true",
  resourceSafe: "true",
  focusEnteredDialog: "true",
  focusTrapped: "true",
  focusReturned: "true",
  choicePersisted: "true",
  googleSignalUpdated: "true",
  settingsFocusedAfterSave: "true",
  returningBannerHidden: "true",
  returningSettingsVisible: "true",
  savedChoiceRestored: "true",
  revocationPersisted: "true",
  revokedScriptStayedInert: "true",
  revocationKeptSettingsButton: "true",
  staleVersionShowsBanner: "true",
  staleVersionCleared: "true",
  rejectOptionalPersists: "true",
};

for (const [name, value] of Object.entries(expected)) {
  const attribute = `data-${name.replace(/[A-Z]/g, (letter) => `-${letter.toLowerCase()}`)}="${value}"`;
  assert.ok(
    output.includes(attribute),
    `Missing browser assertion: ${name}=${value}; final body: ${output.match(/<body[^>]*>/)?.[0]}`,
  );
}
assert.ok(
  output.includes('data-test-result="passed"'),
  output.match(/data-test-error="([^"]*)"/)?.[1] ||
    "Browser flow did not finish",
);
console.log("Headless Chrome consent flow passed");
