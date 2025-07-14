<?php
/**
 * Loads a component file if it exists
 * @param string $component_name The name of the component to load
 * @return string The component's HTML or an error message
 */
function load_component($component_name) {
    $file_path = __DIR__ . "/components/" . $component_name . ".html";
    if (file_exists($file_path)) {
        return file_get_contents($file_path);
    }
    return "<!-- Component '$component_name' not found -->";
}

// If a component is requested via AJAX
if (isset($_GET['component'])) {
    header('Content-Type: text/html');
    echo load_component($_GET['component']);
    exit;
}
?>
