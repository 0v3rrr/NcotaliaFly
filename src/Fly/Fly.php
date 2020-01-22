<?php

namespace Fly;

use pocketmine\Player;
use pocketmine\Server;
use pocketmine\math\Vector3;
use pocketmine\entity\Entity;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\event\entity\EntityLevelChangeEvent;

class Fly extends PluginBase implements Listener{

    public function onEnable() {
        $this->getLogger()->info(TextFormat::GREEN."- Plugin Fly On");
        $this->getLogger()->info(TextFormat::GREEN."- Fait par Noctalia");
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->reloadConfig();
    }

    public function onCommand(CommandSender $sender, Command $command, $label, array $args) : bool{
        if (!$sender->hasPermission("fly.command")) {
            $sender->sendMessage(TextFormat::RED.TextFormat::ITALIC."Vous n'avez pas la permission de faire cette commande");
            return false;
        }
        $sender = $sender;
        if(isset($args[0])) {
            $player = $this->getServer()->getPlayer(args[0]);
            if ($player instanceof Player and $sender->hasPermission("fly.command.others")) {
                $sender = $player;
            }
        }
        $world = $sender->getLevel()->getFolderName();
        if (!$this->flyAllowed($world)) {
            $sender->sendMessage(TextFormat::RED.TextFormat::ITALIC."Vous ne pouvez pas Fly dans ce monde");
            return false;
        }
        if (!$sender->isSurvival()) {
            $sender->sendMessage(TextFormat::RED.TextFormat::ITALIC."Vous ne pouvez pas utiliser le fly en creatif");
            return false;
        }
        if (!$sender->getAllowFlight()) {
            $sender->addTitle(TextFormat::AQUA."Fly", TextFormat::RED."Désactivé", 20, 10, 20); 
            $sender->setFlying(false);
            $sender->setAllowFlight(false);
        }else {
            $sender->addTitle(TextFormat::AQUA."Fly", TextFormat::GREEN."Activé", 20, 10, 20);
            $sender->setAllowFlight(true);
        }
        return true;
    }

    public function flyAllowed($world) : bool{
        $config = new Config($this->getDataFolder()."config.yml", Config::YAML);
        $worlds = $config->get("worlds, []");
        return in_array($world, $worlds);
    }
    public function onChange(EntityLevelChangeEvent $event) {
        $player = $event->getEntity();
        if ($player instanceof Player) {
            $world = $event->getTarget()->getFolderName();
            if (!$this->flyAllowed($world) and $player->getAllowFlight() and $player->isSurvival()) {
            $player->addTitle(TextFormat::AQUA."Fly", TextFormat::RED.TextFormat::BOLD."Désactivé", 20, 10, 20);
            $player-> setFlying(false);
            $player->setAllowedFlight(false);
			}

        }        
    }
}
?>
