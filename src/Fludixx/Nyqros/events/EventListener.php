<?php

/**
 * Nyqros - EventListener.php
 * @author Fludixx
 */

declare(strict_types=1);

namespace Fludixx\Nyqros\events;

use Fludixx\Nyqros\Nyqros;
use Fludixx\Nyqros\tasks\delayedTransfare;
use pocketmine\block\Block;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\SignChangeEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\server\QueryRegenerateEvent;
use pocketmine\item\Item;
use pocketmine\tile\Sign;
use pocketmine\level\particle\DestroyBlockParticle;

class EventListener implements Listener {

	protected $main;

	public function __construct()
	{
		$this->main = Nyqros::getInstance();
	}

	public function onSignChange(SignChangeEvent $event) {
		$ll = $event->getLine(3);
		if($ll == $this->main->settings['join']
			or $ll == $this->main->settings['full']
			or $ll == $this->main->settings['offline']) {
			$event->getPlayer()->sendMessage(Nyqros::PREFIX."Something went wrong!");
			$event->getBlock()->getLevel()->setBlock($event->getBlock()->asVector3(), Block::get(0));
			$event->getBlock()->getLevel()->addParticle(new DestroyBlockParticle($event->getBlock()->asVector3(), $event->getBlock()));
			if($event->getPlayer()->isSurvival()) {
				$event->getBlock()->getLevel()->dropItem($event->getBlock()->asVector3(), Item::get(Item::SIGN));
			}
		}
	}

	public function onBreakBlock(BlockBreakEvent $event) {
		if($this->main->players[$event->getPlayer()->getName()]['setup'] !== FALSE) {
			$event->setCancelled();
			$block = $event->getBlock();
			$sign = $block->getLevel()->getTile($block->asVector3());
			if($sign instanceof Sign) {
				$sign->setLine(0, $this->main->players[$event->getPlayer()->getName()]['setup']);
				$sign->setLine(1, '§aConnecting...');
				$sign->setLine(3, $this->main->settings['offline']);
				$event->getPlayer()->sendMessage(Nyqros::PREFIX."Sucessfully printed Sign!");
			} else {
				$event->getPlayer()->sendMessage(Nyqros::PREFIX."abort.");
			}
			$this->main->players[$event->getPlayer()->getName()]['setup'] = FALSE;
		}
	}

	public function onClickOnSmth(PlayerInteractEvent $event) {
		$player = $event->getPlayer();
		$sign = $event->getBlock()->getLevel()->getTile($event->getBlock()->asVector3());
		if($sign instanceof Sign) {
			if($sign->getLine(3) == $this->main->settings['join'] and
				$this->main->players[$player->getName()]['cooldown'] < time()) {
				$data = $this->main->provider->getLinkedServers()[$sign->getLine(0)];
				$player->sendMessage(Nyqros::PREFIX."Transfaring to ".$sign->getLine(0)."...");
				$this->main->getScheduler()->scheduleDelayedTask(new delayedTransfare($player, $data['address'],
					(int)$data['port']), 10);
				$this->main->players[$player->getName()]['cooldown'] = time();
			} else {
				if($sign->getLine(3) == $this->main->settings['full']
				or $sign->getLine(3) == $this->main->settings['offline']) {
					if($this->main->players[$player->getName()]['cooldown'] < time()) {
						$player->sendMessage(Nyqros::PREFIX."Can not connect to Server!");
						$this->main->players[$player->getName()]['cooldown'] = time();
					}
				}
			}
		}
	}

	public function onQueryRegen(QueryRegenerateEvent $event) {
		if(Nyqros::getInstance()->settings['displayLinkedPlayers']) {
			$event->setPlayerCount(Nyqros::getInstance()->allPlayers+$event->getPlayerCount());
			$event->setMaxPlayerCount(Nyqros::getInstance()->maxPlayers+$event->getMaxPlayerCount());
		}
	}

	public function onLogin(PlayerLoginEvent $event) {
		$this->main->players[$event->getPlayer()->getName()] = [
			'cooldown' => time(),
			'setup'    => FALSE
		];
	}

}