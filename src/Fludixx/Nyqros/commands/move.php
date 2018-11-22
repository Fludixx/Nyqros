<?php

/**
 * Nyqros - move.php
 * @author Fludixx
 */

declare(strict_types=1);

namespace Fludixx\Nyqros\commands;

use Fludixx\Nyqros\Nyqros;
use Fludixx\Nyqros\tasks\delayedTransfare;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;

class move extends Command {

	public function __construct()
	{
		parent::__construct("move", "/move <server>", "/move <server>", ['jumpto', 'moveto']);
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args)
	{
		if(isset($args[0])) {
			$servers = Nyqros::getInstance()->provider->getLinkedServers();
			if(isset($servers[$args[0]]) and $sender instanceof Player) {
				Nyqros::getInstance()->getScheduler()->scheduleDelayedTask(new delayedTransfare($sender,
					$servers[$args[0]]['address'], (int)$servers[$args[0]]['port']), 20);
				return TRUE;
			} else {
				$sender->sendMessage(Nyqros::PREFIX."Server wasen't found!");
				return FALSE;
			}
		} else {
			$sender->sendMessage(Nyqros::PREFIX."/move <server>");
			return FALSE;
		}
	}
}