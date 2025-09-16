<?php

declare(strict_types=1);

namespace WaymarkTo;

use Dotenv\Dotenv;
use Nette;
use Nette\Bootstrap\Configurator;


class Bootstrap {
	private Configurator $configurator;
	private string $rootDir;


	public function __construct() {
		$this->rootDir = dirname(__DIR__);
		$this->configurator = new Configurator;
		$this->configurator->setTempDirectory($this->rootDir . '/temp');
	}

	public function env(string $varName, mixed $defaultValue = null): mixed {
		$value = getenv($varName);
		return $value === false ? $defaultValue : $value;
	}


	public function bootWebApplication(): Nette\DI\Container {
		$this->loadEnvironment();
		$this->initializeEnvironment();
		$this->setupContainer();
		return $this->configurator->createContainer();
	}

	private function loadEnvironment(): void {
		if (file_exists($this->rootDir . '/.env')) {
			$dotenv = Dotenv::createImmutable($this->rootDir);
			$dotenv->load();

			foreach ($_ENV as $key => $value) {
				putenv("$key=$value");
			}
		}
	}

	public function initializeEnvironment(): void {
		//$this->configurator->setDebugMode('secret@23.75.345.200'); // enable for your remote IP
		$this->configurator->enableTracy($this->rootDir . '/log');


		$this->configurator->setDebugMode(true);
	}


	private function setupContainer(): void {
		$this->configurator->addStaticParameters([
			'DB_HOST' => getenv('DB_HOST') ?: 'localhost',
			'DB_PORT' => getenv('DB_PORT') ?: '3306',
			'DB_NAME' => getenv('DB_NAME') ?: 'app_db',
			'DB_USERNAME' => getenv('DB_USERNAME') ?: 'root',
			'DB_PASSWORD' => getenv('DB_PASSWORD') ?: '',
			'DB_LAZY' => getenv('DB_LAZY') ?: true,
		]);

		$configDir = $this->rootDir . '/config';
		$this->configurator->addConfig($configDir . '/common.neon');
		$this->configurator->addConfig($configDir . '/services.neon');
	}

}
