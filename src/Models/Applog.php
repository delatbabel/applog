<?php
/**
 * Applog model
 *
 * @author     Del
 */

namespace Delatbabel\Applog\Models;

use Delatbabel\Applog\Helpers\ApplogHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

/**
 * Applog model
 */
class Applog extends Model
{
    // DO NOT use AuditableTrait or you will cause an endless loop and fill the database!

    public $fillable = ['type', 'modelname', 'foreign_id', 'classname', 'traitname', 'functionname',
        'filename', 'linenumber', 'message', 'details', 'ipaddr', 'created_by', 'updated_by'];

    /**
     * Log a message to the application log.
     *
     * Can be called directly or from the Laravel log listener.
     *
     * @param string $level
     * @param string $message
     * @param mixed $context
     * @return void
     */
    public static function log($level, $message, $context = [])
    {
        // Throw out debug messages if we are not in debug mode
        if (($level == 'debug') && (Config::get('app.debug') != true)) {
            return;
        }

        // Fetch the currently logged in user
        $username = ApplogHelper::currentUserName();

        // Get the list of client IP addresses
        $clientIp = ApplogHelper::getClientIps();

        // Split the log message to see how it is formatted.
        $logdata = explode(':', $message, 6);
        if (count($logdata) == 6) {
            list($classname, $traitname, $filename, $linenumber, $functionname, $message) = $logdata;
        } else {
            list($classname, $traitname, $filename, $linenumber, $functionname, $message) =
                ['', '', '', '', '', $message];
        }

        // Store the log entry.
        try {
            $error = static::create([
                'type'          => $level,
                // 'modelname'    => get_class($target),
                // 'foreign_id'   => $target->id,
                'classname'     => $classname,
                'traitname'     => $traitname,
                'filename'      => $filename,
                'linenumber'    => $linenumber,
                'functionname'  => $functionname,
                'message'       => $message,
                'details'       => json_encode($context),
                'ipaddr'        => $clientIp,
                'created_by'    => $username,
                'updated_by'    => $username,
            ]);

            // Send an email out for error messages -- example code below.
            /*
            if ($level == 'error') {
                MandrillMailerRepository::sendError(env('MAIL_TO_ADDRESS'), env('MAIL_TO_NAME'), $error);
            }
            */
        } catch (\Exception $e) {
            // Do nothing if the log fails -- possibly fatal database error
        }
    }

    /**
     * Log an emergency message to the application log.
     *
     * @param string $message
     * @param mixed $context
     * @return void
     */
    public static function emergency($message, $context = [])
    {
        static::log('emergency', $message, $context);
    }

    /**
     * Log an alert message to the application log.
     *
     * @param string $message
     * @param mixed $context
     * @return void
     */
    public static function alert($message, $context = [])
    {
        static::log('alert', $message, $context);
    }

    /**
     * Log a critical message to the application log.
     *
     * @param string $message
     * @param mixed $context
     * @return void
     */
    public static function critical($message, $context = [])
    {
        static::log('critical', $message, $context);
    }

    /**
     * Log an error message to the application log.
     *
     * @param string $message
     * @param mixed $context
     * @return void
     */
    public static function error($message, $context = [])
    {
        static::log('error', $message, $context);
    }

    /**
     * Log a warning message to the application log.
     *
     * @param string $message
     * @param mixed $context
     * @return void
     */
    public static function warning($message, $context = [])
    {
        static::log('warning', $message, $context);
    }

    /**
     * Log a notice message to the application log.
     *
     * @param string $message
     * @param mixed $context
     * @return void
     */
    public static function notice($message, $context = [])
    {
        static::log('notice', $message, $context);
    }

    /**
     * Log an info message to the application log.
     *
     * @param string $message
     * @param mixed $context
     * @return void
     */
    public static function info($message, $context = [])
    {
        static::log('info', $message, $context);
    }

    /**
     * Log a debug message to the application log.
     *
     * @param string $message
     * @param mixed $context
     * @return void
     */
    public static function debug($message, $context = [])
    {
        static::log('debug', $message, $context);
    }
}
