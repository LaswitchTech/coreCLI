# Usage
## Initiate CLI
To use `CLI`, simply include the CLI.php file and create a new instance of the `CLI` class.

```php
<?php

// Import additionnal class into the global namespace
use LaswitchTech\coreCLI\CLI;

// Load Composer's autoloader
require 'vendor/autoload.php';

// Initiate CLI
$CLI = new CLI();
```

### Properties
`CLI` provides the following properties:

#### core Modules
- [Configurator](https://github.com/LaswitchTech/coreConfigurator)
- [Logger](https://github.com/LaswitchTech/coreLogger)
- [Auth](https://github.com/LaswitchTech/coreAuth)

### Skeleton
Let's start with the skeleton of your CLI project directory.

```sh
├── cli
├── config
│   └── database.cfg
├── Command
│   └── HelloCommand.php
└── Model
    └── HelloModel.php
```

* cli: The cli file is the entry-point of our application. It will initiate the Command being called in our application.
* config/database.cfg: The config file holds the configuration information of our CLI. Mainly, it will hold the database credentials. But you could use it to store other configurations.
* Command/: This directory will contain all of your Commands.
* Command/HelloCommand.php: the Hello Command file which holds the necessary application code to entertain CLI calls. Mainly the methods that can be called.
* Model/: This directory will contain all of your models.
* Model/HelloModel.php: the Hello model file which implements the necessary methods to interact with a table in the MySQL database.

### Models
Model files implements the necessary methods to interact with a table in the MySQL database. These model files needs to extend the Database class in order to access the database.

See [coreBase](https://github.com/LaswitchTech/coreBase) for more information.

#### Naming convention
The name of your model file should start with a cCLItal character and be followed by ```Model.php```.  If not, the bootstrap will not load it.
The class name in your Model files should match the name of the model file.

#### Example
```php

// Import BaseModel class into the global namespace
use LaswitchTech\coreBase\BaseModel;

class UserModel extends BaseModel {
    public function getUsers($limit) {
        return $this->select("SELECT * FROM users ORDER BY id ASC LIMIT ?", ["i", $limit]);
    }
}
```

### Commands
Command files holds the necessary application code to entertain CLI calls. Mainly the methods that can be called. These Command files needs to extend the BaseCommand class in order to access the basic methods.

#### Naming convention
The name of your Command file should start with a capital character and be followed by ```Command.php```.  If not, the bootstrap will not load it. The class name in your Command files should match the name of the command file.

Finally, callable methods need to end with ```Action```.

#### Example
```php

// Import BaseCommand class into the global namespace
use LaswitchTech\coreBase\BaseCommand;

class HelloCommand extends BaseCommand {

    public function worldAction($argv) {
        if(count($argv) > 0){
            foreach($argv as $name){
                $this->sendOutput('Hello ' . $name . '!');
            }
        } else {
            $this->sendOutput('Hello World!');
        }
    }
}
```

#### Methods
##### output()
This method is used to output content into the terminal.
```php
$this->output('Hello Wolrd!');
```

##### set()
This method is used to color content for the terminal.

Available Colors:
 * default
 * black
 * red
 * green
 * yellow
 * blue
 * magenta
 * cyan
 * light-gray
 * dark-gray
 * light-red
 * light-green
 * light-yellow
 * light-blue
 * light-magenta
 * light-cyan
 * white

```php
$this->set('Hello Wolrd!', 'magenta');
```

##### error()
This method is used to output content in red into the terminal.
```php
$this->error('Hello Wolrd!');
```

##### success()
This method is used to output content in green into the terminal.
```php
$this->success('Hello Wolrd!');
```

##### warning()
This method is used to output content in yellow into the terminal.
```php
$this->warning('Hello Wolrd!');
```

##### info()
This method is used to output content in cyan into the terminal.
```php
$this->info('Hello Wolrd!');
```

##### input($string, $options = null, $default = null)
This method is used to request input from the terminal. There are 3 types of request you can make string, select and text.
```php
// String
$this->input('What is your name?');

// String with default value
$this->input('What is your name?', 'John Doe');
```

```php
// Select
$this->input('Are you a?',['dog','cat','person']);

// Select with default value
$this->input('Are you a?',['dog','cat','person'],'person');
```

```php
// Note that you can type (END/EXIT/QUIT/EOF/:Q/) to exit this request
// Text with a limit of lines
$this->input('What kind of person are you?',5);

// Text without a limit of lines
$this->input('What kind of person are you?',0);

// Text request and prompt exits
$this->input('What kind of person are you?',5,true);
```

### CLI
The cli file is the entry-point of our application. It will initiate the Command being called in our application. The file itself can be named any way you want.

#### Example

```php
#!/usr/bin/env php
<?php
session_start();

// Import coreCLI class into the global namespace
// These must be at the top of your script, not inside a function
use LaswitchTech\coreCLI\CLI;

// Load Composer's autoloader
require 'vendor/autoload.php';

// Interpret Standard Input
if(defined('STDIN') && !empty($argv)){

    // Start Command
    new CLI($argv);
}
```

### Calling the CLI
Once you have setup your first Command, you can start calling your Command-Line.

#### Example
```bash
./cli [COMMAND] [ACTION]
```
