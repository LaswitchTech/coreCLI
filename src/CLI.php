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
    private $CLI = null;
    private $Reflector = null;

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
                $strCommandName = ucfirst($argv[1] . "Command");
                unset($argv[1]);

                // Identify the Action
                if(count($argv) > 0){
                    $strMethodName = $argv[2] . "Action";
                    unset($argv[2]);

                    // Assemble Command
                    if(is_file($this->Configurator->root() . "/Command/" . $strCommandName . ".php")){

                        // Load Command File
                        require_once $this->Configurator->root() . "/Command/" . $strCommandName . ".php";

                        // Create Command
                        $this->CLI = new $strCommandName($this->Auth);

                        // Execute Action
                        $this->CLI->{$strMethodName}(array_values($argv));
                    } else {
                        $this->Logger->debug($this->Configurator->root() . "/Command/" . $strCommandName . ".php not found!");
                        $this->Logger->debug("[".$strCommandName."] 501 Not Implemented");
                        $this->output("[".$strCommandName."] 501 Not Implemented");
                    }
                } else {
                    $this->output("Missing action");
                }
            } else {
                $this->output("Missing command");
            }
        } else {
            $this->output("Could not identify the defining file");
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
