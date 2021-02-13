<?php

namespace dadodasyra\armorutilities;

use pocketmine\event\entity\EntityArmorChangeEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\item\Item;
use pocketmine\Player;
use pocketmine\scheduler\Task;

class ArmorListener implements Listener
{
    public function onEquip(EntityArmorChangeEvent $event)
    {
        ArmorEffect::trigger($event);
    }

    public function onRespawn(PlayerRespawnEvent $event)
    {
        $player = $event->getPlayer();
        ArmorUtilities::$instance->getScheduler()->scheduleDelayedTask(new class($player) extends Task{
            public Player $player;
            public function __construct($player){$this->player = $player;}
            public function onRun(int $currentTick) : void
            {
                $player = $this->player;
                $inv = $player->getArmorInventory()->getContents();
                foreach ($inv as $slot => $item) {
                    $ev = new EntityArmorChangeEvent($player, Item::get(0), $item, $slot);
                    $ev->call();
                }
            }
        }, 1);
    }
}