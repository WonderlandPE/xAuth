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
            			$this->unregisterAccount($sender);
                		break;
        	}
  	}
  	private function unregisterAccount($sender){
  		if($this->provider === "yml"){
  			$this->plugin->registered->remove(strtolower($sender->getName()));
  			$this->plugin->registered->save();
  			unset($this->plugin->chatprotection[$sender->getId()]);
  			$this->plugin->loginmanager[$sender->getId()];
  			$sender->sendMessage("You have un-registered this account.");
  		}
  	}
}
