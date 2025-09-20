<?php

$appContainerName = 'testing-app';

putenv('COMPOSE_FILE=tests/docker-compose.yml');

if (in_array('rebuild', $argv)) {
	passthru('docker compose up -d --build');
} else {
	exec('docker compose up -d');
}

passthru('docker ps');
passthru("docker exec -i $appContainerName bash -c 'php bin/console migrations:reset'");
passthru("docker exec -i $appContainerName bash -c 'composer tester'");
exec('docker compose down -v');