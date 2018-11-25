<?php

/**
 * Nyqros - YamlProvider.php
 * @author Fludixx
 */

declare(strict_types=1);

namespace Fludixx\Nyqros\provider;

use Fludixx\Nyqros\Nyqros;
use pocketmine\utils\Config;

class JsonProvider implements ProviderInterface {

	/** @var Nyqros */
	private $nyqros;
	/** @var Config */
	private $config;

	public function __construct()
	{
		$this->nyqros = Nyqros::getInstance();
		$this->config = new Config($this->nyqros->getDataFolder()."/servers.json", 2);
	}

	public function linkServer(string $name, string $ip, int $port) : void
	{
		$this->config->set($name, [
			'address' => $ip,
			'port'    => $port,
			'name'    => $name
		]);
		$this->config->save();
	}

	public function unlinkServer(string $name) : void
	{
		$this->config->remove($name);
		$this->config->save();
	}

	public function getLinkedServers() : array
	{
		return $this->config->getAll();
	}

}