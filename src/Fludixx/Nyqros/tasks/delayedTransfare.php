<?php

/**
 * Nyqros - delayedTransfare.php
 * @author Fludixx
 */

declare(strict_types=1);

namespace Fludixx\Nyqros\tasks;

use Fludixx\Nyqros\Nyqros;
use pocketmine\Player;
use pocketmine\scheduler\Task;

class delayedTransfare extends Task {

	/** @var Player  */
	protected $player;
	/** @var string */
	protected $ip;
	/** @var int */
	protected $port;

	public function __construct(Player $player, string $ip, int $port)
	{
		$this->player = $player;
		$this->ip = $ip;
		$this->port = $port;
	}

	public function onRun(int $currentTick)
	{
		$main = Nyqros::getInstance();
		$this->player->transfer($this->ip, $this->port);
		$main->getScheduler()->cancelTask($this->getTaskId());
	}

}