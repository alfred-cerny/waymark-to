<?php

declare(strict_types=1);

namespace WaymarkToTesting;

use Nette\DI\Container;

/**
 * Helper to store and retrieve the Nette DI Container for tests.
 */
class TestContainer {
	private static ?Container $container = null;

	public static function setContainer(Container $container): void {
		self::$container = $container;
	}

	public static function getContainer(): Container {
		if (self::$container === null) {
			throw new \LogicException('Nette DI Container not initialized for tests. Ensure Tester\'s global bootstrap is configured correctly and has run.');
		}
		return self::$container;
	}

}