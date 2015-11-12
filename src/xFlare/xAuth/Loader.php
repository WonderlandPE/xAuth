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
  public $proccessmanager = [];
  public $mainlogger = [];
  public function onEnable(){
    $this->getServer()->getPluginManager()->registerEvents($this, $this);
    $this->version = "1.0.0";
    $this->codename = "xFlaze";
    $this->loggercount = 0;
    array_push($this->mainlogger, "xAuth by xFlare has been enabled!");
    $this->saveDefaultConfig();
    $this->provider = strtolower($this->getConfig()->get("autentication-type"));
    $this->status = null; //Plugin starting up...
    $this->memorymanagerdata = 0;
    $this->debug = true; //$this->getConfig()->get("debug-mode");
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
      $this->status = "failed";
      $this->getServer()->getLogger()->info("§7[§eException§7] §3Updating config...xAuth will be enabled soon...§7.");
      $myoptions=array();
      array_push($myoptions, $this->provider); //Push old data so it can be inserted in new config.
      $this->updateConfig($myoptions);
      return;
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
    if($this->debug === true or $this->logger === true && !file_exists($this->getDataFolder() . "xauthlogs")){
      $this->getServer()->getLogger()->info("§7[§axAuth§7] §eCreating §axAuth §elogger§7...");
      $this->xauthlogger = new Config($this->getDataFolder() . "xauthlogs.log", Config::ENUM, array());
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
      $this->getServer()->getLogger()->info("§7> §axAuth §3has been §aenabled§7.");
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
    if($this->getConfig()->get("hotbar-message") === true){
      $this->getServer()->getScheduler()->scheduleRepeatingTask(new AuthMessage($this), 20);
    }
    $this->getServer()->getScheduler()->scheduleRepeatingTask(new xAuthLogger($this), 20);
    if($this->api){
      $this->getServer()->getPluginManager()->registerEvents(new API($this), $this);
    }
  }
  public function updateConfig($myoptions){
    if($this->debug){
      var_dump($myoptions);
    }
    if($this->version !== $this->getConfig()->get("version")){
      $this->getServer()->getLogger()->info("§7[§axAuth§7] §3Updating xAuth config to $this->version...");
      $this->getConfig()->set("version", $this->version);
      $this->getConfig()->save();
      $this->checkForConfigErrors();
    }
    else{
      $this->getServer()->getLogger()->info("§7[§cError§7] §3xAuth called config update on null.");
      $this->totalerrors++;
    }
  }
  public function registerConfigOptions(){ //Config -> Object for less lag.
    $this->allowMoving = $this->getConfig()->get("allow-movement");
    $this->allowPlace = $this->getConfig()->get("allow-block-placing");
    $this->allowBreak = $this->getConfig()->get("allow-block-breaking");
    $this->allowCommand = $this->getConfig()->get("allow-commands");
    $this->simplepassword = $this->getConfig()->get("simple-passcode-blocker");
    $this->safemode = $this->getConfig()->get("safe-mode");
    $this->logger = $this->getConfig()->get("log-xauth");
    $this->api = $this->getConfig()->get("enable-api");
    $this->async = $this->getConfig()->get("use-async");
    if($this->safemode !== true && $this->safemode !== false || $this->simplepassword !== true && $this->simplepassword !== false || $this->allowMoving !== true && $this->allowMoving !== false || $this->allowPlace !== true && $this->allowPlace !== false || $this->allowBreak !== true && $this->allowBreak !== false || $this->allowCommand !== true && $this->allowCommand !== false || $this->debug !== false && $this->debug !== true){
      $this->getServer()->getLogger()->info("§7[§axAuth§7] §3Config to object conversion failed, please make sure you configure the config properly!");
      $this->status = "failed";
      $this->totalerrors++;
      return;
    }
    if($this->logger){
      $this->getServer()->getLogger()->info("§7[§axAuth§7] §3Logger is enabled.");
    }
    if($this->debug){
      $this->getServer()->getLogger()->info("§7[§axAuth-Debug§7] §3Config options have been registered.");
    }
  }
}
    
