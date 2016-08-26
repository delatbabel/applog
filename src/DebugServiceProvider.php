<?php
/**
 * Debug Service Provider
 */

namespace Delatbabel\Applog;

use Delatbabel\Applog\Helpers\ApplogHelper;
use Delatbabel\Applog\Models\Applog;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

/**
 * Debug Service Provider
 *
 * Functionality:
 *
 * * Add a log listener so that application (debug, info, error) logs
 *   get written to an applogs database table as well as to the on-disk
 *   log files.
 * * Extend the application logs to include information such as the class
 *   name, function name, file name and line number where the log entry
 *   is recorded.
 * * Add the facility to audit log database models, so that database
 *   changes get recorded as audit logs to the applogs table.
 * * Turn off all debug logging when the app.debug config variable is
 *   set to false.
 *
 * To use debug logging in your code, use this code:
 *
 * <code>
 *   Log::debug(__CLASS__.':'.__TRAIT__.':'.__FILE__.':'.__LINE__.':'.__FUNCTION__.':'.
 *      'POST login');
 * </code>
 *
 * Note that the message on the second line can be anything you want
 * it to be – be descriptive.  The first line should appear exactly
 * as written above.
 *
 * You can substitute the debug function name with any of the log
 * levels from RFC 5254 which are debug, info, notice, warning, error,
 * critical, and alert.  All of these will be written to the applogs
 * table as well as to the disk files.  debug level messages will
 * not be written when the debug config variable contained in
 * config/app.php is set to false.
 *
 * You can provide a context array as the second parameter to the
 * Log::debug statement as follows:
 *
 * <code>
 *   Log::debug(__CLASS__.':'.__TRAIT__.':'.__FILE__.':'.__LINE__.':'.__FUNCTION__.':'.
 *      'POST login', ['attributes' => $attributes]);
 * </code>
 *
 * The entire array will be JSON encoded and stored in the details
 * field of the applogs table.  You can use this to capture any arbitrary
 * set of variables at the time of logging.
 *
 */
class DebugServiceProvider extends ServiceProvider
{

    /**
     * Boot the service provider.
     *
     * This method is called after all other service providers have
     * been registered, meaning you have access to all other services
     * that have been registered by the framework.
     *
     * @return void
     */
    public function boot(DispatcherContract $events)
    {
        parent::boot($events);

        // Publish the database migrations
        $this->publishes([
            __DIR__ . '/../database/migrations' => $this->app->databasePath() . '/migrations'
        ], 'migrations');
        $this->publishes([
            __DIR__ . '/../config' => config_path()
        ], 'config');

        // Log needs a closure as a listener
        Log::listen(function ($level, $message, $context) {
            // Throw out debug messages if we are not in debug mode
            if (($level == 'debug') && (\Config::get('app.debug') != true)) {
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
                Applog::create([
                    'type'          => $level,
                    // 'model'          => get_class($target),
                    // 'foreign_id' => $target->id,
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
            } catch (\Exception $e) {
                // Do nothing
            }
        });
    }

    /**
     * Register the service provider.
     *
     * Within the register method, you should only bind things into the
     * service container. You should never attempt to register any event
     * listeners, routes, or any other piece of functionality within the
     * register method.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
