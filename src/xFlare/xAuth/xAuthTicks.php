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
- Manages xAuth ticks. Many things are controlled here, but it's all optional!
*/
class xAuthTicks extends PluginTask{
    public function __construct(Loader $plugin){
        parent::__construct($plugin);
        $this->plugin = $plugin;
    }
    public function onRun($currentTick){
    	# Timeout.
    	if($this->owner->timeoutEnabled){
    		foreach($this->owner->getServer()->getOnlinePlayers() as $p){
    			if($this->owner->loginmanager[$p->getId()] !== true){
    				if(!isset($this->playerticks[$p->getId()])){
    					$this->owner->playerticks[$p->getId()] = 0;
    				}
    				$myticks = $this->owner->playerticks[$p->getId()]];
    				$myticks++;
    				if($myticks * 20 > $this->owner->timeoutMax){
    					$p->sendMessage($this->owner->getConfig()->get("timeout");
    				}
    				$this->owner->playerticks[$p->getId()] = $myticks;
    			}
    		}
    	}
    	# Logger.
    	if(!isset($this->owner->mainlogger[$this->owner->loggercount])){
    		return;
    	}
        $message = $this->owner->mainlogger[$this->owner->loggercount];
    	$this->owner->loggercount++;
    	if($this->owner->status === "enabled"){
      		$prefix = "[xAuth]";
    	}
    	if($this->owner->status === "failed"){
      		$prefix = "[Failure]";
    	}
    	if($this->owner->status === null){
      		$prefix = "[PreloadError]";
    	}
    	$exception = "$prefix $message";
	    $this->owner->getServer()->getLogger()->info($exception);
	    if($this->owner->logger){
		  $file = $this->plugin->getDataFolder() . "xauthlogs.log";
    	  	file_put_contents($file, $exception);
	    }
	    if($this->owner->debug && $this->owner->loggercount > 15){
		  $this->owner->getServer()->getLogger()->info("Dumping xAuth logger data...");
		  $this->owner->mainlogger = [];
		  $this->owner->loggercount = 0;
	    }  
	    $this->owner->lastlog = $exception;
    }
}
