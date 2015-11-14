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
use pocketmine\utils\TextFormat;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\Server;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerJoinEvent;
/*
- Logins/Registers players.
- Main core functions are done here.
*/
class LoginAndRegister implements Listener{
	public function __construct(Loader $plugin){
        $this->plugin = $plugin;
    }
    public function onJoin(PlayerJoinEvent $event){
    	//Messages
    	if($this->plugin->status === "enabled"){
    		$event->getPlayer()->sendMessage("[xAuth] This server is protected by xAuth.");
    		$event->getPlayer()->sendMessage($this->plugin->getConfig()->get("join"));
    	}
        if($this->plugin->usernamestatus === true){
            $name = $event->getPlayer()->getName();
            $event->getPlayer()->setNameTag("[Processing..] $name");
        }
    	if($this->plugin->safemode && $this->plugin->status !== "enabled"){
    		$event->getPlayer()->sendMessage($this->plugin->getConfig()->get("disable"));
    	}
    	if($this->plugin->status === "enabled"){
    		if($this->plugin->provider === "yml"){
    			if($this->plugin->registered->exists(strtolower($event->getPlayer()->getName()))){
    				$event->getPlayer()->sendMessage($this->plugin->getConfig()->get("already-registered"));
    				$event->getPlayer()->sendMessage($this->plugin->getConfig()->get("login"));
                    $event->getPlayer()->setNameTag("[Not-Logged-In] $name");
    				$this->plugin->loginmanager[$event->getPlayer()->getId()] = 1;
    			}
    			else{
    				$event->getPlayer()->sendMessage($this->plugin->getConfig()->get("please-register"));
                    $event->getPlayer()->sendMessage($this->plugin->getConfig()->get("wanted"));
                    $event->getPlayer()->setNameTag("[Not-Registered] $name");
    				$this->plugin->loginmanager[$event->getPlayer()->getId()] = 0;
    			}
    		}
    	}
    }
    public function onChat(PlayerChatEvent $event){
    	$message = $event->getMessage();
    	if($this->plugin->loginmanager[$event->getPlayer()->getId()] === 1){
    		if($this->plugin->provider === "yml"){
    			$myuser = new Config($this->plugin->getDataFolder() . "players/" . strtolower($event->getPlayer()->getName() . ".yml"), Config::YAML);
    			if(md5($message) === $myuser->get("password")){
    				$this->plugin->loginmanager[$event->getPlayer()->getId()] = true;
                    $this->plugin->chatprotection[$event->getPlayer()->getId()] = md5($message);
                    $event->getPlayer()->setNameTag("$name");
    				$event->getPlayer()->sendMessage($this->plugin->getConfig()->get("logged"));
    			}
    			else{
    				$event->getPlayer()->sendMessage($this->plugin->getConfig()->get("incorrect"));
    			}
    		}
    		return;
    	}
    	if($this->plugin->loginmanager[$event->getPlayer()->getId()] === 0 && !isset($this->plugin->chatprotection[$event->getPlayer()->getId()])){
    		if($this->plugin->provider === "yml"){
    			$this->plugin->chatprotection[$event->getPlayer()->getId()] = $this->proccessPassword($message, $event->getPlayer());
    			if(isset($this->plugin->chatprotection[$event->getPlayer()->getId()])){
    				$event->getPlayer()->sendMessage($this->plugin->getConfig()->get("success"));
    			}
    		}
    		return;
    	}
    	if($this->plugin->loginmanager[$event->getPlayer()->getId()] === 0 && isset($this->plugin->chatprotection[$event->getPlayer()->getId()])){
    		if(md5($message) === $this->plugin->chatprotection[$event->getPlayer()->getId()]){
    			$myuser = new Config($this->plugin->getDataFolder() . "players/" . strtolower($event->getPlayer()->getName() . ".yml"), Config::YAML);
    			$myuser->set("password", $this->plugin->chatprotection[$event->getPlayer()->getId()]);
    			$myuser->save();
                $this->plugin->registered->set(strtolower($event->getPlayer()->getName()));
                $this->plugin->registered->save();
    			if($myuser->get("password") === md5($message)){
    				$event->getPlayer()->sendMessage($this->plugin->getConfig()->get("registered"));
    				$this->plugin->loginmanager[$event->getPlayer()->getId()] = true;
    			}
    			else{
    				$event->getPlayer()->sendMessage($this->plugin->getConfig()->get("error")); //This should not happen anyways.
    			}
    		}
    		else{
    			$event->getPlayer()->sendMessage($this->plugin->getConfig()->get("no-success"));
    			unset($this->plugin->chatprotection[$event->getPlayer()->getId()]);
    		}
        }
    }
    private function proccessPassword($password, $player){
    	if(strlen($password) < $this->plugin->short){
    		$player->sendMessage($this->plugin->getConfig()->get("short"));
    		return;
    	}
    	if(strlen($password) > $this->plugin->max){
    		$player->sendMessage($this->plugin->getConfig()->get("long"));
    		return;
    	}
    	$simplepass = strtolower($password);
    	if($this->plugin->simplepassword === true && $this->plugin->status === "enabled"){
    		if($simplepass === 123456789 || $simplepass === 987654321 || $simplepass === "asdfg" || $simplepass === "password"){
    			$player->sendMessage($this->plugin->getConfig()->get("simple"));
    			unset($this->plugin->chatprotection[$event->getPlayer()->getId()]);
    			return;
    		}
    	}
    	$myuser = new Config($this->plugin->getDataFolder() . "players/" . strtolower($player->getName() . ".yml"), Config::YAML);
    	$myuser->set("password", md5($password));
    	$myuser->save();
    	return md5($password);
    }
}

