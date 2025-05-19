<?php

/**
 * Check if the current route matches or is a subpage of the menu item route
 * @param string $route_pattern The menu item route to check against
 * @param string $current_route The current URL path
 * @return boolean Whether the current route matches the pattern
 */
function isRouteActive($route_pattern, $current_route)
{
    // Exact match
    if ($current_route === $route_pattern) {
        return true;
    }

    // Check if current route is a subpage of the menu item
    // For example, /admin/announcements/create should match /admin/announcements
    if ($route_pattern !== '/' && strpos($current_route, $route_pattern) === 0) {
        // Make sure it's a proper subpath by checking for / or end of string
        $next_char_pos = strlen($route_pattern);
        if ($next_char_pos >= strlen($current_route) || $current_route[$next_char_pos] === '/') {
            return true;
        }
    }

    return false;
}
