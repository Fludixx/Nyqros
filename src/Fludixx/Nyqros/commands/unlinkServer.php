<?php

/**
 * Nyqros - unlinkServer.php
 * @author Fludixx
 */

declare(strict_types=1);

namespace Fludixx\Nyqros\commands;

use Fludixx\Nyqros\Nyqros;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;

class unlinkServer extends Command {

	public function __construct()
	{
		parent::__construct("unlink", "/unlink <server>", "/unlink <server>", ['unlinkserver']);
		$this->setPermission("nyqros.unlink");
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args)
	{
		if($sender->hasPermission("nyqros.unlink") or $sender->isOp()) {
			if (!isset($args[0])) {
				$sender->sendMessage(Nyqros::PREFIX . "/unlink <server>");
				return FALSE;
			} else {
				$servername = $args[0];
				$nyqros = Nyqros::getInstance();
				$nyqros->provider->unlinkServer($servername);
				$sender->sendMessage(Nyqros::PREFIX . "Server sucessfully unlinked!");
				return true;
			}
		} else {
			return FALSE;
		}
	}

}