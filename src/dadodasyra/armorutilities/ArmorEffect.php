<?php


namespace dadodasyra\armorutilities;


use pocketmine\event\entity\EntityArmorChangeEvent;
use pocketmine\Player;

class ArmorEffect
{
    public static function trigger(EntityArmorChangeEvent $event)
    {
        $player = $event->getEntity();
        if(!$player instanceof Player) return;

        $new = $event->getNewItem()->getId();
        $old = $event->getOldItem()->getId();
        if($old === $new) return;//Cancel if before and after is same (when the durability update)

        if(isset(ArmorUtilities::$instance->effects[$old])){
            foreach(ArmorUtilities::$instance->effects[$old] as $effect){
                $player->removeEffect($effect->getId()); //Remove effect when player remove his armor
            }
        }

        if(isset(ArmorUtilities::$instance->effects[$new])){
            foreach(ArmorUtilities::$instance->effects[$new] as $effect){
                $player->addEffect($effect); //Add effect when player add an armor
            }
        }
    }
}