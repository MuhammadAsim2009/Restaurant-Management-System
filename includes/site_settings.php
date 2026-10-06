<?php
require_once __DIR__ . '/db.php';

/**
 * Fetch all site settings and return as a key-value pair array
 */
function getSiteSettings($pdo)
{
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
$site_name = $SETTINGS['site_name'] ?? 'Restaurant';
$primary_color = $SETTINGS['primary_color'] ?? '#FF2020';
$accent_color = $SETTINGS['accent_color'] ?? '#FFB800';
$bg_color = $SETTINGS['bg_color'] ?? '#161616';
$surface_color = $SETTINGS['surface_color'] ?? '#1A1A1A';
$font_display = $SETTINGS['font_display'] ?? 'Bebas Neue';
$font_primary = $SETTINGS['font_primary'] ?? 'Barlow';

// Light Mode Defaults
$SETTINGS['light_bg_color'] = $SETTINGS['light_bg_color'] ?? '#F8FAFC';
$SETTINGS['light_surface_color'] = $SETTINGS['light_surface_color'] ?? '#FFFFFF';
$SETTINGS['light_primary_color'] = $SETTINGS['light_primary_color'] ?? $primary_color;
$SETTINGS['light_accent_color'] = $SETTINGS['light_accent_color'] ?? $accent_color;
$SETTINGS['light_text_color'] = $SETTINGS['light_text_color'] ?? '#0F172A';

/**
 * Output dynamic CSS based on site settings
 */
function renderDynamicCSS($SETTINGS)
{
    $primary = $SETTINGS['primary_color'] ?? '#FF2020';
    $accent = $SETTINGS['accent_color'] ?? '#FFB800';
    $bg = $SETTINGS['bg_color'] ?? '#161616';
    $surface = $SETTINGS['surface_color'] ?? '#1A1A1A';
    $font_display = $SETTINGS['font_display'] ?? 'Bebas Neue';
    $font_primary = $SETTINGS['font_primary'] ?? 'Barlow';

    // Light mode values from settings or defaults
    $l_bg = $SETTINGS['light_bg_color'] ?? '#F8FAFC';
    $l_surface = $SETTINGS['light_surface_color'] ?? '#FFFFFF';
    $l_primary = $SETTINGS['light_primary_color'] ?? $primary;
    $l_accent = $SETTINGS['light_accent_color'] ?? $accent;
    $l_text = $SETTINGS['light_text_color'] ?? '#0F172A';

    echo "
    <style>
        :root {
            --k86-red: $primary;
            --k86-amber: $accent;
            --k86-bg: $bg;
            --k86-surface: $surface;
            --k86-text: #FFFFFF;
            --k86-text-muted: #AAAAAA;
        }

        .light-mode {
            --k86-bg: $l_bg;
            --k86-surface: $l_surface;
            --k86-red: $l_primary;
            --k86-amber: $l_accent;
            --k86-text: $l_text;
            --k86-text-muted: #64748B;
        }
        
        /* Font overrides */
        .font-display { font-family: '$font_display', cursive !important; }
        body, .font-body { font-family: '$font_primary', sans-serif !important; }
        
        /* Utility overrides using variables */
        .text-k86-red { color: var(--k86-red) !important; }
        .bg-k86-red { background-color: var(--k86-red) !important; }
        .border-k86-red { border-color: var(--k86-red) !important; }
        
        .text-k86-amber { color: var(--k86-amber) !important; }
        .bg-k86-amber { background-color: var(--k86-amber) !important; }
        
        .bg-k86-black { background-color: var(--k86-bg) !important; }
        .bg-k86-surface { background-color: var(--k86-surface) !important; }
        .text-white { color: var(--k86-text) !important; }
        .text-k86-muted { color: var(--k86-text-muted) !important; }
        
        /* Dynamic Shadow and Glow effects */
        .shadow-k86-red\/20 { box-shadow: 0 10px 15px -3px rgba(" . hexToRgb($primary) . ", 0.2); }
    </style>
    ";
}

function hexToRgb($hex)
{
    $hex = str_replace("#", "", $hex);
    if (strlen($hex) == 3) {
        $r = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
        $g = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
        $b = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
    } else {
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
    }
    return "$r, $g, $b";
}
?>