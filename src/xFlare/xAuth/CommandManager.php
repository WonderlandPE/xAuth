<?php
/*
                            _     _     
            /\             | |   | |    
 __  __    /  \     _   _  | |_  | |__  
 \ \/ /   / /\ \   | | | | | __| | '_ \ 
  >  <   / ____ \  | |_| | | |_  | | | |
 /_/\_\ /_/    \_\  \__,_|  \__| |_| |_|
                                        
                                        */
namespace xFlare\xAuth;

use pocketmine\event\Listener;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Server;
/*
- Manages commands, xAuth commands are proccessed here.
*/
class CommandManager implements Listener{
	public function __construct(Loader $plugin){
        	$this->plugin = $plugin;
  	}
  	public function onCommand(CommandSender $sender, Command $command, $label, array $args) {
        	switch (strtolower($command->getName())){
            		case "changepw":
            			//Change password...
                		break;
                	case "unregister":
            			//Unregister...
                		break;
        	}
  	}
}
