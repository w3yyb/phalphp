phalphp for cli
===================



Command Line Application with common features built using phalcon framework.

Features
- Easily Record cli application output to the database
- Easily force your application to run one instance at a time (handles fatal errors properly releasing the pid file)
- Easily output debug information (even if your application has a fatal/runtime error)

 

Command Line Examples
----------------------

General Syntax for running a task/job (Note: only Task is required)

```bash
cd phalphp/private 
php cli.php [Task] [Action] [Param1] [Param2] [...]
```

Tasks are stored in `phalphp/app/tasks` directory. The following example task is named `ExampleTask.php`.
Basic example of how to kick off a cli job/task.

```bash
cd phalphp/private
php cli.php Example test1 
```

Passing parameters to your application

```bash
php cli.php Example test2 bob sanders 
```

Special Flags
---------------------

Enable debug mode to see a more detailed overview of what is going on `--debug`
This also enables a more verbose level of php reporting, displaying all php warnings.

```bash
php cli.php Example cmd --debug
```

Record all output to the database (in the `task` table) `--record` . 

```bash
php cli.php Example test1 --record
```


Only allow 1 instance to run at a time `--single`
```bash
php cli.php Example test1 --single
```

Enable all flags
```bash
php cli.php Example test1 --debug --record --single
```

Adding New Tasks
--------------------

Go to `phalphp/app/tasks` directory. This is where all the tasks are stored.
Just go ahead and create a new file here (eg. `NewTask.php`)

```php
<?php

namespace Tasks;

use \Cli\Output as Output;

class NewTask extends \Phalcon\Cli\Task {

    public function workAction() {
        Output::stdout("hi");
    }
}
?>
```

Now run it!
```bash
cd phalphp/private
php cli.php New work
```

Adding New Classes to Autoload
--------------------

Note: All classes must be namespaced if you use the provided autoloader.

Open `phalphp/app/config/autoload-cli.php` and an element to the existing array.
So, you have to use namespacing to load new classes.

```php
$autoload = [
        'Utilities\Debug' => $dir . '/library/utilities/debug/',
	'Trend' => $dir . '/library/trend/'
];

return $autoload;
```
