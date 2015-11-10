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
use pocketmine\Server;
/*
- xAuth runs tasks here to log stuff if enabled. Useful to track bugs.
*/
class xAuthLogger implements Listener{
	public function __construct(Loader $plugin){
        $this->plugin = $plugin;
  }
  public function onWrite($message){
    $file = $this->plugin->getDataFolder() . "xauthlogs.log";
    if($this->plugin->status === "enabled"){
      $prefix = "[xAuth]";
    }
    if($this->plugin->status === "failed"){
      $prefix = "[Failure]";
    }
    if($this->plugin->status === null){
      $prefix = "[PreloadError]";
    }
    $exception = "$prefix $message";
    $this->getServer()->getLogger()->info($exception);
    file_put_contents($file, $exception);
  }
}
