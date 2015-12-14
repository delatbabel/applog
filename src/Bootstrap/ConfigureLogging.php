<?php
/**
 * Configure Logging Class
 *
 * @author Del
 * @copyright  2015 Incube8.sg
 */
namespace Delatbabel\Applog\Bootstrap;

use Illuminate\Foundation\Bootstrap\ConfigureLogging as BaseConfigureLogging;
use Illuminate\Contracts\Foundation\Application;
use Monolog\Logger as Monolog;
use Delatbabel\Applog\Log\Writer;

/**
 * Configure Logging Class
 *
 * Provides some alternatives to the standard laravel logging boostrapper.
 *
 * You will need to make the following changes:
 *
 * * Modify each of app/Console/Kernel.php and app/Http/Kernel.php to include
 *   the following bootstrappers array:
 *
 * <code>
 *   protected $bootstrappers = [
 *       'Illuminate\Foundation\Bootstrap\DetectEnvironment',
 *       'Illuminate\Foundation\Bootstrap\LoadConfiguration',
 *       'Delatbabel\Applog\Bootstrap\ConfigureLogging',
 *       'Illuminate\Foundation\Bootstrap\HandleExceptions',
 *       'Illuminate\Foundation\Bootstrap\RegisterFacades',
 *       'Illuminate\Foundation\Bootstrap\RegisterProviders',
 *       'Illuminate\Foundation\Bootstrap\BootProviders',
 *   ];
 * </code>
 *
 * Note that Delatbabel\Applog\Bootstrap\ConfigureLogging replaces the original
 * line Illuminate\Foundation\Bootstrap\ConfigureLogging.  You may of course
 * already have a bootstrappers array in your Kernel.php files with other
 * bootstrappers replaced, in which case you just need to modify it to include
 * the updated ConfigureLogging bootstrapper.
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
