<?php

/**
 * Nyqros - MySqlProvider.php
 * @author Fludixx
 */

declare(strict_types=1);

namespace Fludixx\Nyqros\provider;

use Fludixx\Nyqros\Nyqros;
use Fludixx\Nyqros\tasks\MySqlTask;

class MySqlProvider implements ProviderInterface {

	/** @var Nyqros */
	private $nyqros, $login;

	public function __construct()
	{
		$this->nyqros = Nyqros::getInstance();
		$this->login = (array)$this->nyqros->settings['mysql'];
		$this->nyqros->getServer()->getAsyncPool()->submitTask(new MySqlTask(
			"CREATE TABLE IF NOT EXISTS `linkedServers` (
			`serverName` VARCHAR(30) PRIMARY KEY,
			`serverAddress` VARCHAR(80) NOT NULL,
			`serverPort` SMALLINT UNSIGNED NOT NULL
		) ENGINE = InnoDB;", $this->login));
	}


	public function linkServer(string $name, string $ip, int $port) : void
	{
		$this->nyqros->getServer()->getAsyncPool()->submitTask(new MySqlTask(
			"INSERT INTO linkedServers (serverName, serverAddress, serverPort) VALUES ('$name', '$ip', $port)", $this->login));
	}

	public function unlinkServer(string $name) : void
	{
		$this->nyqros->getServer()->getAsyncPool()->submitTask(new MySqlTask(
			"DELETE FROM linkedServers WHERE serverName='$name'", $this->login));
	}

	public function getLinkedServers() : array
	{
		return [];
	}

}