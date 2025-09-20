<?php

$appContainerName = 'testing-app';

putenv('COMPOSE_FILE=tests/docker-compose.yml');

if (in_array('rebuild', $argv)) {
	passthru('docker compose up -d --build');
} else {
	exec('docker compose up -d');
}

passthru('docker ps');
passthru("docker exec -i $appContainerName bash -c 'php bin/console migrations:continue'");
passthru("docker exec -i $appContainerName bash -c 'composer tester tests'");
exec('docker compose down -v');