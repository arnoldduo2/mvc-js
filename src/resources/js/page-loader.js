/**
 * Page Loader
 * 
 * Utility functions for loading page content
 */
class PageLoader {
    /**
     * Load a page via AJAX
     * 
     * @param {string} url - URL to load
     * @param {Function} onSuccess - Success callback
     * @param {Function} onError - Error callback
     */
    static async load(url, onSuccess, onError) {
        try {
            const response = await fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            
            if (onSuccess) {
                onSuccess(data);
            }
            
            return data;
        } catch (error) {
            if (onError) {
                onError(error);
            }
            throw error;
        }
    }
    
    /**
     * Load and update a specific container
     * 
     * @param {string} url - URL to load
     * @param {string} containerId - Container element ID
     */
    static async loadInto(url, containerId) {
        const container = document.getElementById(containerId);
        
        if (!container) {
            console.error(`Container #${containerId} not found`);
            return;
        }
        
        try {
            const data = await this.load(url);
            
            if (data.content) {
                container.innerHTML = data.content;
                
                // Execute scripts if provided
                if (data.scripts && data.scripts.length > 0) {
                    const delay = data.executeAfter || 100;
                    setTimeout(() => {
                        this.executeScripts(data.scripts);
                    }, delay);
                }
            }
        } catch (error) {
            console.error('Failed to load content:', error);
            container.innerHTML = '<p class="error">Failed to load content.</p>';
        }
    }
    
    /**
     * Execute scripts
     * 
     * @param {Array} scripts - Array of script strings
     */
    static executeScripts(scripts) {
        scripts.forEach(scriptContent => {
            try {
                const script = document.createElement('script');
                script.textContent = scriptContent;
                document.body.appendChild(script);
                document.body.removeChild(script);
            } catch (error) {
                console.error('Script execution error:', error);
            }
        });
    }
    
    /**
     * Show loading indicator
     * 
     * @param {string} containerId - Container element ID
     */
    static showLoading(containerId) {
        const container = document.getElementById(containerId);
        if (container) {
            container.innerHTML = '<div class="loading">Loading...</div>';
        }
    }
}

// Make available globally
window.PageLoader = PageLoader;
