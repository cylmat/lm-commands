lm-console
==========
Console commands for Laminas framework with Laminas-Cli.

How to use it?
--------------
You can include it like a classical Laminas module, in /modules ou /src directory:
* You need to have at last php7.3 installed.
* Run "composer require laminas/laminas-cli" to install officiel [Laminas-Cli](https://github.com/laminas/laminas-cli) command-line.
* Download package at [https://github.com/clymate: LmConsole](https://github.com/clymate/lm-console/archive/master.zip).
* Unzip package and insert it in your /modules or /src directory.
* At the root of your project, type in your terminal:
    * $ bin/laminas

Autoloading
-----------
You can include your own command from any module:
* Just load your traditional module in config/modules.config.php.
* Add inside a "<filename>Command.php" file extendeds from LmConsole\Command\AbstractCommand.
    * eg: MyModule/MyCommand.php.
* The command will appear in the command-line.

Commands
--------
### Debug:routes [route_name]
This command let you see the list of all routes used in your application.

### Debug:events [url:'/'] [event_name]
This command let you see the list events used in the application for a specific route.
Default value is the home url '/'.
You can get information for a specific event, or just list events with --list option
