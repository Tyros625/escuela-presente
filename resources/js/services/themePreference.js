export const THEME_STORAGE_KEY = "ep-color-scheme";

const VALID_PREFERENCES = new Set(["light", "dark", "system"]);

export function readStoredTheme() {
  try {
    const value = localStorage.getItem(THEME_STORAGE_KEY);
    if (VALID_PREFERENCES.has(value)) {
      return value;
    }
  } catch (_) {
    /* ignore */
  }
  return "light";
}

export function resolveDarkMode(preference) {
  if (preference === "dark") {
    return true;
  }
  if (preference === "system") {
    return (
      window.matchMedia &&
      window.matchMedia("(prefers-color-scheme: dark)").matches
    );
  }
  return false;
}

export function preferenceFromSettings(settings) {
  if (settings.darkModeSystem) {
    return "system";
  }
  return settings.darkMode ? "dark" : "light";
}

export function persistThemePreference(settings) {
  try {
    localStorage.setItem(THEME_STORAGE_KEY, preferenceFromSettings(settings));
  } catch (_) {
    /* ignore */
  }
}

export function syncDocumentTheme(isDark) {
  document.documentElement.classList.toggle("ep-dark", isDark);
  document.documentElement.dataset.colorScheme = isDark ? "dark" : "light";
}

export function initThemeFromStorage(store) {
  const preference = readStoredTheme();

  if (preference === "system") {
    store.darkModeSystem({ mode: "on" });
  } else {
    store.darkModeSystem({ mode: "off" });
    store.darkMode({ mode: preference === "dark" ? "on" : "off" });
  }

  syncDocumentTheme(store.settings.darkMode);
}
