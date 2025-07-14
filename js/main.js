/**
 * Main JavaScript functionality for AutoDial Pro
 * Handles page navigation, component loading, and UI interactions
 */

// Global state
const APP_STATE = {
    currentPage: 'landing-page',
    loadedComponents: new Set()
};

/**
 * Show a specific page and handle component loading
 * @param {string} pageId - The ID of the page to show
 */
function showPage(pageId) {
    // Update active page in state
    APP_STATE.currentPage = pageId;
    
    // Update UI
    document.querySelectorAll('.page-content').forEach(page => {
        page.classList.remove('active');
    });
    
    const targetPage = document.getElementById(pageId);
    if (targetPage) {
        targetPage.classList.add('active');
        
        // Lazy load components for the page if needed
        if (pageId === 'dashboard-page') {
            loadComponent('call-summarization');
        }
    }
    
    // Close mobile menu if open
    const navbar = document.getElementById('navbarNav');
    if (navbar && navbar.classList.contains('show')) {
        new bootstrap.Collapse(navbar);
    }
    
    // Scroll to top
    window.scrollTo(0, 0);
}

/**
 * Show a specific dashboard section
 * @param {string} sectionId - The ID of the section to show (without '-section' suffix)
 * @param {HTMLElement} element - The clicked element (for UI updates)
 */
function showDashboardSection(sectionId, element) {
    // Update UI
    document.querySelectorAll('.dashboard-section').forEach(section => {
        section.style.display = 'none';
    });
    
    const targetSection = document.getElementById(`${sectionId}-section`);
    if (targetSection) {
        targetSection.style.display = 'block';
        
        // Lazy load the section content if not already loaded
        if (!targetSection.hasAttribute('data-loaded')) {
            loadComponent(sectionId);
        }
    }
    
    // Update active state in sidebar
    document.querySelectorAll('.sidebar-link').forEach(link => {
        link.classList.remove('active');
    });
    
    if (element) {
        element.classList.add('active');
    }
}

/**
 * Toggle the sidebar on mobile view
 */
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.querySelector('.main-content');
    
    if (sidebar && mainContent) {
        sidebar.classList.toggle('active');
        mainContent.classList.toggle('active');
    }
}

/**
 * Load a component asynchronously
 * @param {string} componentName - The name of the component to load
 * @returns {Promise<void>}
 */
async function loadComponent(componentName) {
    const componentId = `${componentName}-section`;
    const container = document.getElementById(componentId);
    
    if (!container || container.hasAttribute('data-loading')) {
        return;
    }
    
    // Mark as loading to prevent duplicate requests
    container.setAttribute('data-loading', 'true');
    container.innerHTML = '<div class="text-center p-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div><p class="mt-2">Loading...</p></div>';
    
    try {
        const response = await fetch(`/autodialer/load_component.php?component=${componentName}`);
        if (response.ok) {
            const html = await response.text();
            container.innerHTML = html;
            container.setAttribute('data-loaded', 'true');
            container.removeAttribute('data-loading');
            
            // Initialize any dynamic content
            initializeDynamicContent(container);
        } else {
            throw new Error(`Failed to load component: ${componentName}`);
        }
    } catch (error) {
        console.error('Error loading component:', error);
        container.innerHTML = `
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                Failed to load component. Please try again later.
            </div>
        `;
        container.removeAttribute('data-loading');
    }
}

/**
 * Initialize dynamic content within a container
 * @param {HTMLElement} container - The container element to initialize
 */
function initializeDynamicContent(container = document) {
    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(container.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.forEach(tooltipTriggerEl => {
        new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Initialize popovers
    const popoverTriggerList = [].slice.call(container.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.forEach(popoverTriggerEl => {
        new bootstrap.Popover(popoverTriggerEl);
    });
}

// Initialize the application when the DOM is fully loaded
document.addEventListener('DOMContentLoaded', () => {
    // Initialize the current page
    showPage(APP_STATE.currentPage);
    
    // Set up intersection observer for lazy loading
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const componentId = entry.target.id.replace('-section', '');
                loadComponent(componentId);
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1 });
    
    // Observe all lazy-load sections
    document.querySelectorAll('[data-lazy-load]').forEach(section => {
        observer.observe(section);
    });
    
    // Initialize dynamic content on the initial page load
    initializeDynamicContent();
});
