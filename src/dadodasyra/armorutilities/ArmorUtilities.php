<?php

namespace dadodasyra\armorutilities;

use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

class ArmorUtilities extends PluginBase
{
    public static self $instance;
    public Config $config;

    public array $effects;

    public function onEnable()
    {
        $this->getServer()->getPluginManager()->registerEvents(new ArmorListener(), $this);
        self::$instance = $this;

        $this->config = $this->getConfig();
        $this->loadConfig();
    }

    private function loadConfig()
    {
        $conf = $this->config;
        if((int)$conf->get("version") !== 1){
            $this->getLogger()->alert("Config not updated, rename in oldconfig.yml ");

            @rename($this->getDataFolder()."config.yml", $this->getDataFolder() . "old" . "config.yml");
            $this->reloadConfig();
            $conf = $this->config;
        } else if ((float)$conf->get("version") !== 1.0){
            $this->getLogger()->warning("Little update of the config, please get attention to new config");
        }
        foreach($conf->get("armor_effect") as $item => $effects){
            foreach($effects as $rest){
                if(!is_numeric($item)){
                    $this->getLogger()->error("§cError on load of item effect for ".(string)$item.", not a valid item id");
                    continue;
                }

                $effectname = Effect::getEffectByName($rest["name"]);
                if(!isset($effectname)){
                    $this->getLogger()->error("§cError on load of item effect for ".(string)$item.", effect name don't exist");
                    continue;
                }
                if(!is_numeric($rest["duration"])){
                    $this->getLogger()->error("§cError on load of item effect for ".(string)$item.", duration not a valid int");
                    continue;
                }
                if($rest["duration"] > 2147483646) $rest["duration"] = 2147483646;
                if(!is_numeric($rest["amplifier"])){
                    $this->getLogger()->error("§cError on load of item effect for ".(string)$item.", amplifier not a valid int");
                    continue;
                }
                $rest["amplifier"] = $rest["amplifier"] - 1;
                $effect = new EffectInstance($effectname, $rest["duration"], $rest["amplifier"], (bool)$rest["visible"]??true);
                if(!isset($this->effects[$item])) $this->effects[$item] = [];
                $this->effects[$item][] = $effect;
            }
        }
    }
}