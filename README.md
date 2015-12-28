# applog

Application and Audit Log package for Laravel 5.

This builds on some code that I've had hanging around for some time.  It provides a database
backed log for Laravel, into which debug, info and error logs can be written.  It also provides
an audit logging trait which can be attached to any model to automatically log CREATE and UPDATE
records for that model.

## Features

This package extends the existing Laravel 5 log writer with the following features:

* Add a log listener so that application (debug, info, error) logs get written to an applogs
  database table as well as to the on-disk log files.
* Extend the application logs to include information such as the class name, function name,
  file name and line number where the log entry is recorded.
* Add the facility to audit log database models, so that database changes get recorded as
  audit logs to the applogs table.
* Turn off all debug logging when the app.debug config variable is set to false.

This package contains the following classes and traits:

* Applog -- model class allowing logs to be written to the database.
* Auditable -- a trait that can be applied to any model class allowing any changes to the
  table to be automatically audited.

## Installation

Add these lines to your composer.json file:

```
    "require": {
        "delatbabel/applog": "~1.0"
    },
```

Once that is done, run the composer update command:

```
    composer update
```

### Register Service Provider

After composer update completes, add this line to your config/app.php file in the 'providers' array:

```
Delatbabel\Applog\DebugServiceProvider::class,
```

### Boostrap the Log Writer

Modify each of app/Console/Kernel.php and app/Http/Kernel.php to include the following bootstrappers function:

```php
protected function bootstrappers()
{
    $bootstrappers = parent::bootstrappers();

    // Swap out the default Laravel ConfigureLogging class with our own.
    foreach ($bootstrappers as $key => $value) {
        if ($value == 'Illuminate\Foundation\Bootstrap\ConfigureLogging') {
            $bootstrappers[$key] = 'Delatbabel\Applog\Bootstrap\ConfigureLogging';
        }
    }

    return $bootstrappers;
}
```

Note that Delatbabel\Applog\Bootstrap\ConfigureLogging replaces the original class
Illuminate\Foundation\Bootstrap\ConfigureLogging.   You may of course
already have a bootstrappers function in your Kernel.php files with other
bootstrappers replaced, in which case you just need to modify it to include
the updated ConfigureLogging bootstrapper code.

### Incorporate and Run the Migrations

Finally, incorporate and run the migration scripts to create the database tables as follows:

```php
php artisan vendor:publish --tag=migrations --force
php artisan migrate
```

## Example

### Audit Logging

In order to turn audit logging on for a model class, use this code:

```php
use Delatbabel\Applog\Models\Auditable;

class MyModel
{
    use Auditable;
    // ... remainder of the model code
}
```

This will now log changes to the model into the applogs table.  Note that there is an
Applog model class which you can use (Delatbabel\Applog\Models\Applog) to manipulate this
table, please ensure that you don't use the Auditable trait in that model class or you
will cause an infinite loop and fill the database.

### Debug and Error Logging

To use debug logging in your code, use this code:

```php
Log::debug(__CLASS__.':'.__TRAIT__.':'.__FILE__.':'.__LINE__.':'.__FUNCTION__.':'.
    'POST login');
```

Note that the message on the second line can be anything you want it to be – be descriptive.
The first line should appear exactly as written above.

You can substitute the debug function name with any of the log levels from RFC 5254 which
are debug, info, notice, warning, error, critical, and alert.  All of these will be written
to the applogs table as well as to the disk files.  debug level messages will not be written
when the debug config variable contained in config/app.php is set to false.

You can provide a context array as the second parameter to the Log::debug statement as follows:

```php
Log::debug(__CLASS__.':'.__TRAIT__.':'.__FILE__.':'.__LINE__.':'.__FUNCTION__.':'.
    'POST login', ['attributes' => $attributes ]);
```

The entire array will be JSON encoded and stored in the details field of the applogs table.
You can use this to capture any arbitrary set of variables at the time of logging.

## Helper Function -- get_user_name()

