/**
 * SPA Router Module
 *
 * Handles client-side navigation without page reloads using ES6 modules
 */

export class Router {
  constructor(options = {}) {
    this.options = {
      appContainer: options.appContainer || "app",
      loaderElement: options.loaderElement || "spa-loader",
      executeDelay: options.executeDelay || 100,
      ...options,
    };

    this.basePath = window.APP_BASE_PATH || "";
    this.currentUrl = window.location.pathname;
    this.init();
  }

  /**
   * Initialize router - attach event listeners
   */
  init() {
    // Intercept all link clicks
    document.addEventListener("click", (e) => {
      const link = e.target.closest("a[href]");

      if (link && this.shouldIntercept(link)) {
        e.preventDefault();
        this.navigate(link.href);
      }
    });

    // Handle browser back/forward
    window.addEventListener("popstate", (e) => {
      if (e.state && e.state.url) {
        this.loadPage(e.state.url, false);
      }
    });

    // Store initial state
    window.history.replaceState({ url: this.currentUrl }, "", this.currentUrl);
  }

  /**
   * Determine if link should be intercepted for SPA navigation
   */
  shouldIntercept(link) {
    const href = link.getAttribute("href");

    // Don't intercept external links
    if (link.hostname !== window.location.hostname) {
      return false;
    }

    // Don't intercept links with target="_blank"
    if (link.target === "_blank") {
      return false;
    }

    // Don't intercept links with data-no-spa attribute
    if (link.hasAttribute("data-no-spa")) {
      return false;
    }

    // Don't intercept hash links
    if (href && href.startsWith("#")) {
      return false;
    }

    // Don't intercept download links
    if (link.hasAttribute("download")) {
      return false;
    }

    return true;
  }

  /**
   * Navigate to a new URL
   */
  navigate(url) {
    // Update browser history
    window.history.pushState({ url }, "", url);
    this.currentUrl = url;

    // Load the page
    this.loadPage(url, true);
  }

  /**
   * Load page content via AJAX
   */
  async loadPage(url, addToHistory = true) {
    try {
      // Show loading state
      this.showLoading();

      // Fetch page content
      const response = await fetch(url, {
        headers: {
          "X-Requested-With": "XMLHttpRequest",
          Accept: "application/json",
        },
      });

      if (!response.ok) {
        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
      }

      const data = await response.json();

      // Update page
      this.updatePage(data);

      // Trigger custom event
      this.triggerEvent("pageLoaded", { url, data });
    } catch (error) {
      console.error("Failed to load page:", error);
      this.showError(`Failed to load page: ${error.message}`);
      this.triggerEvent("pageLoadError", { url, error });
    } finally {
      this.hideLoading();
    }
  }

  /**
   * Update page content and metadata
   */
  updatePage(data) {
    // Check if this is an error response
    if (data.error || data.type === "error") {
      this.handleErrorResponse(data);
      return;
    }

    // Update page title
    if (data.title) {
      document.title = data.title;
    }

    // Update content
    if (data.content) {
      const appContainer = document.getElementById(this.options.appContainer);
      if (appContainer) {
        appContainer.innerHTML = data.content;
      } else {
        console.warn(`App container #${this.options.appContainer} not found`);
      }
    }

    // Execute scripts after delay
    if (
      data.scripts &&
      Array.isArray(data.scripts) &&
      data.scripts.length > 0
    ) {
      const delay = data.executeAfter || this.options.executeDelay;
      setTimeout(() => {
        this.executeScripts(data.scripts);
      }, delay);
    }

    // Scroll to top
    window.scrollTo({ top: 0, behavior: "smooth" });
  }

  /**
   * Handle error responses from server
   */
  handleErrorResponse(data) {
    console.error("Server error:", data);

    // Update page title
    if (data.title) {
      document.title = data.title;
    }

    // Display error content
    if (data.content) {
      const appContainer = document.getElementById(this.options.appContainer);
      if (appContainer) {
        appContainer.innerHTML = data.content;
      }
    } else {
      // Fallback error message
      const appContainer = document.getElementById(this.options.appContainer);
      if (appContainer) {
        appContainer.innerHTML = `
          <div class="error-page" style="padding: 2rem; text-align: center;">
            <h1>Error ${data.code || 500}</h1>
            <p>${data.message || "An error occurred"}</p>
            <a href="${this.basePath || "/"}" class="btn">Go Home</a>
          </div>
        `;
      }
    }

    // Trigger error event
    this.triggerEvent("pageError", { data });
  }

  /**
   * Execute page scripts
   */
  executeScripts(scripts) {
    scripts.forEach((scriptContent, index) => {
      try {
        const script = document.createElement("script");
        script.textContent = scriptContent;
        script.setAttribute("data-spa-script", index);
        document.body.appendChild(script);

        // Clean up after execution
        setTimeout(() => {
          document.body.removeChild(script);
        }, 100);
      } catch (error) {
        console.error("Script execution error:", error);
      }
    });
  }

  /**
   * Show loading indicator
   */
  showLoading() {
    const loader = document.getElementById(this.options.loaderElement);
    if (loader) {
      loader.style.display = "block";
    }
  }

  /**
   * Hide loading indicator
   */
  hideLoading() {
    const loader = document.getElementById(this.options.loaderElement);
    if (loader) {
      loader.style.display = "none";
    }
  }

  /**
   * Show error message
   */
  showError(message) {
    // You can customize this to show a nicer error notification
    console.error(message);

    // Optional: Show a toast notification or modal
    const appContainer = document.getElementById(this.options.appContainer);
    if (appContainer) {
      const errorDiv = document.createElement("div");
      errorDiv.className = "spa-error";
      errorDiv.textContent = message;
      errorDiv.style.cssText =
        "padding: 1rem; background: #f44336; color: white; margin: 1rem 0; border-radius: 4px;";
      appContainer.insertBefore(errorDiv, appContainer.firstChild);

      // Auto-remove after 5 seconds
      setTimeout(() => errorDiv.remove(), 5000);
    }
  }

  /**
   * Trigger custom events
   */
  triggerEvent(eventName, detail = {}) {
    const event = new CustomEvent(eventName, { detail });
    window.dispatchEvent(event);
  }

  /**
   * Programmatically navigate to a URL
   */
  go(url) {
    this.navigate(url);
  }

  /**
   * Go back in history
   */
  back() {
    window.history.back();
  }

  /**
   * Go forward in history
   */
  forward() {
    window.history.forward();
  }

  /**
   * Reload current page
   */
  reload() {
    this.loadPage(this.currentUrl, false);
  }
}

export default Router;
