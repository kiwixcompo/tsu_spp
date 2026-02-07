<?php

/**
 * Text Helper Functions
 */

if (!function_exists('escape_html')) {
    /**
     * Escape HTML but preserve common characters like apostrophes
     */
    function escape_html($text) {
        if (empty($text)) {
            return '';
        }
        
        // Use htmlspecialchars with ENT_NOQUOTES to avoid encoding apostrophes
        // This preserves apostrophes while still escaping dangerous HTML
        $escaped = htmlspecialchars($text, ENT_NOQUOTES, 'UTF-8');
        
        // Only escape quotes when necessary for attributes
        return $escaped;
    }
}

if (!function_exists('escape_attr')) {
    /**
     * Escape text for use in HTML attributes
     */
    function escape_attr($text) {
        if (empty($text)) {
            return '';
        }
        
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('safe_output')) {
    /**
     * Safe output for display - escapes HTML but keeps text readable
     */
    function safe_output($text) {
        return escape_html($text);
    }
}