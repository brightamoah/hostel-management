<?php

/**
 * Sanitize user input to prevent XSS attacks.
 * @param string $input
 * @return string
 */
function sanitizeInput($input)
{
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, "UTF-8");
}
