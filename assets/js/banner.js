(() => {
  "use strict";

  const config = window.OCEConsentConfig;
  const widget = document.getElementById("oce-widget");
  if (!config || !widget) return;

  const storageKey = "openconsent-eu-consent";
  const banner = document.getElementById("oce-banner");
  const dialog = document.getElementById("oce-dialog");
  const settingsButton = document.getElementById("oce-settings-link");
  const saveErrors = document.querySelectorAll("[data-oce-save-error]");
  let memoryConsent = null;

  const closeDialog = () => {
    if (typeof dialog.close === "function") dialog.close();
    else dialog.removeAttribute("open");
  };

  const readConsent = () => {
    try {
      const consent = JSON.parse(
        window.localStorage.getItem(storageKey) || "null",
      );
      if (
        !consent ||
        typeof consent !== "object" ||
        consent.version !== config.consentVersion ||
        !Number.isFinite(consent.expiresAt) ||
        !consent.categories ||
        typeof consent.categories !== "object" ||
        consent.expiresAt <= Date.now()
      ) {
        window.localStorage.removeItem(storageKey);
        return null;
      }
      return consent;
    } catch (error) {
      return memoryConsent;
    }
  };

  const renderState = (consent) => {
    const hasChoice = Boolean(consent);
    banner.hidden = hasChoice;
    settingsButton.hidden = !hasChoice;

    document.querySelectorAll("[data-oce-category-input]").forEach((input) => {
      const id = input.dataset.oceCategoryInput;
      input.checked = Boolean(
        config.categories[id]?.required || consent?.categories?.[id],
      );
    });
  };

  const activateAllowedScripts = (consent) => {
    if (!consent) return;

    document
      .querySelectorAll('script[type="text/plain"][data-oce-consent-category]')
      .forEach((blocked) => {
        const category = blocked.dataset.oceConsentCategory;
        if (
          !consent.categories?.[category] ||
          blocked.dataset.oceActivated === "true"
        )
          return;

        const script = document.createElement("script");
        Array.from(blocked.attributes).forEach((attribute) => {
          if (
            ![
              "type",
              "data-oce-consent-category",
              "data-oce-original-type",
              "data-oce-activated",
            ].includes(attribute.name)
          ) {
            script.setAttribute(attribute.name, attribute.value);
          }
        });
        script.type = blocked.dataset.oceOriginalType || "text/javascript";
        script.textContent = blocked.textContent;
        blocked.dataset.oceActivated = "true";
        blocked.replaceWith(script);
      });
  };

  const dispatchConsent = (consent) => {
    const event = new CustomEvent("openconsent:consent-changed", {
      detail: consent,
    });
    document.dispatchEvent(event);
  };

  const updateGoogleConsent = (consent) => {
    if (!config.googleConsentMode || typeof window.gtag !== "function") return;

    const categories = consent?.categories || {};
    window.gtag("consent", "update", {
      ad_storage: categories.marketing ? "granted" : "denied",
      ad_user_data: categories.marketing ? "granted" : "denied",
      ad_personalization: categories.marketing ? "granted" : "denied",
      analytics_storage: categories.analytics ? "granted" : "denied",
      functionality_storage: categories.preferences ? "granted" : "denied",
      personalization_storage: categories.preferences ? "granted" : "denied",
      security_storage: "granted",
    });
  };

  const saveConsent = (choices) => {
    const categories = {};
    Object.entries(config.categories).forEach(([id, category]) => {
      categories[id] = Boolean(category.required || choices[id]);
    });

    const now = Date.now();
    const consent = {
      version: config.consentVersion,
      categories,
      updatedAt: now,
      expiresAt: now + config.expiryDays * 86400000,
    };

    memoryConsent = consent;
    let persisted = true;
    try {
      window.localStorage.setItem(storageKey, JSON.stringify(consent));
    } catch (error) {
      persisted = false;
    }

    saveErrors.forEach((message) => {
      message.textContent = persisted ? "" : config.saveError;
      message.hidden = persisted;
    });
    renderState(persisted ? consent : null);
    activateAllowedScripts(consent);
    dispatchConsent(consent);
    updateGoogleConsent(consent);
    if (persisted && dialog.open) closeDialog();
  };

  const openPreferences = () => {
    renderState(readConsent());
    if (typeof dialog.showModal === "function") dialog.showModal();
    else dialog.setAttribute("open", "");
  };

  document.querySelectorAll("[data-oce-accept]").forEach((button) => {
    button.addEventListener("click", () => {
      const choices = {};
      Object.keys(config.categories).forEach((id) => {
        choices[id] = true;
      });
      saveConsent(choices);
    });
  });

  document.querySelectorAll("[data-oce-reject]").forEach((button) => {
    button.addEventListener("click", () => saveConsent({}));
  });

  document
    .querySelectorAll("[data-oce-customize], #oce-settings-link")
    .forEach((button) => {
      button.addEventListener("click", openPreferences);
    });

  document
    .querySelector("[data-oce-close]")
    ?.addEventListener("click", closeDialog);
  document.querySelector("[data-oce-save]")?.addEventListener("click", () => {
    const choices = {};
    document.querySelectorAll("[data-oce-category-input]").forEach((input) => {
      choices[input.dataset.oceCategoryInput] = input.checked;
    });
    saveConsent(choices);
  });

  document
    .querySelector("[data-oce-withdraw]")
    ?.addEventListener("click", () => {
      saveConsent({});
      window.location.reload();
    });

  const currentConsent = readConsent();
  renderState(currentConsent);
  activateAllowedScripts(currentConsent);
  updateGoogleConsent(currentConsent);
})();
