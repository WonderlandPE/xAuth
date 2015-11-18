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
- Loads up xAuth files.
- xAuth is user-friendly and checks for errors.
*/
class Loader extends PluginBase implements Listener{
  public $loginmanager = [];
  public $chatprotection = [];
  public $mainlogger = [];
  public $kicklogger = [];
  public $playerticks = [];
  public function onEnable(){
    $this->getServer()->getPluginManager()->registerEvents($this, $this);
    $this->version = "1.0.0 beta";
    $this->codename = "xFlaze";
    $this->prefix = "§7[§dx§aAuth§7]";
    $this->loggercount = 0;
    $this->lastlog = null;
    $this->getServer()->getLogger()->info("§dx§aAuth §3by §axFlare §3is starting up§7...");
    $this->saveDefaultConfig();
    $this->provider = strtolower($this->getConfig()->get("autentication-type"));
    $this->status = null; //Plugin starting up...
    $this->memorymanagerdata = 0;
    $this->debug = false; //$this->getConfig()->get("debug-mode");
    $this->totalerrors = 0;
    $this->checkForConfigErrors();
    if($this->async !== true && $this->provider === "mysql"){
    // $this->database = mysql; Later.
    }
  }
  public function onDisable(){
    if($this->status === "enabled" && $this->debug === true && $this->totalerrors !== 0){
      $this->getServer()->getLogger()->info("§7[§axAuth§7] §3Total errors during session§7:§c $this->totalerrors");
    }
  }
  public function checkForConfigErrors(){
    $errors = 0;
    if($this->getConfig()->get("version") !== $this->version){
    	$this->getServer()->getLogger()->info("§7[§axAuth§7] §3Upgrading config§7...");
    	unlink($this->getDataFolder() . "config.yml");
    	if(!file_exists($this->getDataFolder() . "config.yml")){
    		$this->getServer()->getLogger()->info("§7[§axAuth§7] §3Config updated. Restarting§7.");
    		$this->getServer()->shutdown();
    	}
    	else{
    		$this->getServer()->getLogger()->info("§7[§axAuth§7] §3Config update failed§7!");
    	}
    }
    $this->registerConfigOptions();
    if(!file_exists($this->getDataFolder() . "players/") && $this->provider === "yml"){
        $this->getServer()->getLogger()->info("§7[§axAuth§7] §eCreating players folder for provider§7...");
	      @mkdir($this->getDataFolder() . "players/");			
    }
    elseif($this->provider === "yml" && !file_exists($this->getDataFolder() . "players/")){
      $this->getServer()->getLogger()->info("§7[§axAuth§7] §eCannot create players folder§7!");
      $errors++;
      $this->status = "failed";
      $this->getServer()->shutdown();
    }
    if($this->provider !== "mysql" && $this->provider !== "yml"){
      $this->status = "failed";
      $this->getServer()->getLogger()->info("§7[§cError§7] §3Invaild §ax§dAuth §3provider§7!");
      $this->getServer()->shutdown();
    }
    if($this->max < 0 or $this->short < 0){
      $this->max = 15;
      $this->short = 6;
      $errors++;
    }
    if($this->getConfig()->get("database-checks") === true && $this->provider !== "mysql"){
      $this->getConfig()->set("data-checks", false);
      $this->getConfig()->save();
      $errors++;
    }
    if($this->provider === "yml"){
      $this->registered = new Config($this->getDataFolder() . "registered.txt", Config::ENUM, array());
    }
    if($this->logger !== true && $this->debug !== false){
      $this->getConfig()->set("log-xauth", true);
      $this->getConfig()->save();
      $errors++;
    }
    if($this->debug === true || $this->logger === true){
      if(!file_exists($this->getDataFolder() . "xauthlogs.logs")){
        $this->getServer()->getLogger()->info("§7[§axAuth§7] §3Creating §dx§aAuth §3logger§7...");
        $this->xauthlogger = new Config($this->getDataFolder() . "xauthlogs.log", Config::ENUM, array());
      }
    }
    if($this->async !== true && $this->async !== false){
      $errors++;
      $this->getConfig()->set("use-async", false);
      $this->getConfig()->save();
      $this->async = false;
    }
    $this->totalerrors = $this->totalerrors + $errors;
    if($errors !== 0 || $this->totalerrors !== 0){
        $this->getConfig()->reload();
        $this->getServer()->getLogger()->info("§7[§ax§dAuth§7] " . $this->totalerrors . " §cerrors have been found§7.\n§3We tried to fix it§7, §3but just in case review your config settings§7!");
    }
    if($this->status === null){
      $this->registerClasses();
      $this->status = "enabled";
    }
    elseif($this->status !== null){
      $this->status = "failed";
      $this->getServer()->getLogger()->info("§7> §axAuth §3has failed to start up§7. (§c Error: $this->status §7)");
    }
  }
  public function registerClasses(){
    $this->getServer()->getPluginManager()->registerEvents(new LoginTasks($this), $this);
    $this->getServer()->getPluginManager()->registerEvents(new LoginAndRegister($this), $this);
    $this->getServer()->getPluginManager()->registerEvents(new CommandManager($this), $this);
    $this->getServer()->getScheduler()->scheduleRepeatingTask(new xAuthTicks($this), 20);
    if($this->api){
      $this->getServer()->getPluginManager()->registerEvents(new API($this), $this);
    }
    array_push($this->mainlogger, "§7> §dx§aAuth §3has been §aenabled§7.");
  }
  public function registerConfigOptions(){ //Config -> Object for less lag.
    $this->allowMoving = $this->getConfig()->get("allow-movement");
    $this->allowPlace = $this->getConfig()->get("allow-block-placing");
    $this->allowBreak = $this->getConfig()->get("allow-block-breaking");
    $this->allowCommand = $this->getConfig()->get("allow-commands");
    $this->allowShoot = $this->getConfig()->get("allow-shoot-arrows");
    $this->allowDrops = $this->getConfig()->get("allow-drops");
    $this->allowPvP = $this->getConfig()->get("allow-pvp");
    $this->allowDamage = $this->getConfig()->get("allow-damage");
    $this->simplepassword = $this->getConfig()->get("simple-passcode-blocker");
    $this->safemode = $this->getConfig()->get("safe-mode");
    $this->logger = $this->getConfig()->get("log-xauth");
    $this->api = $this->getConfig()->get("enable-api");
    $this->async = $this->getConfig()->get("use-async");
    $this->max = $this->getConfig()->get("max-characters");
    $this->short = $this->getConfig()->get("shortest-characters");
    $this->usernamestatus = $this->getConfig()->get("show-username-auth-status");
    $this->protectForce = $this->getConfig()->get("enable-kick-invalid");
    $this->hotbar = $this->getConfig()->get("hotbar-message");
    $this->timoutEnabled = $this->getConfig()->get("enabled-kick");
    $this->ipAuth = $this->getConfig()->get("ip-auth");
    if($this->timoutEnaobled){
    	$this->timoutMax = $this->getConfig()->get("kick-after-seconds");
    }
    if($this->protectForce){
    	$this->maxAttempts = $this->getConfig()->get("kick-after-invailds");
    }
    if($this->logger){
      $this->getServer()->getLogger()->info("§7[§axAuth§7] §3Logger is enabled.");
    }
    if($this->debug){
      $this->getServer()->getLogger()->info("§7[§axAuth-Debug§7] §3Config options have been registered.");
    }
  }
}
    
