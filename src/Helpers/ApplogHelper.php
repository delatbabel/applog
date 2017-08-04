<?php
/**
 * Applog Helper
 *
 * @author     Del
 */

namespace Delatbabel\Applog\Helpers;

use Illuminate\Support\Facades\Request;

/**
 * Applog Helper
 */
class ApplogHelper
{

    /**
     * Get the currently logged in user name.
     *
     * @return string
     */
    public static function currentUserName()
    {
        if (function_exists('\get_user_name')) {
            $username = \get_user_name();
        }
        if (empty($username)) {
            $username   = \get_current_user();
        }
        if (empty($username)) {
            $username   = Request::header('php-auth-user');
        }
        if (empty($username)) {
            $username   = 'system';
        }

        return $username;
    }

    /**
     * Get the list of client IP addresses.
     *
     * @return string
     */
    public static function getClientIps()
    {
        // Get the list of client IP addresses by first setting the
        // list of trusted proxies.
        // Request::setTrustedProxies(['reverse.proxies.go.here']);
        $clientIps = Request::getClientIps();
        $clientIp  = implode(', ', $clientIps);

        // Also get any addresses in _SERVER["HTTP_X_FORWARDED_FOR"]
        $forwardAddress = isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : '';
        if (empty($clientIp)) {
            $clientIp = $forwardAddress;
        } elseif (! empty($forwardAddress)) {
            $clientIp = $clientIp . ',' . $forwardAddress;
        }

        return $clientIp;
    }

    public static function detectCreatePermission($model) {
        return true;
    }

    public static function detectUpdatePermission($model) {
        return true;
    }

    public static function detectDeletePermission($model) {
        return true;
    }
}
