<?php

/**
 * Nyqros - MySqlTask.php
 * @author Fludixx
 */

declare(strict_types=1);

namespace Fludixx\Nyqros\tasks;

use Fludixx\Nyqros\Nyqros;
use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;

class MySqlTask extends AsyncTask {

	protected $query, $login, $saveTo;

	public function __construct(string $query, array $login, $saveTo = NULL)
	{
		$this->query = $query;
		$this->login = $login;
		$this->saveTo = $saveTo;
	}

	public function onRun()
	{
		$conn = new \mysqli($this->login['host'], $this->login['username'], $this->login['password'], $this->login['database']);
		$res = $conn->query($this->query);
		if($res instanceof \mysqli_result) {
			if($this->saveTo !== NULL) {
				$dataToSave = [];
				while ($data = $res->fetch_array()) {
					$dataToSave[$data['serverName']] = [
						'address' => $data['serverAddress'],
						'port' => $data['serverPort']];
				}
				var_dump($dataToSave);
				$this->setResult($dataToSave);
			} else {
				$this->setResult($res, TRUE);
			}
		} else {
			$this->setResult(FALSE);
		}
	}

	public function onCompletion(Server $server)
	{
		if($this->saveTo !== NULL) {
			$saveTo = $this->saveTo;
			Nyqros::getInstance()->$saveTo = $this->getResult();
		}
		Nyqros::getInstance()->isMysqlDone = TRUE;
	}
}