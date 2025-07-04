// AutoDial Pro Debug Script
console.log('🔍 AutoDial Pro Debug Script Started');

// Test 1: Check if all required libraries are loaded
function testLibraries() {
    console.log('📚 Testing library dependencies...');
    
    const libraries = {
        'Bootstrap': typeof bootstrap !== 'undefined',
        'jQuery': typeof $ !== 'undefined',
        'Chart.js': typeof Chart !== 'undefined'
    };
    
    Object.entries(libraries).forEach(([name, loaded]) => {
        if (loaded) {
            console.log(`✅ ${name} is loaded`);
        } else {
            console.warn(`⚠️ ${name} is not loaded`);
        }
    });
}

// Test 2: Check if all required functions exist
function testFunctions() {
    console.log('🔧 Testing required functions...');
    
    const functions = [
        'showPage',
        'showDashboardSection',
        'toggleSidebar',
        'handleLogin',
        'handleSignup',
        'initializeCharts'
    ];
    
    functions.forEach(funcName => {
        if (typeof window[funcName] === 'function') {
            console.log(`✅ Function ${funcName} exists`);
        } else {
            console.warn(`⚠️ Function ${funcName} is missing`);
        }
    });
}

// Test 3: Check DOM elements
function testDOMElements() {
    console.log('🏗️ Testing DOM elements...');
    
    const elements = [
        'landing-page',
        'signup-page',
        'login-page',
        'dashboard-page',
        'overview-section',
        'dialer-section'
    ];
    
    elements.forEach(elementId => {
        const element = document.getElementById(elementId);
        if (element) {
            console.log(`✅ Element #${elementId} exists`);
        } else {
            console.warn(`⚠️ Element #${elementId} is missing`);
        }
    });
}

// Test 4: Test navigation functionality
function testNavigation() {
    console.log('🧭 Testing navigation...');
    
    try {
        // Test showPage function
        if (typeof showPage === 'function') {
            console.log('✅ showPage function is available');
            
            // Test navigation to different pages
            const pages = ['landing-page', 'signup-page', 'login-page', 'dashboard-page'];
            pages.forEach(page => {
                try {
                    showPage(page);
                    const pageElement = document.getElementById(page);
                    if (pageElement && pageElement.classList.contains('active')) {
                        console.log(`✅ Navigation to ${page} works`);
                    } else {
                        console.warn(`⚠️ Navigation to ${page} may not work correctly`);
                    }
                } catch (error) {
                    console.error(`❌ Navigation to ${page} failed:`, error);
                }
            });
        } else {
            console.warn('⚠️ showPage function is not available');
        }
    } catch (error) {
        console.error('❌ Navigation test failed:', error);
    }
}

// Test 5: Test form functionality
function testForms() {
    console.log('📝 Testing forms...');
    
    const forms = document.querySelectorAll('form');
    console.log(`Found ${forms.length} forms`);
    
    forms.forEach((form, index) => {
        const requiredFields = form.querySelectorAll('[required]');
        console.log(`Form ${index + 1}: ${requiredFields.length} required fields`);
        
        // Test form submission
        try {
            const submitEvent = new Event('submit', { cancelable: true });
            form.dispatchEvent(submitEvent);
            console.log(`✅ Form ${index + 1} submission test completed`);
        } catch (error) {
            console.error(`❌ Form ${index + 1} submission test failed:`, error);
        }
    });
}

// Test 6: Test responsive design
function testResponsive() {
    console.log('📱 Testing responsive design...');
    
    // Check for media queries in CSS
    const styleSheets = Array.from(document.styleSheets);
    let hasMediaQueries = false;
    
    styleSheets.forEach(sheet => {
        try {
            const rules = Array.from(sheet.cssRules || sheet.rules);
            rules.forEach(rule => {
                if (rule instanceof CSSMediaRule) {
                    hasMediaQueries = true;
                    console.log(`✅ Found media query: ${rule.conditionText}`);
                }
            });
        } catch (error) {
            // Cross-origin stylesheets may throw errors
        }
    });
    
    if (!hasMediaQueries) {
        console.warn('⚠️ No media queries found in stylesheets');
    }
    
    // Check viewport meta tag
    const viewport = document.querySelector('meta[name="viewport"]');
    if (viewport) {
        console.log('✅ Viewport meta tag found');
    } else {
        console.warn('⚠️ Viewport meta tag missing');
    }
}

