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
  let dialogOpener = null;
  let closeTimer = null;
  let scriptActivationQueue = Promise.resolve();
  const animations = new WeakMap();
  const reducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)");

  const animateSurface = (element, visible, kind) => {
    if (element.hidden === !visible) return;

    const currentAnimation = animations.get(element);
    if (currentAnimation) currentAnimation.cancel();
    element.hidden = false;
    element.inert = !visible;
    element.setAttribute("aria-hidden", String(!visible));

    if (reducedMotion.matches || typeof element.animate !== "function") {
      element.hidden = !visible;
      return;
    }

    const frames =
      kind === "button"
        ? visible
          ? [
              { opacity: 0, transform: "scale(.94)" },
              { opacity: 1, transform: "scale(1)" },
            ]
          : [
              { opacity: 1, transform: "scale(1)" },
              { opacity: 0, transform: "scale(.94)" },
            ]
        : visible
          ? [
              { opacity: 0, transform: "translateY(14px)" },
              { opacity: 1, transform: "translateY(0)" },
            ]
          : [
              { opacity: 1, transform: "translateY(0)" },
              { opacity: 0, transform: "translateY(14px)" },
            ];
    const animation = element.animate(frames, {
      duration: 190,
      easing: "ease-out",
    });
    animations.set(element, animation);
    const finish = () => {
      if (animations.get(element) !== animation) return;
      if (!visible) element.hidden = true;
      animations.delete(element);
      animation.cancel();
    };
    animation.onfinish = finish;
    // Some Web Animation implementations do not reliably dispatch `finish`
    // while a page is backgrounded. The timer preserves the final semantic
    // state even in that case.
    window.setTimeout(finish, 200);
  };

  const finishDialogClose = () => {
    window.clearTimeout(closeTimer);
    dialog.classList.remove("is-open", "is-closing");
    if (dialog.open && typeof dialog.close === "function") {
      dialog.close();
    } else {
      dialog.removeAttribute("open");
    }
    const returnTarget = settingsButton.hidden ? dialogOpener : settingsButton;
    if (returnTarget && !returnTarget.hidden) returnTarget.focus();
  };

  const closeDialog = () => {
    const isOpen =
      typeof dialog.open === "boolean"
        ? dialog.open
        : dialog.hasAttribute("open");
    if (!isOpen) return;
    if (reducedMotion.matches) {
      finishDialogClose();
      return;
    }

    dialog.classList.remove("is-open");
    dialog.classList.add("is-closing");
    closeTimer = window.setTimeout(finishDialogClose, 190);
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
    animateSurface(banner, !hasChoice, "banner");
    animateSurface(settingsButton, hasChoice, "button");

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
          blocked.dataset.oceActivationQueued
        )
          return;

        blocked.dataset.oceActivationQueued = "true";
        scriptActivationQueue = scriptActivationQueue
          .then(
            () =>
              new Promise((resolve) => {
                if (!blocked.isConnected) {
                  resolve();
                  return;
                }

                let settled = false;
                let timeoutId = null;
                const complete = () => {
                  if (settled) return;
                  settled = true;
                  if (timeoutId) window.clearTimeout(timeoutId);
                  resolve();
                };

                const script = document.createElement("script");
                Array.from(blocked.attributes).forEach((attribute) => {
                  if (
                    ![
                      "type",
                      "data-oce-consent-category",
                      "data-oce-original-type",
                      "data-oce-activation-queued",
                    ].includes(attribute.name)
                  ) {
                    script.setAttribute(attribute.name, attribute.value);
                  }
                });
                script.type =
                  blocked.dataset.oceOriginalType || "text/javascript";
                script.textContent = blocked.textContent;

                if (script.hasAttribute("src")) {
                  if (!script.hasAttribute("async")) script.async = false;
                  script.addEventListener("load", complete, { once: true });
                  script.addEventListener("error", complete, { once: true });
                  // A stalled third-party response must not indefinitely block
                  // every later consent-gated script in the activation queue.
                  timeoutId = window.setTimeout(complete, 10000);
                }

                blocked.replaceWith(script);
                if (!script.hasAttribute("src")) complete();
              }),
          )
          .catch(() => {});
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
    const previousConsent = readConsent();
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
    else if (persisted && !settingsButton.hidden) settingsButton.focus();

    const revokedCategory =
      previousConsent &&
      Object.keys(previousConsent.categories).some(
        (id) =>
          previousConsent.categories[id] &&
          !categories[id] &&
          !config.categories[id]?.required,
      );
    if (persisted && revokedCategory) window.location.reload();
  };

  const openPreferences = (opener) => {
    renderState(readConsent());
    dialogOpener = opener || settingsButton;
    dialog.classList.remove("is-closing");
    if (typeof dialog.showModal === "function") dialog.showModal();
    else {
      dialog.setAttribute("open", "");
      dialog.setAttribute("aria-modal", "true");
    }

    window.requestAnimationFrame(() => {
      dialog.classList.add("is-open");
      dialog.querySelector("[data-oce-close]")?.focus();
    });
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
      button.addEventListener("click", (event) =>
        openPreferences(event.currentTarget),
      );
    });

  document
    .querySelector("[data-oce-close]")
    ?.addEventListener("click", closeDialog);
  dialog.addEventListener("cancel", (event) => {
    event.preventDefault();
    closeDialog();
  });
  dialog.addEventListener("close", () => {
    dialog.classList.remove("is-open", "is-closing");
  });
  dialog.addEventListener("click", (event) => {
    if (event.target === dialog) closeDialog();
  });
  dialog.addEventListener("keydown", (event) => {
    if (event.key === "Escape" && typeof dialog.close !== "function") {
      event.preventDefault();
      closeDialog();
      return;
    }
    if (event.key !== "Tab") return;
    const focusable = Array.from(
      dialog.querySelectorAll(
        'a[href], button:not([disabled]), input:not([disabled]), summary, [tabindex]:not([tabindex="-1"])',
      ),
    ).filter(
      (element) =>
        !element.hidden && element.getAttribute("aria-hidden") !== "true",
    );
    if (!focusable.length) {
      event.preventDefault();
      return;
    }

    const first = focusable[0];
    const last = focusable[focusable.length - 1];
    if (event.shiftKey && document.activeElement === first) {
      event.preventDefault();
      last.focus();
    } else if (!event.shiftKey && document.activeElement === last) {
      event.preventDefault();
      first.focus();
    }
  });
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
    });

  const currentConsent = readConsent();
  if (currentConsent) {
    // The early head script prevents a visual flash. Set matching semantic
    // state before the first render so assistive technology sees the same UI.
    banner.hidden = true;
    banner.inert = true;
    banner.setAttribute("aria-hidden", "true");
    settingsButton.hidden = false;
    settingsButton.inert = false;
    settingsButton.setAttribute("aria-hidden", "false");
  } else {
    document.documentElement.classList.remove("oce-consent-recorded");
  }
  renderState(currentConsent);
  activateAllowedScripts(currentConsent);
  updateGoogleConsent(currentConsent);
})();