The audit log attempts to capture the user name of each user that was logged in when a change
is made.  To aid in this, provide a global function called get_user_name(), returning a string
(user name, email address or other identifier).

An example of such a function using the Cartalyst Sentinel facade is as follows:

```php
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;

function get_user_name()
{
    // Fetch the currently logged in user
    try {
        $user       = Sentinel::getUser();
        $username   = $user ? $user->email: '';
    } catch (\Exception $e) {
        // Ignore
    }
    return $username;
}
```

If the function does not exist or returns a null / empty value, some fallback values will be
used, culminating in "system" if no username can be found.

# Architecture

This section explains the architecture of the package and the decisions that I made while
coding.

## Bootstrapping

Laravel includes a class called Illuminate\Foundation\Http\Kernel which handles bootstrapping
the application in Http mode, and a similar class for bootstraping in Console mode.  These two
classes are normally extended in an application in the App\Http\Kernel and App\Console\Kernel
classes respectively.

Each of these classes loads a bunch of core classes that need bootstrapping, including the
Laravel logger.

Each of these classes contains a $bootstrappers array which contains the list of classes to be
bootstrapped, and a bootstrappers() function which returns that array content. I was originally
over-riding the $bootstrappers array but found that it varied between different patch releases
of Laravel, so instead I have extended the bootstrappers() function (or at least provided documentation
on how to extend it) so that it returns a modified version of the $bootstrappers array.

## Log Writer

Creating a Monolog log writer is easy, it's just a matter of creating a Writer class that
implements the LogContract and PsrLoggerInterface interfaces.  I have chosen to extend the
original Illuminate\Log\Writer class to provide the base Laravel logging functionality plus
any additional functionality I choose.

My extended log writer class is Delatbabel\Applog\Log\Writer, and the only change that I have
made is to extend the debug() function to throw away the log message if application debug
mode is off.

### Additional Writer

There is one additional function that intercepts the log listener event by passing a closure to
the Log::listen call. This happens in the service provider bootstrap, being function
Delatbabel\Applog\DebugServiceProvider::boot.

This is where the database backed logging, i.e. the writing to the Applog model happens.

## Logger

Inside the original Illuminate\Foundation\Bootstrap\ConfigureLogging class there is a function
called registerLogger which creates the "log" app instance and binds a new Monolog writer to it.
Unfortunately that monolog writer is created as an object instance inside the registerLogger function,
and so can't be modified or extended.

The only alternative was to create a new ConfigureLogging class which provides a different
object instance to the same "log" app instance, in this case providing the Delatbabel\Applog\Log\Writer
class.

Ideally the creation of the log writers would be done separately to the registration of them
with the application. e.g. I would have created a ConfigureLogging class which looked up an
array of log writers from the application config, and then passed them all as instances to
the Monolog framework.

Creating multiple monolog compliant log writers is relatively simple, and Monolog supports the
idea of having multiple writers subscribed to log events so this would have created a more
modular logging architecture.  Unfortunately Laravel doesn't take advantage of the full functionality
of the Monolog framework by initiating the logger the way that it does.

## Audit Logging

Audit logging is a much simpler process than general logging, because at this point we are only
interested in the specific event of models being saved to the database.

All of this is handled in the Delatbabel\Applog\Models\Auditable trait.

Firstly it's a standard Laravel feature to call a bootTrait() method when booting each trait that
is attached to the model.  In this way the bootAuditable() function is automatically called when
bootstrapping any model containing the Auditable trait.

The bootAuditable() trait sets up an event listener for the Laravel "eloquent.saved: $classname"
event that is fired each time a model is saved.  The listener is a closure that just calls the
internal (public static) eventAuditLogger() method.

eventAuditLogger() does the actual work of audit logging -- writing an entry to the applogs table.

Note that I have chosen to use a DB::table()->insert() call to write the data instead of creating
an Applog model and saving it, *just in case* someone accidentally ties the Auditable trait to
the Applog model (which would create an infinite loop).
