/**
 * SPA Router
 * 
 * Handles client-side navigation without page reloads
 */
class SPARouter {
    constructor() {
        this.init();
    }

    init() {
        // Intercept all link clicks
        document.addEventListener('click', (e) => {
            const link = e.target.closest('a[href]');
            
            if (link && this.shouldIntercept(link)) {
                e.preventDefault();
                this.navigate(link.href);
            }
        });

        // Handle browser back/forward
        window.addEventListener('popstate', (e) => {
            if (e.state && e.state.url) {
                this.loadPage(e.state.url, false);
            }
        });
    }

    shouldIntercept(link) {
        const href = link.getAttribute('href');
        
        // Don't intercept external links
        if (link.hostname !== window.location.hostname) {
            return false;
        }
        
        // Don't intercept links with target="_blank"
        if (link.target === '_blank') {
            return false;
        }
        
        // Don't intercept links with data-no-spa attribute
        if (link.hasAttribute('data-no-spa')) {
            return false;
        }
        
        // Don't intercept hash links
        if (href.startsWith('#')) {
            return false;
        }
        
        return true;
    }

    navigate(url) {
        // Update browser history
        window.history.pushState({ url }, '', url);
        
        // Load the page
        this.loadPage(url, true);
    }

    async loadPage(url, addToHistory = true) {
        try {
            // Show loading state
            this.showLoading();
            
            // Fetch page content
            const response = await fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            
            // Update page
            this.updatePage(data);
            
        } catch (error) {
            console.error('Failed to load page:', error);
            this.showError('Failed to load page. Please try again.');
        } finally {
            this.hideLoading();
        }
    }

    updatePage(data) {
        // Update page title
        if (data.title) {
            document.title = data.title;
        }
        
        // Update content
        if (data.content) {
            const appContainer = document.getElementById('app');
            if (appContainer) {
                appContainer.innerHTML = data.content;
            }
        }
        
        // Execute scripts after delay
        if (data.scripts && data.scripts.length > 0) {
            const delay = data.executeAfter || 100;
            setTimeout(() => {
                this.executeScripts(data.scripts);
            }, delay);
        }
        
        // Scroll to top
        window.scrollTo(0, 0);
    }

    executeScripts(scripts) {
        scripts.forEach(scriptContent => {
            try {
                // Create and execute script
                const script = document.createElement('script');
                script.textContent = scriptContent;
                document.body.appendChild(script);
                document.body.removeChild(script);
            } catch (error) {
                console.error('Script execution error:', error);
            }
        });
    }

    showLoading() {
        const loader = document.getElementById('spa-loader');
        if (loader) {
            loader.style.display = 'block';
        }
    }

    hideLoading() {
        const loader = document.getElementById('spa-loader');
        if (loader) {
            loader.style.display = 'none';
        }
    }

    showError(message) {
        // You can customize this to show a nicer error message
        alert(message);
    }
}

// Initialize SPA router when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        window.spaRouter = new SPARouter();
    });
} else {
    window.spaRouter = new SPARouter();
}
