<?php

class Avatar
{

    /**
     * Generate user initials and consistent background color
     * 
     * @param array $user User data containing first_name, last_name, name, user_id
     * @return array ['initials' => string, 'bg_color' => string]
     */
    public static function generateUserAvatar($user)
    {
        // Generate user initials
        $initials = '';
        $first_name = $user['first_name'] ?? '';
        $last_name = $user['last_name'] ?? '';
        $name = $user['name'] ?? '';

        if (!empty($first_name) && !empty($last_name)) {
            $initials = strtoupper(substr($first_name, 0, 1) . substr($last_name, 0, 1));
        } else {
            // Fallback to name field if first_name/last_name not available
            $name_parts = explode(' ', trim($name));
            if (count($name_parts) >= 2) {
                $initials = strtoupper(substr($name_parts[0], 0, 1) . substr($name_parts[1], 0, 1));
            } else {
                $initials = strtoupper(substr($name, 0, 2));
            }
        }

        // Generate background color based on user ID for consistency
        $colors = ['primary', 'success', 'danger', 'warning', 'info', 'dark'];
        $color_index = ($user['user_id'] ?? 0) % count($colors);
        $bg_color = $colors[$color_index];

        return [
            'initials' => $initials,
            'bg_color' => $bg_color
        ];
    }

    /**
     * Render avatar HTML
     * 
     * @param array $user User data
     * @param string $size Size class (sm, md, lg, xl) or custom size
     * @param array $options Additional options like custom classes
     * @return string HTML for avatar
     */
    public static function renderAvatar($user, $size = 'md', $options = [])
    {
        $avatar = self::generateUserAvatar($user);

        $sizeClasses = [
            'xs' => 'avatar-xs',
            'sm' => 'avatar-sm',
            'md' => '',
            'lg' => 'avatar-lg',
            'xl' => 'avatar-xl'
        ];

        $sizeClass = $sizeClasses[$size] ?? '';
        $customClasses = $options['classes'] ?? '';
        $isOnline = $options['online'] ?? true;
        $onlineClass = $isOnline ? 'avatar-online' : '';

        return sprintf(
            '<div class="avatar %s %s %s">
                <span class="bg-label-%s rounded-circle avatar-initial">%s</span>
            </div>',
            $sizeClass,
            $onlineClass,
            $customClasses,
            $avatar['bg_color'],
            $avatar['initials']
        );
    }
}
