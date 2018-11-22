<?php

/**
 * Nyqros - ProviderInterface.php
 * @author Fludixx
 */

declare(strict_types=1);

namespace Fludixx\Nyqros\provider;

interface ProviderInterface {

	/** @return null */
	public function linkServer(string $name, string $ip, int $port) : void ;

	/** @return null */
	public function unlinkServer(string $name) : void ;

	/** @return array */
	public function getLinkedServers() : array;
}