<?php
/**
 * Auditable Trait -- for model classes that can be audited.
 *
 * @author Del
 * @copyright  2015 Incube8.sg
 */

namespace Delatbabel\Applog\Models;

use Delatbabel\Applog\Helpers\ApplogHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

/**
 * Auditable Trait -- for model classes that can be audited.
 *
 * This trait adds an event listener to a model class so that any
 * saves for the model class (inserts, updates) get audited automatically.
 *
 * In order to turn audit logging on for a model class, use this code:
 *
 * <code>
 *   use Delatbabel\Applog\Models\Auditable;
 *
 *   class MyModel
 *   {
 *       use Auditable;
 *       // ... remainder of the model code
 *   }
 * </code>
 */
trait Auditable
{
    /**
     * Boot the auditable trait for a model.
     *
     * This sets up the listeners for the internal events fired by the Laravel
     * base model class.  In this class we only add one listener --- for after
     * a model is saved.
     *
     * @return void
     */
    public static function bootAuditable()
    {
        $class_name = get_called_class();
        Event::listen('eloquent.saved: ' . $class_name, function ($target) use ($class_name) {
            if (method_exists($class_name, 'eventAuditLogger')) {
                call_user_func($class_name . '::eventAuditLogger', $target);
            }
        });
    }

    /**
     * Generic audit logger.
     *
     * This is set up as a listener for the after save event by the
     * bootAuditable method.
     *
     * Store a message in the logs table in the database.
     * Use a raw query for this to prevent looping.
     *
     * Just for the time being we are logging direct to a table rather
     * than going through the Monolog / Laravel logger API.
     *
     * @param   Model   $target
     * @return  void
     */
    public static function eventAuditLogger($target)
    {
        // exists flag is set on an existing record.
        if ($target->exists) {
            $message = 'UPDATE';
        } else {
            $message = 'INSERT';
        }

        // Fetch the currently logged in user
        $username = ApplogHelper::currentUserName();

        // Get the list of client IP addresses
        $clientIp = ApplogHelper::getClientIps();

        // Also get any addresses in _SERVER["HTTP_X_FORWARDED_FOR"]
        $forwardAddress = isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : '';
        if (empty($clientIp)) {
            $clientIp = $forwardAddress;
        } elseif (! empty($forwardAddress)) {
            $clientIp = $clientIp . ',' . $forwardAddress;
        }

        // Store the audit log.
        try {
            DB::table('applogs')->insert(array(
                'type'          => 'audit',
                'model'         => get_class($target),
                'foreign_id'    => $target->id,
                'classname'     => __CLASS__,
                'traitname'     => __TRAIT__,
                'functionname'  => __FUNCTION__,
                'filename'      => __FILE__,
                'linenumber'    => __LINE__,
                'message'       => $message,
                'details'       => $message . ' in ' . $target->getTable() . ' table.',
                'ipaddr'        => $clientIp,
                'created_by'    => $username,
                'updated_by'    => $username,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ));
        } catch (\Exception $e) {
            // Do nothing
        }
    }
}
