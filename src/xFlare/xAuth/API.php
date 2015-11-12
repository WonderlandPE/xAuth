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
- Here you can access some basic xAuth data so you can use it in your plugin.
- Open up an issue on the tracker if you think a function should be added.
*/
class API implements Listener{
	public function __construct(Loader $plugin){
        $this->plugin = $plugin;
    }
    
    #Returns the provider in lowercase, the result will always be mysql or yml.
    public function getProvider(){
      return $this->plugin->provider;
    }
    
    #Get xAuth, returns plugin.
    public function getxAuth(){
      return $this->plugin;
    }
    
    #Get xAuth version.
    public function getXauthVersion(){
    	return $this->plugin->version;
    }
    
    #Gets a config option and returns it.
    public function getxAuthConfigOption($option){
      $statement = $this->plugin->getConfig()->get($option);
      if($statement !== false && $statement !== true){
      	return false;
      }
      else{
      	return $statement;
      }
    }
    
    #Returns a true or false value depending on if a player has logged in.
    public function isAuthenticated($name){
      if($this->getPlayer($name) !== null){
      	$player = $this->getPlayer($name);
      	return $this->plugin->loginmanager[$player->getId()];
      }
    }
    
    #Important! Always check the status on your plugins or xAuth may not function right.
    #Returns a true or false value.
    public function xAuthStatus(){
      if($this->plugin->status === "enabled"){
        return true;
      }
      else{
        return false;
      }
   }
   
   #Returns an array of config options, NOTE: It will not return MySQL details for your security.
   public function sendConfigOptios(){
   }
   
   #Disables xAuth..Dangerous since auth will turn off, but safe-mode will force-fully kick in.
   #Returns false if already disabled, returns true if it has been disabled.
   public function disablexAuth(){
     if($this->plugin->status === "disabled"){
       return false; //If plugin is already disabled..
     }
     $this->plugin->safemode = true;
     $this->plugin->status = "disabled";
     if($this->plugin->status = "disabled"){
      return true;
     }
   }
   
   #Forces xAuth to turn on..If safemode is enabled there will be some checks to protect.
   public function forceActivatexAuth(){
   	if($this->plugin->safemode === true){
     		if($this->status !== null && $this->status !== "enabled" && $this->status !== "failed"){
     			$this->status = "enabled";
     			return true;
     		}
     		else{
     			return false;
     		}
   	}
   	else{
   		$this->status = "enabled";
   		return true;
   	}
   }
}
  
