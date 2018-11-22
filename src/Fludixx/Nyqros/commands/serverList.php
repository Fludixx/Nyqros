<?php

/**
 * Nyqros - serverList.php
 * @author Fludixx
 */

declare(strict_types=1);

namespace Fludixx\Nyqros\commands;

use Fludixx\Nyqros\Nyqros;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;

class serverList extends Command {

	public function __construct()
	{
		parent::__construct("listservers", "/link <server>", "/link <server> <ip> <port>", ['nyqros']);
		$this->setPermission("nyqros.link");
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args)
	{
		$servers = Nyqros::getInstance()->provider->getLinkedServers();
		foreach ($servers as $name => $data) {
			$sender->sendMessage(" - $name");
		}
		return TRUE;
	}
}