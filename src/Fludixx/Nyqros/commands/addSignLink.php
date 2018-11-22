<?php

/**
 * Nyqros - addSignLink.php
 * @author Fludixx
 */

declare(strict_types=1);

namespace Fludixx\Nyqros\commands;

use Fludixx\Nyqros\Nyqros;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;

class addSignLink extends Command {

	public function __construct()
	{
		parent::__construct("sign", "Link a linked serevr to a Sign", "/sign <server>", ['linksign', 'linktosign', 'addsign']);
		$this->setPermission("nyqros.link");
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args)
	{
		if($sender->hasPermission("nyqros.link") or $sender->isOp()) {
			if(!isset($args[0])) {
				$sender->sendMessage(Nyqros::PREFIX."/sign <server>");
				return FALSE;
			} else {
				$allLinkedServes = Nyqros::getInstance()->provider->getLinkedServers();
				if(!isset($allLinkedServes[$args[0]])) {
					$sender->sendMessage(Nyqros::PREFIX."Server {$args[0]} nicht gefunden!");
					return FALSE;
				} else {
					$sender->sendMessage(Nyqros::PREFIX."Please break the Sign to Select it!");
					Nyqros::getInstance()->players[$sender->getName()]['setup'] = $args[0];
					return TRUE;
				}
			}
		} else {
			return FALSE;
		}

	}

}