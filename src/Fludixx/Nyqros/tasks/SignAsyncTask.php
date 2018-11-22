<?php

/**
 * Nyqros - AsyncTask.php
 * @author Fludixx
 */

declare(strict_types=1);

namespace Fludixx\Nyqros\tasks;

use Fludixx\Nyqros\Nyqros;
use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;

class SignAsyncTask extends AsyncTask {

	protected $linkedServers;

	public function __construct(array $ls)
	{
		$this->linkedServers = $ls;
	}

	/** @return array|bool */
	public static function getQueryInfo(string $host, int $port, int $timeout = 4) {
		$socket = @fsockopen('udp://' . $host, $port, $errno, $errstr, $timeout);
		if($errno or $socket === false) {
			return FALSE;
		}
		stream_Set_Timeout($socket, $timeout);
		stream_Set_Blocking($socket, true);
		$randInt = mt_rand(1, 999999999);
		$reqPacket = "\x01";
		$reqPacket .= pack('Q*', $randInt);
		$reqPacket .= "\x00\xff\xff\x00\xfe\xfe\xfe\xfe\xfd\xfd\xfd\xfd\x12\x34\x56\x78";
		$reqPacket .= pack('Q*', 0);
		fwrite($socket, $reqPacket, strlen($reqPacket));
		$response = fread($socket, 4096);
		fclose($socket);
		if (empty($response) or $response === false) {
			return FALSE;
		}
		if (substr($response, 0, 1) !== "\x1C") {
			return FALSE;
		}
		$serverInfo = substr($response, 35);
		//$serverInfo = preg_replace("#§.#", "", $serverInfo);
		$serverInfo = explode(';', $serverInfo);
		return [
			'motd' => $serverInfo[1],
			'num' => $serverInfo[4],
			'max' => $serverInfo[5],
			'version' =>  $serverInfo[3]
		];
	}

	public function onRun() : void
	{
		$data = [];
		foreach ($this->linkedServers as $name => $server) {
			$info = SignAsyncTask::getQueryInfo((string)$server['address'], (int)$server['port']);
			$data[$name] = $info;
		}
		$this->setResult($data);
	}

	public function onCompletion(Server $server)
	{
		Nyqros::getInstance()->AsyncTaskAuswertung($this->getResult());
	}

}