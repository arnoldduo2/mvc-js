/**
 * Main Application Module
 *
 * Initializes and manages the SPA application
 */

import Router from "./app-router.js";

class App {
  constructor() {
    this.router = null;
    this.config = {
      appContainer: "app",
      loaderElement: "spa-loader",
      executeDelay: 100,
    };
  }

  /**
   * Initialize the application
   */
  init() {
    console.log("🚀 Initializing SPA Application...");

    // Initialize dark mode
    this.initDarkMode();

    // Initialize router
    this.router = new Router(this.config);

    // Attach to window for global access
    window.app = this;
    window.router = this.router;

    // Setup global event listeners
    this.setupEventListeners();

    console.log("✅ SPA Application ready!");
  }

  /**
   * Initialize dark mode from localStorage
   */
  initDarkMode() {
    const darkMode = localStorage.getItem("darkMode") === "true";
    if (darkMode) {
      document.body.classList.add("dark-mode");
    }
  }

  /**
   * Setup global event listeners
   */
  setupEventListeners() {
    // Listen for page load events
    window.addEventListener("pageLoaded", (e) => {
      console.log("📄 Page loaded:", e.detail.url);
      this.onPageLoaded(e.detail);
    });

    // Listen for page load errors
    window.addEventListener("pageLoadError", (e) => {
      console.error("❌ Page load error:", e.detail.error);
      this.onPageLoadError(e.detail);
    });

    // Handle form submissions (optional)
    document.addEventListener("submit", (e) => {
      const form = e.target;
      if (form.hasAttribute("data-spa-form")) {
        e.preventDefault();
        this.handleFormSubmit(form);
      }
    });

    // Dark mode toggle
    document.addEventListener("click", (e) => {
      if (
        e.target.id === "darkModeToggle" ||
        e.target.closest("#darkModeToggle")
      ) {
        this.toggleDarkMode();
      }
    });
  }

  /**
   * Toggle dark mode
   */
  toggleDarkMode() {
    const isDark = document.body.classList.toggle("dark-mode");
    localStorage.setItem("darkMode", isDark);

    // Update button icon
    const button = document.getElementById("darkModeToggle");
    if (button) {
      button.textContent = isDark ? "☀️" : "🌙";
    }

    console.log(`🎨 Dark mode ${isDark ? "enabled" : "disabled"}`);
  }

  /**
   * Handle page loaded event
   */
  onPageLoaded(detail) {
    // You can add custom logic here
    // For example: analytics tracking, updating active nav links, etc.
    this.updateActiveNavLinks(detail.url);
  }

  /**
   * Handle page load error event
   */
  onPageLoadError(detail) {
    // You can add custom error handling here
    // For example: show a user-friendly error page
  }

  /**
   * Update active navigation links
   */
  updateActiveNavLinks(currentUrl) {
    const links = document.querySelectorAll(".nav-links a");
    links.forEach((link) => {
      const href = link.getAttribute("href");
      if (
        href === currentUrl ||
        (currentUrl !== "/" && href !== "/" && currentUrl.startsWith(href))
      ) {
        link.classList.add("active");
      } else {
        link.classList.remove("active");
      }
    });
  }

  /**
   * Handle SPA form submissions
   */
  async handleFormSubmit(form) {
    const formData = new FormData(form);
    const action = form.getAttribute("action") || window.location.pathname;
    const method = form.getAttribute("method") || "POST";

    try {
      const response = await fetch(action, {
        method: method.toUpperCase(),
        headers: {
          "X-Requested-With": "XMLHttpRequest",
        },
        body: formData,
      });

      const data = await response.json();

      if (data.redirect) {
        this.router.navigate(data.redirect);
      } else if (data.content) {
        this.router.updatePage(data);
      }
    } catch (error) {
      console.error("Form submission error:", error);
    }
  }

  /**
   * Navigate to a URL
   */
  navigate(url) {
    this.router.navigate(url);
  }

  /**
   * Reload current page
   */
  reload() {
    this.router.reload();
  }
}

// Initialize app when DOM is ready
if (document.readyState === "loading") {
  document.addEventListener("DOMContentLoaded", () => {
    const app = new App();
    app.init();
  });
} else {
  const app = new App();
  app.init();
}

export default App;
