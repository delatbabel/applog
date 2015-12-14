<?php
/**
 * Custom Log Writer
 *
 * @author Del
 * @copyright  2015 Incube8.sg
 */
namespace Delatbabel\Applog\Log;

use Illuminate\Log\Writer as BaseWriter;

/**
 * Custom Log Writer
 *
 * Provides some alternatives to the standard laravel log writer.
 */
class Writer extends BaseWriter
{

    /**
     * Log a debug message to the logs.
     *
     * @param  string  $message
     * @param  array  $context
     * @return void
     */
    public function debug($message, array $context = array())
    {
        // Throw out debug messages if we are not in debug mode
        if (\Config::get('app.debug') != true) {
            return;
        }
        $this->writeLog(__FUNCTION__, $message, $context);
    }
}
