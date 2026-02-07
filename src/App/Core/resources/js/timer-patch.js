/**
 * Core Timer Patch
 *
 * Intercepts setInterval and setTimeout to track IDs for SPA cleanup.
 * Must be loaded before any other scripts.
 */
(function () {
  // Global storage for timer IDs
  window.__SPA_TIMERS__ = {
    intervals: new Set(),
    timeouts: new Set(),
  };

  const originalSetInterval = window.setInterval;
  const originalClearInterval = window.clearInterval;
  const originalSetTimeout = window.setTimeout;
  const originalClearTimeout = window.clearTimeout;

  // Override setInterval
  window.setInterval = function (callback, delay, ...args) {
    const id = originalSetInterval.call(window, callback, delay, ...args);
    window.__SPA_TIMERS__.intervals.add(id);
    return id;
  };

  // Override clearInterval
  window.clearInterval = function (id) {
    window.__SPA_TIMERS__.intervals.delete(id);
    return originalClearInterval.call(window, id);
  };

  // Override setTimeout
  window.setTimeout = function (callback, delay, ...args) {
    const id = originalSetTimeout.call(
      window,
      (...a) => {
        window.__SPA_TIMERS__.timeouts.delete(id);
        if (typeof callback === "function") {
          callback(...a);
        } else {
          // Handle string callbacks (eval) - though discouraged
          new Function(callback)(...a);
        }
      },
      delay,
      ...args,
    );
    window.__SPA_TIMERS__.timeouts.add(id);
    return id;
  };

  // Override clearTimeout
  window.clearTimeout = function (id) {
    window.__SPA_TIMERS__.timeouts.delete(id);
    return originalClearTimeout.call(window, id);
  };

  console.log("⏱️ SPA Timer Patch initialized");
})();
