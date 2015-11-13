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

use pocketmine\Server;
use pocketmine\scheduler\PluginTask;
/*
- Sends xAuth messages to hotbar.
*/
class AuthMessage extends PluginTask{
    public function __construct(Loader $plugin){
        parent::__construct($plugin);
        $this->plugin = $plugin;
        # This will be loaded into memory to prevent lag.
        # I may add a config option for this in the future.
        $this->disable = $this->owner->getConfig()->get("hotbar-disabled");
        $this->loginhotbar = $this->owner->getConfig()->get("hotbar-login");
        $this->registerhotbar = $this->owner->getConfig()->get("hotbar-register");
    }
    public function onRun($currentTick){
        if($this->owner->status === "enabled"){
            foreach($this->owner->getServer()->getOnlinePlayers() as $p){
                if($this->owner->safemode === true && $this->owner->status !== "enabled"){
                  $p->sendTip($this->disable);
                }
                elseif($this->owner->loginmanager[$p->getId()] === 0){
                   $p->sendTip($this->loginhotbar);
                }
                elseif($this->owner->loginmanager[$p->getId()] === 1){
                   $p->sendTip($this->registerhotbar);
            } 
        }
    }
}
