<?php

declare(strict_types=1);

namespace WaymarkTo\Model\Repository;

use Nette;
use WaymarkTo\Model\DTO\Signpost;

class SignpostRepository extends BaseRepository {
	public const TABLE_NAME = 'signpost';

	/**
	 * @param Signpost $dto
	 * @return bool true => success
	 */
	public function save(Signpost $dto): bool {
		$data = $dto->getValueMap();
		$keys = array_keys($data);
		$values = array_values($data);
		$placeholders = str_repeat('?,', count($values) - 1) . '?';

		$result = $this->database->query(
			'INSERT INTO ' . self::TABLE_NAME . ' (' . implode(', ', $keys) . ') VALUES (' . $placeholders . ')',
			...$values
		);
		return $result->getRowCount() > 0;
	}

}