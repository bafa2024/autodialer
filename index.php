<?php
// Start output buffering
ob_start("ob_gzhandler");

// Set headers for caching and compression
header("Content-Type: text/html; charset=UTF-8");
header("Vary: Accept-Encoding");
header("Cache-Control: max-age=3600, public");
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="AutoDial Pro - Enterprise Auto Dialer Solution">
    <title>AutoDial Pro - Enterprise Auto Dialer Solution</title>
    
    <!-- Preload critical resources -->
    <link rel="preload" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <link rel="preload" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <link rel="preload" href="css/styles.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    
    <!-- Fallback for non-JS browsers -->
    <noscript>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="css/styles.min.css">
    </noscript>
</head>
<body>
    <!-- Main Content Container -->
    <div id="app">
        <?php include 'components/landing-page.php'; ?>
    </div>

    <!-- Load scripts at the end of the body -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous" defer></script>
    <script src="js/main.js" defer></script>
    
    <!-- Lazy load non-critical components -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Lazy load components when needed
        const lazyLoadComponent = async (componentId, componentName) => {
            const container = document.getElementById(componentId);
            if (container && !container.hasAttribute('data-loaded')) {
                try {
                    const response = await fetch(`load_component.php?component=${componentName}`);
                    if (response.ok) {
                        container.innerHTML = await response.text();
                        container.setAttribute('data-loaded', 'true');
                    }
                } catch (error) {
                    console.error('Error loading component:', error);
                }
            }
        };

        // Example: Load call summarization when the section is about to be viewed
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    lazyLoadComponent('call-summarization-section', 'call-summarization');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1 });

        // Observe sections that should be lazy-loaded
        const sections = ['call-summarization-section', 'contact-support-section'];
        sections.forEach(id => {
            const el = document.getElementById(id);
            if (el) observer.observe(el);
        });
    });
    </script>
</body>
</html>
<?php
// Flush the output buffer
ob_end_flush();
?>
