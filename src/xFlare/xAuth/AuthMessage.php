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
    }
    public function onRun($currentTick){
        if($this->owner->status === "enabled"){
            foreach($this->owner->getServer()->getOnlinePlayers() as $p){
                if($this->owner->loginmanager[$p->getId()] === 0){
                    $p->sendTip("§axAuth§7: §dPlease authenticate§7!");
                }
            } 
        }
        elseif($this->owner->safemode === true && $this->owner->status !== "enabled"){
            foreach($this->owner->getServer()->getOnlinePlayers() as $p){
                $p->sendTip("§axAuth§7: §axAuth §3is §cdisabled§7, §3please check §cconsole§7!");
            }
        }
    }
}
