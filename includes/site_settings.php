<?php
require_once __DIR__ . '/db.php';

/**
 * Fetch all site settings and return as a key-value pair array
 */
function getSiteSettings($pdo) {
    try {
        $stmt = $pdo->query("SELECT `key`, `value` FROM site_settings");
        return $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    } catch (Exception $e) {
        return [];
    }
}

// Global settings array
$SETTINGS = getSiteSettings($pdo);

// Default values for common settings (fallback)
$site_name = $SETTINGS['site_name'] ?? 'Kitchen 86';
$primary_color = $SETTINGS['primary_color'] ?? '#FF2020';
$accent_color = $SETTINGS['accent_color'] ?? '#FFB800';
$bg_color = $SETTINGS['bg_color'] ?? '#161616';
$surface_color = $SETTINGS['surface_color'] ?? '#1A1A1A';
$font_display = $SETTINGS['font_display'] ?? 'Bebas Neue';
$font_primary = $SETTINGS['font_primary'] ?? 'Barlow';

/**
 * Output dynamic CSS based on site settings
 */
function renderDynamicCSS($SETTINGS) {
    $primary = $SETTINGS['primary_color'] ?? '#FF2020';
    $accent = $SETTINGS['accent_color'] ?? '#FFB800';
    $bg = $SETTINGS['bg_color'] ?? '#161616';
    $surface = $SETTINGS['surface_color'] ?? '#1A1A1A';
    $font_display = $SETTINGS['font_display'] ?? 'Bebas Neue';
    $font_primary = $SETTINGS['font_primary'] ?? 'Barlow';
    
    echo "
    <style>
        :root {
            --k86-red: $primary;
            --k86-amber: $accent;
            --k86-black: $bg;
            --k86-surface: $surface;
        }
        
        /* Font overrides if dynamic fonts are selected */
        .font-display { font-family: '$font_display', cursive !important; }
        body, .font-body { font-family: '$font_primary', sans-serif !important; }
        
        /* Tailwind custom class overrides */
        .text-k86-red { color: $primary !important; }
        .bg-k86-red { background-color: $primary !important; }
        .border-k86-red { border-color: $primary !important; }
        
        .text-k86-amber { color: $accent !important; }
        .bg-k86-amber { background-color: $accent !important; }
        
        .bg-k86-black { background-color: $bg !important; }
        .bg-k86-surface { background-color: $surface !important; }
        
        /* Dynamic Shadow and Glow effects */
        .shadow-k86-red\/20 { shadow: 0 10px 15px -3px rgba(" . hexToRgb($primary) . ", 0.2); }
        .drop-shadow-\[0_0_8px_rgba\(255\,32\,32\,0\.5\)\] { filter: drop-shadow(0 0 8px rgba(" . hexToRgb($primary) . ", 0.5)); }
    </style>
    ";
}

function hexToRgb($hex) {
    $hex = str_replace("#", "", $hex);
    if(strlen($hex) == 3) {
        $r = hexdec(substr($hex,0,1).substr($hex,0,1));
        $g = hexdec(substr($hex,1,1).substr($hex,1,1));
        $b = hexdec(substr($hex,2,1).substr($hex,2,1));
    } else {
        $r = hexdec(substr($hex,0,2));
        $g = hexdec(substr($hex,2,2));
        $b = hexdec(substr($hex,4,2));
    }
    return "$r, $g, $b";
}
?>
