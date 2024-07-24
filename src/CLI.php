<?php

// Declaring namespace
namespace LaswitchTech\coreCLI;

// Import additionnal class into the global namespace
use LaswitchTech\coreConfigurator\Configurator;
use LaswitchTech\coreLogger\Logger;
use LaswitchTech\coreAuth\Auth;
use ReflectionClass;
use Exception;

class CLI {

	// core Modules
	private $Configurator;
    private $Logger;
    private $Auth;

    // Properties
    private $CLI;
    private $Reflector;
    protected $Command;
    protected $Action;

    /**
     * CLI constructor.
     * @param $argv
     */
    public function __construct($argv){

        // Initialize Configurator
        $this->Configurator = new Configurator('cli','requirements');

        // Initiate Logger
        $this->Logger = new Logger('cli');

        // Initiate Auth if class exists
        if(class_exists('LaswitchTech\coreAuth\Auth')){
            $this->Auth = new Auth();
        }

        // Include all model files
        if(is_dir($this->Configurator->root() . "/Model")){
            foreach(scandir($this->Configurator->root() . "/Model/") as $model){
                if(str_contains($model, 'Model.php')){
                    require_once $this->Configurator->root() . "/Model/" . $model;
                }
            }
        }

        // Parse Standard Input
        if(count($argv) > 0){

            // Identify the Defining File
            $this->Reflector = $argv[0];
            unset($argv[0]);

            // Identify the Command File
            if(count($argv) > 0){

                // Identify the Command
                $this->Command = ucfirst($argv[1] . "Command");
                unset($argv[1]);

                // Check if the required command is available
                if(is_file($this->Configurator->root() . "/Command/" . $this->Command . ".php")){

                    // Load Command File
                    require_once $this->Configurator->root() . "/Command/" . $this->Command . ".php";

                    // Check if command class exist
                    if(class_exists($this->Command)){

                        // Create Command
                        $this->CLI = new $this->Command($this->Auth);

                        // Identify the Action
                        if(count($argv) > 0){

                            // Identify the Action
                            $this->Action = $argv[2] . "Action";
                            unset($argv[2]);

                            // Check if the required action is available
                            if(method_exists($this->CLI, $this->Action)){

                                // Execute Action
                                $this->CLI->{$this->Action}(array_values($argv));
                            } else {

                                // Set error message
                                $error = "Action[".$this->Action."] not implemented";

                                // Reset Command
                                $this->Action = null;

                                // Display help
                                $this->help($error);
                            }
                        } else {

                            // Display help
                            $this->help("Missing action");
                        }
                    } else {

                        // Set error message
                        $error = "Unable to load command[".$this->Command."]";

                        // Reset Command
                        $this->Command = null;

                        // Display help
                        $this->help($error);
                    }
                } else {

                    // Set error message
                    $error = "Command[".$this->Command."] not found";

                    // Reset Command
                    $this->Command = null;

                    // Display help
                    $this->help($error);
                }
            } else {

                // Display help
                $this->help("Missing command");
            }
        } else {

            // Display help
            $this->output("Could not identify the defining file");
        }
    }

    /**
     * Output command-line help
     * @param string $string
     * @return void
     */
    protected function help($string) {

        // Log the error
        // $this->Logger->error($string);

        // List available commands if no command is provided
        if(!$this->Command){

            // Output Usage
            $this->output("Usage: ./cli [command] [action] [options]");

            // List available commands
            $this->output("Available Commands:");
            foreach(scandir($this->Configurator->root() . "/Command/") as $command){
                if(str_contains($command, 'Command.php')){
                    $this->output(" - " . strtolower(str_replace('Command.php','',$command)));
                }
            }
            exit();
        }

        // List available actions if no action is provided
        if(!$this->Action){

            // Output Usage
            $this->output("Usage: ./cli ".strtolower(str_replace('Command','',$this->Command))." [action] [options]");

            // List available actions
            $this->output("Available Actions:");
            foreach(get_class_methods($this->CLI) as $method){
                if(substr($method,-6) == 'Action'){
                    $this->output(" - " . strtolower(str_replace('Action','',$method)));
                }
            }
            exit();
        }
    }

    /**
     * Output a string
     */
    protected function output($string) {
        print_r($string . PHP_EOL);
    }

    /**
     * Check if the required modules are installed
     *
     * @return bool
     */
    protected function isInstalled(){

        // Retrieve the list of required modules
        $modules = $this->Configurator->get('requirements','modules');

        // Check if the required modules are installed
        foreach($modules as $module){

            // Check if the class exists
            if (!class_exists($module)) {
                return false;
            }

            // Initialize the class
            $class = new $module();

            // Check if the method exists
            if(method_exists($class, isInstalled)){
                if(!$class->isInstalled()){
                    return false;
                }
            }
        }

        // Return true
        return true;
    }
}
