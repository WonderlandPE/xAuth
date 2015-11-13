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
    public function getXAuthVersion(){
    	return $this->plugin->version;
    }
    
    #Get xAuth codename.
    public function getXAuthCodeName(){
    	return $this->plugin->codename;
    }
    
    #Gets a config option and returns it, returs false if denied.
    public function getxAuthConfigOption($option){
      $option = strtolower($option);
      if($option === "username" || $option === "port" || $option === "server" || $option === "password"){
      	return false; //Nice try, your not allowed to take these options.
      }
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
   
   #Counts all logged-in players.
   public function countLoggedPlayers()(){
   	$count = 0;
   	foreach($this->getServer()->getOfflinePlayers() as $p){
   		if($this->plugin->loginmanager[$p->getId()] === true){
   			$count++;
   		}
   	}
   	return $count;
   }
   
   #Counts all not logged-in players.
   public function countNotLoggedPlayers()(){
   	$count = 0;
   	foreach($this->getServer()->getOfflinePlayers() as $p){
   		if($this->plugin->loginmanager[$p->getId()] === false){
   			$count++;
   		}
   	}
   	return $count;
   }
   
   #Returns true or false depending on if SafeMode is enabled.
   public function isSafeModexAuth(){
   	return $this->owner->safemode;
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
}
  
