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

namespace room17\SkyBlock\command\presets;


use room17\SkyBlock\command\IslandCommand;
use room17\SkyBlock\session\Session;
use room17\SkyBlock\utils\message\MessageContainer;

class SetSpawnCommand extends IslandCommand {

    /**
     * @return string
     */
    public function getName(): string {
        return "setspawn";
    }

    /**
     * @return MessageContainer
     */
    public function getUsageMessageContainer(): MessageContainer {
        return new MessageContainer("SET_SPAWN_USAGE");
    }

    /**
     * @return MessageContainer
     */
    public function getDescriptionMessageContainer(): MessageContainer {
        return new MessageContainer("SET_SPAWN_DESCRIPTION");
    }

    /**
     * @param Session $session
     * @param array $args
     */
    public function onCommand(Session $session, array $args): void {
        if($this->checkOfficer($session)) {
            return;
        } elseif($session->getPlayer()->getLevel() !== $session->getIsland()->getLevel()) {
            $session->sendTranslatedMessage(new MessageContainer("MUST_BE_IN_YOUR_ISLAND"));
        } else {
            $session->getIsland()->setSpawnLocation($session->getPlayer());
            $session->sendTranslatedMessage(new MessageContainer("SUCCESSFULLY_SET_SPAWN"));
        }
    }

}