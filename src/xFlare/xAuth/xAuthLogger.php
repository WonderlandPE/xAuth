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
    	if($this->plugin->status === "enabled"){
      		$prefix = "[xAuth]";
    	}
    	if($this->plugin->status === "failed"){
      		$prefix = "[Failure]";
    	}
    	if($this->plugin->status === null){
      		$prefix = "[PreloadError]";
    	}
    	$message = $this->owner->mainlogger[$this->loggercount];
    	$exception = "$prefix $message";
	$this->getServer()->getLogger()->info($exception);
	if($this->logger){
		$file = $this->plugin->getDataFolder() . "xauthlogs.log";
    		file_put_contents($file, $exception);
	}
	$this->loggercount++;
  }
}
