<?php

declare(strict_types=1);

namespace WaymarkToTesting;

require __DIR__ . '/../../vendor/autoload.php';

use Nette\Bootstrap\Configurator;
use Tester\Environment;

final class Bootstrap {
	private string $rootDir;
	private Configurator $configurator;

	public function __construct() {
		Environment::setup();

		$this->rootDir = dirname(__DIR__, 2);
		$this->configurator = new Configurator();

		$this->initializeConfigurator();
	}

	public function env(string $varName, mixed $defaultValue = null): mixed {
		$value = getenv($varName);
		return $value === false ? $defaultValue : $value;
	}

	private function initializeConfigurator(): void {
		$this->configurator->setTempDirectory(__DIR__ . '/temp');
		// $this->configurator->enableTracy(__DIR__ . '/log');
		$this->configurator->setDebugMode(true);

		$this->configurator->addStaticParameters([
			'DB_HOST' => getenv('DB_HOST') ?: 'localhost',
			'DB_PORT' => getenv('DB_PORT') ?: '6543',
			'DB_NAME' => getenv('DB_NAME') ?: 'testing',
			'DB_USERNAME' => getenv('DB_USERNAME') ?: 'user',
			'DB_PASSWORD' => getenv('DB_PASSWORD') ?: 'password',
			'DB_LAZY' => (bool)(getenv('DB_LAZY') ?: true),
		]);

		$configDir = $this->rootDir . '/config';
		$this->configurator->addConfig($configDir . '/common.neon');
		$this->configurator->addConfig($configDir . '/services.neon');

		if (file_exists(__DIR__ . '/config.test.neon')) {
			$this->configurator->addConfig(__DIR__ . '/config.test.neon');
		}
	}

	public function createContainer(): \Nette\DI\Container {
		$container = $this->configurator->createContainer();
		TestContainer::setContainer($container);

		return $container;
	}

}

return (new \WaymarkToTesting\Bootstrap())->createContainer();