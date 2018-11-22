<?php

/**
 * Nyqros - StartAsyncTask.php
 * @author Fludixx
 */

declare(strict_types=1);

namespace Fludixx\Nyqros\tasks;

use Fludixx\Nyqros\Nyqros;
use pocketmine\scheduler\Task;

class StartAsyncTask extends Task {

	public function onRun(int $currentTick)
	{
		if(Nyqros::getInstance()->isQueryDone) {
			Nyqros::getInstance()->callAsyncTask();
		}
	}

}