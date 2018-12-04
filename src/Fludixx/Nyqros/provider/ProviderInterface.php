<?php

/**
 * Nyqros - ProviderInterface.php
 * @author Fludixx
 */

declare(strict_types=1);

namespace Fludixx\Nyqros\provider;

interface ProviderInterface {

	/**
	 * @param string $name
	 * @param string $ip
	 * @param int    $port
	 */
	public function linkServer(string $name, string $ip, int $port) : void ;

	/**
	 * @param string $name
	 */
	public function unlinkServer(string $name) : void ;

	/** @return array */
	public function getLinkedServers() : array;
}