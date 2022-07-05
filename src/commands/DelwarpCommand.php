<?php

namespace supercrafter333\theSpawn\commands;

use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\world\sound\GhastShootSound;
use supercrafter333\theSpawn\events\other\RemoveWarpEvent;
use supercrafter333\theSpawn\form\WarpForms;
use supercrafter333\theSpawn\MsgMgr;
use supercrafter333\theSpawn\theSpawn;
use supercrafter333\theSpawn\warp\WarpManager;

/**
 * Class DelwarpCommand
 * @package supercrafter333\theSpawn\commands
 */
class DelwarpCommand extends theSpawnOwnedCommand
{
    
    /**
     * DelwarpCommand constructor.
     * @param string $name
     * @param string $description
     * @param string|null $usageMessage
     * @param array $aliases
     */
    public function __construct(string $name, string $description = "", string $usageMessage = null, array $aliases = [])
    {
        $this->plugin = theSpawn::getInstance();
        $this->setPermission("theSpawn.delwarp.cmd");
        parent::__construct("delwarp", "Delete a warp!", "§4Use:§r /delwarp <warpname>", ["rmwarp", "deletewarp", "removewarp"]);
    }

    /**
     * @param CommandSender|Player $s
     * @param string $commandLabel
     * @param array $args
     */
    public function execute(CommandSender $s, string $commandLabel, array $args): void
    {
        $prefix = theSpawn::$prefix;
        $pl = theSpawn::getInstance();

        if (!$this->canUse($s)) return;

        if (count($args) < 1) {
            if ($pl->useForms()) {
                if ($pl->listWarps() == null)
                    $s->sendMessage($prefix . MsgMgr::getMsg("no-warps-set"));
                else
                    (new WarpForms())->openRmWarp($s);
            } else
                $s->sendMessage($this->usageMessage);
            return;
        }

        if (!WarpManager::existsWarp($args[0])) {
            $s->sendMessage($prefix . MsgMgr::getMsg("warp-not-exists", ["{warpname}" => (string)$args[0]]));
            return;
        }

        WarpManager::removeWarp($args[0]);
        $s->sendMessage($prefix . MsgMgr::getMsg("warp-deleted", ["{warpname}" => (string)$args[0]]));
        $s->getWorld()->addSound($s->getPosition(), new GhastShootSound());
    }

    public static function simpleExecute(Player $s, array $args): void
    {
        $prefix = theSpawn::$prefix;
        $pl = theSpawn::getInstance();

        if (!self::testPermissionX($s, "theSpawn.delwarp.cmd", "delwarp")) return;

        if (!WarpManager::existsWarp($args[0])) {
            $s->sendMessage($prefix . MsgMgr::getMsg("warp-not-exists", ["{warpname}" => (string)$args[0]]));
            return;
        }

        $ev = new RemoveWarpEvent($args[0]);
        $ev->call();
        if ($ev->isCancelled()) return;

        WarpManager::removeWarp($args[0]);
        $s->sendMessage($prefix . MsgMgr::getMsg("warp-deleted", ["{warpname}" => (string)$args[0]]));
        $s->getWorld()->addSound($s->getPosition(), new GhastShootSound());
    }
}