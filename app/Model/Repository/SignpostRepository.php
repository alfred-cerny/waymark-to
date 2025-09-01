<?php

declare(strict_types=1);

namespace WaymarkTo\Model\Repository;

use Nette;
use WaymarkTo\Model\DTO\Signpost;

class SignpostRepository {
	public function __construct(
		private Nette\Database\Explorer $database,
	) {}

	public function findByShortCode(string $shortCode): ?Nette\Database\Row {
		return $this->database
			->query('SELECT * FROM signpost WHERE short_code = ?', $shortCode)
			->fetch();
	}

	public function save(Signpost $dto): void {
		$data = $dto->getValueMap();
		$keys = array_keys($data);
		$values = array_values($data);
		$placeholders = str_repeat('?,', count($values) - 1) . '?';

		$this->database->query(
			'INSERT INTO signpost (' . implode(', ', $keys) . ') VALUES (' . $placeholders . ')',
			...$values
		);
	}

}