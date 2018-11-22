<?php

/**
 * Nyqros - linkServer.php
 * @author Fludixx
 */

declare(strict_types=1);

namespace Fludixx\Nyqros\commands;

use Fludixx\Nyqros\Nyqros;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;

class linkServer extends Command {

	public function __construct()
	{
		parent::__construct("link", "/link <server>", "/link <server> <ip> <port>", ['linkserver']);
		$this->setPermission("nyqros.link");
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args)
	{
		if($sender->hasPermission("nyqros.link") or $sender->isOp()) {
			if (!isset($args[0]) or !isset($args[1]) or !isset($args[2])) {
				$sender->sendMessage(Nyqros::PREFIX . "/link <server> <ip> <port>");
				return FALSE;
			} else {
				$servername = $args[0];
				$ip = $args[1];
				$port = $args[2];
				$nyqros = Nyqros::getInstance();
				$nyqros->provider->linkServer($servername, (string)$ip, (int)$port);
				$sender->sendMessage(Nyqros::PREFIX . "Server sucessfully linked!");
				return true;
			}
		} else {
			return FALSE;
		}
	}
}