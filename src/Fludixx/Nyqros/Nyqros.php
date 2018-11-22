<?php

/**
 * Nyqros - Nyqros.php
 * @author Fludixx
 */

declare(strict_types=1);

namespace Fludixx\Nyqros;

use Fludixx\Nyqros\commands\addSignLink;
use Fludixx\Nyqros\commands\linkServer;
use Fludixx\Nyqros\commands\move;
use Fludixx\Nyqros\commands\serverList;
use Fludixx\Nyqros\commands\unlinkServer;
use Fludixx\Nyqros\events\EventListener;
use Fludixx\Nyqros\provider\ProviderInterface;
use Fludixx\Nyqros\provider\YamlProvider;
use Fludixx\Nyqros\tasks\SignAsyncTask;
use Fludixx\Nyqros\tasks\StartAsyncTask;
use pocketmine\tile\Sign;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat as f;

class Nyqros extends PluginBase {

	/** @var string  */
	const NAME = "Nyqros";
	/** @var string  */
	const PREFIX = f::DARK_GRAY."» ".f::AQUA.Nyqros::NAME.f::DARK_GRAY." | ".f::WHITE;
	/** @var Nyqros */
	private static $instance;
	/** @var array */
	public $settings;
	/** @var ProviderInterface */
	public $provider;
	public $players = [];
	public $isQueryDone = TRUE;
	/** @var int */
	public $allPlayers;
	/** @var int */
	public $maxPlayers;

	public function onEnable() : void {
		Nyqros::$instance = $this;
		@mkdir($this->getDataFolder());
		if(!is_file($this->getDataFolder()."/config.yml")) {
			$c = $this->getConfig();
			$c->setAll([
				'provider' => 'yaml',
				'displayLinkedPlayers' => TRUE,
				'developer_mode' => FALSE,
				'join' => '§aJOIN',
				'full' => '§cFULL',
				'offline' => '§cOFFLINE',
				'mysql' => [
					'host'     => '127.0.0.1',
					'username' => 'root',
					'password' => 'toor',
					'database' => 'database'
				]
			]);
			$c->save();
			$this->getLogger()->notice("Created Config!");
		}
		$this->settings = $this->getConfig()->getAll();
		if($this->settings['developer_mode']) {
			$this->getLogger()->debug("Configuration:");
			var_dump($this->settings);
		}
		switch ($this->settings['provider']) {
			case 'yml':
			case 'yaml':
				$this->provider = new YamlProvider();
				break;
			default:
				$this->provider = new YamlProvider();
				break;
		}
		if($this->settings['developer_mode']) {
			$this->getLogger()->debug("Provider {$this->settings['provider']}");
		}
		$this->regEvents();
		$this->regCommands();
		$this->getScheduler()->scheduleRepeatingTask(new StartAsyncTask(), 120);
		$this->getLogger()->info(Nyqros::PREFIX."Loaded!");
	}

	/** @return Sign[]|null */
	public function getSigns() {
		$signs = [];
		foreach ($this->getServer()->getLevels() as $level) {
			foreach ($level->getTiles() as $sign) {
				if($sign instanceof Sign and
					($sign->getLine(3) == $this->settings['join']
						or $sign->getLine(3) == $this->settings['full']
						or $sign->getLine(3) == $this->settings['offline'])) {
					$signs[] = $sign;
				}
			}
		}
		return count($signs) > 0 ? $signs : null;
	}

	public function callAsyncTask() {
		if($this->settings['developer_mode']) {
			$this->getLogger()->debug("calling AsyncTask");
		}
		$this->isQueryDone = FALSE;
		$this->getServer()->getAsyncPool()->submitTask(new SignAsyncTask($this->provider->getLinkedServers()));
	}

	public function AsyncTaskAuswertung(array $data) {
		$this->isQueryDone = TRUE;
		if($this->settings['developer_mode']) {
			$this->getLogger()->debug("AsyncTask evaluation:");
			var_dump($data);
		}
		$signs = $this->getSigns();
		$this->allPlayers = 0;
		$this->maxPlayers = 0;
		if($signs !== NULL) {
			foreach ($signs as $sign) {
				$name = $sign->getLine(0);
				if (!isset($data[$name])) {
					$data[$name] = ['num' => 0, 'max' => 0, 'motd' => f::RED . "---"];
					$state = $this->settings['offline'];
					$pcolor = f::RED;
				} else {
					if(is_string($data[$name]['motd'])) {
						$pcolor = $data[$name]['num'] >= $data[$name]['max'] ? f::RED : f::GREEN;
						$state = $data[$name]['num'] >= $data[$name]['max'] ? $this->settings['full'] : $this->settings['join'];
					} else {
						$data[$name] = ['num' => 0, 'max' => 0, 'motd' => f::RED . "---"];
						$state = $this->settings['offline'];
						$pcolor = f::RED;
					}
				}
				$sign->setText("$name",
					f::WHITE . $data[$name]['motd'],
					$pcolor . $data[$name]['num'] . f::GRAY . "/" . f::RED . $data[$name]['max'],
					$state);
				if($this->settings['displayLinkedPlayers']) {
					$this->allPlayers = $this->allPlayers + (int)$data[$name]['num'];
					$this->maxPlayers = $this->maxPlayers + (int)$data[$name]['max'];
				}
			}
		}
	}

	protected function regEvents() : void {
		$this->getServer()->getPluginManager()->registerEvents(new EventListener(), $this);
	}

	protected function regCommands() : void {
		$map = $this->getServer()->getCommandMap();
		$map->register("link", new linkServer());
		$map->register("unlink", new unlinkServer());
		$map->register("sign", new addSignLink());
		$map->register("serverList", new serverList());
		$map->register("move", new move());
	}

	/**
	 * @return Nyqros
	 */
	public static function getInstance() : Nyqros {
		return Nyqros::$instance;
	}

}