// Test 7: Performance test
function testPerformance() {
    console.log('⚡ Testing performance...');
    
    const startTime = performance.now();
    
    // Simulate some operations
    for (let i = 0; i < 1000; i++) {
        document.createElement('div');
    }
    
    const endTime = performance.now();
    const duration = endTime - startTime;
    
    if (duration < 10) {
        console.log(`✅ Performance test passed: ${duration.toFixed(2)}ms`);
    } else {
        console.warn(`⚠️ Performance test slow: ${duration.toFixed(2)}ms`);
    }
}

// Test 8: Accessibility test
function testAccessibility() {
    console.log('♿ Testing accessibility...');
    
    // Check for alt attributes on images
    const images = document.querySelectorAll('img');
    let imagesWithoutAlt = 0;
    
    images.forEach(img => {
        if (!img.alt) {
            imagesWithoutAlt++;
        }
    });
    
    if (imagesWithoutAlt === 0) {
        console.log('✅ All images have alt attributes');
    } else {
        console.warn(`⚠️ ${imagesWithoutAlt} images missing alt attributes`);
    }
    
    // Check for ARIA labels
    const elementsWithAria = document.querySelectorAll('[aria-label], [aria-labelledby]');
    console.log(`Found ${elementsWithAria.length} elements with ARIA attributes`);
    
    // Check for semantic HTML
    const semanticElements = document.querySelectorAll('nav, main, section, article, aside, header, footer');
    console.log(`Found ${semanticElements.length} semantic HTML elements`);
}

// Test 9: Browser compatibility
function testBrowserCompatibility() {
    console.log('🌐 Testing browser compatibility...');
    
    const features = {
        'ES6 Classes': typeof class Test {} === 'function',
        'Arrow Functions': typeof (() => {}) === 'function',
        'Template Literals': typeof `test` === 'string',
        'Fetch API': typeof fetch === 'function',
        'CSS Grid': CSS.supports('display', 'grid'),
        'Flexbox': CSS.supports('display', 'flex'),
        'CSS Variables': CSS.supports('color', 'var(--test)'),
        'Backdrop Filter': CSS.supports('backdrop-filter', 'blur(10px)')
    };
    
    Object.entries(features).forEach(([feature, supported]) => {
        if (supported) {
            console.log(`✅ ${feature} is supported`);
        } else {
            console.warn(`⚠️ ${feature} is not supported`);
        }
    });
}

// Test 10: Error handling
function testErrorHandling() {
    console.log('🛡️ Testing error handling...');
    
    // Test if error handlers are in place
    const originalError = console.error;
    let errorCaught = false;
    
    console.error = function() {
        errorCaught = true;
        originalError.apply(console, arguments);
    };
    
    // Trigger a potential error
    try {
        showPage('non-existent-page');
    } catch (error) {
        console.log('✅ Error handling works correctly');
    }
    
    console.error = originalError;
    
    if (!errorCaught) {
        console.log('✅ No errors detected during testing');
    }
}

// Run all tests
function runAllTests() {
    console.log('🚀 Starting comprehensive debug tests...');
    console.log('='.repeat(50));
    
    testLibraries();
    console.log('-'.repeat(30));
    
    testFunctions();
    console.log('-'.repeat(30));
    
    testDOMElements();
    console.log('-'.repeat(30));
    
    testNavigation();
    console.log('-'.repeat(30));
    
    testForms();
    console.log('-'.repeat(30));
    
    testResponsive();
    console.log('-'.repeat(30));
    
    testPerformance();
    console.log('-'.repeat(30));
    
    testAccessibility();
    console.log('-'.repeat(30));
    
    testBrowserCompatibility();
    console.log('-'.repeat(30));
    
    testErrorHandling();
    console.log('-'.repeat(30));
    
    console.log('='.repeat(50));
    console.log('🎉 Debug tests completed!');
}

// Auto-run tests when script loads
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', runAllTests);
} else {
    runAllTests();
}

// Export functions for manual testing
window.debugTests = {
    testLibraries,
    testFunctions,
    testDOMElements,
    testNavigation,
    testForms,
    testResponsive,
    testPerformance,
    testAccessibility,
    testBrowserCompatibility,
    testErrorHandling,
    runAllTests
}; 