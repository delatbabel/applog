<?php
/**
 * Configure Logging Class
 *
 * @author Del
 */
namespace Delatbabel\Applog\Bootstrap;

use Delatbabel\Applog\Log\Writer;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Bootstrap\ConfigureLogging as BaseConfigureLogging;
use Monolog\Logger as Monolog;

/**
 * Configure Logging Class
 *
 * Provides some alternatives to the standard laravel logging boostrapper.
 *
 * You will need to make the following changes:
 *
 * * Modify each of app/Console/Kernel.php and app/Http/Kernel.php to include
 *   the following bootstrappers function:
 *
 * <code>
 * protected function bootstrappers()
 * {
 *     $bootstrappers = parent::bootstrappers();
 *     // Swap out the default Laravel ConfigureLogging class with our own.
 *     foreach ($bootstrappers as $key => $value) {
 *         if ($value == 'Illuminate\Foundation\Bootstrap\ConfigureLogging') {
 *             $bootstrappers[$key] = 'Delatbabel\Applog\Bootstrap\ConfigureLogging';
 *         }
 *     }
 *     return $bootstrappers;
 * }
 * </code>
 *
 * Note that Delatbabel\Applog\Bootstrap\ConfigureLogging replaces the original
 * line Illuminate\Foundation\Bootstrap\ConfigureLogging.  You may of course
 * already have a bootstrappers function in your Kernel.php files with other
 * bootstrappers replaced, in which case you just need to modify it to include
 * the updated ConfigureLogging bootstrapper code.
 */
class ConfigureLogging extends BaseConfigureLogging
{

    /**
     * Register the logger instance in the container.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @return \Delatbabel\Applog\Log\Writer
     */
    protected function registerLogger(Application $app)
    {
        $app->instance('log', $log = new Writer(
            new Monolog($app->environment()), $app['events'])
        );

        return $log;
    }
}
