<?php
/**
 * Custom Log Writer
 *
 * @author Del
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
    public function debug($message, array $context = [])
    {
        // Throw out debug messages if we are not in debug mode
        if (\Config::get('app.debug') != true) {
            return;
        }
        $this->writeLog(__FUNCTION__, $message, $context);
    }
}
