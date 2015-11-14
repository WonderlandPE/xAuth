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
- Logs everything, sends console messages.
- Performes lag checks and more!
*/
class xAuthLogger extends PluginTask{
    public function __construct(Loader $plugin){
        parent::__construct($plugin);
        $this->plugin = $plugin;
        $this->length = -1;
    }
    public function onRun($currentTick){
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
