<?php
/**
 *  _____    ____    ____   __  __  __  ______
 * |  __ \  / __ \  / __ \ |  \/  |/_ ||____  |
 * | |__) || |  | || |  | || \  / | | |    / /
 * |  _  / | |  | || |  | || |\/| | | |   / /
 * | | \ \ | |__| || |__| || |  | | | |  / /
 * |_|  \_\ \____/  \____/ |_|  |_| |_| /_/
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 */

declare(strict_types=1);

namespace room17\SkyBlock\session;


use pocketmine\Player;
use room17\SkyBlock\event\session\SessionCloseEvent;
use room17\SkyBlock\event\session\SessionOpenEvent;
use room17\SkyBlock\SkyBlock;

class SessionManager {

    /** @var SkyBlock */
    private $plugin;

    /** @var Session[] */
    private $sessions = [];

    /**
     * SessionManager constructor.
     * @param SkyBlock $plugin
     */
    public function __construct(SkyBlock $plugin) {
        $this->plugin = $plugin;
        $plugin->getServer()->getPluginManager()->registerEvents(new SessionListener($this), $plugin);
    }

    /**
     * @return SkyBlock
     */
    public function getPlugin(): SkyBlock {
        return $this->plugin;
    }

    /**
     * @return Session[]
     */
    public function getSessions(): array {
        return $this->sessions;
    }

    /**
     * @param Player $player
     * @return Session
     * @throws \ReflectionException
     */
    public function getSession(Player $player): Session {
        if(!$this->isSessionOpen($player)) {
            $this->openSession($player);
        }
        return $this->sessions[$player->getName()];
    }

    /**
     * @param Player $player
     * @return bool
     */
    public function isSessionOpen(Player $player): bool {
        return isset($this->sessions[$player->getName()]);
    }

    /**
     * @param string $username
     * @return null|OfflineSession
     */
    public function getOfflineSession(string $username): ?OfflineSession {
        return new OfflineSession($this, $username);
    }

    /**
     * @param Player $player
     * @throws \ReflectionException
     */
    public function openSession(Player $player): void {
        $this->sessions[$username = $player->getName()] = new Session($this, $player);
        (new SessionOpenEvent($this->sessions[$username]))->call();
    }

    /**
     * @param Player $player
     * @throws \ReflectionException
     */
    public function closeSession(Player $player): void {
        if(isset($this->sessions[$username = $player->getName()])) {
            $session = $this->sessions[$username];
            $session->save();
            (new SessionCloseEvent($session))->call();
            unset($this->sessions[$username]);
            if($session->hasIsland()) {
                $session->getIsland()->tryToClose();
            }
        }
    }

}