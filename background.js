console.log("[Ad Blocker] Service worker active.");

// Optional: Log when rules are applied
chrome.declarativeNetRequest.getDynamicRules((rules) => {
  console.log(`[Ad Blocker] Loaded ${rules.length} dynamic rules.`);
});
