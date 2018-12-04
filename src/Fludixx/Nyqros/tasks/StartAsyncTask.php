<?php

/**
 * Nyqros - StartAsyncTask.php
 * @author Fludixx
 */

declare(strict_types=1);

namespace Fludixx\Nyqros\tasks;

use Fludixx\Nyqros\Nyqros;
use Fludixx\Nyqros\provider\MySqlProvider;
use pocketmine\scheduler\Task;

class StartAsyncTask extends Task {

	public function onRun(int $currentTick)
	{
		if(Nyqros::getInstance()->isQueryDone) {
			Nyqros::getInstance()->callAsyncTask();
		}
		if(Nyqros::getInstance()->provider instanceof MySqlProvider and Nyqros::getInstance()->isMysqlDone) {
			Nyqros::getInstance()->getServer()->getAsyncPool()->submitTask(new MySqlTask(
				"SELECT * FROM linkedServers", Nyqros::getInstance()->settings['mysql'],
				'linkedServers'
			));
			Nyqros::getInstance()->isMysqlDone = FALSE;
		}
	}

}