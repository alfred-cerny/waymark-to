<?php

declare(strict_types=1);

namespace WaymarkTo\Model\Repository;

use Nette;
use WaymarkTo\Model\DTO\Signpost;

abstract class BaseRepository {
	public const TABLE_NAME = null;

	public function __construct(
		protected Nette\Database\Explorer $database,
	) {}


	public function findOne(array $conditions): ?Nette\Database\Row {
		return $this->find($conditions, 1);
	}

	public function find(array $conditions, int $limit = 10): ?Nette\Database\Row {
		$values = [];
		$whereClause = $this->buildWhereClause($conditions, $values);

		$result = $this->database->query(
			'SELECT * FROM ' . static::TABLE_NAME . " WHERE $whereClause LIMIT $limit",
			...$values
		);

		return $result->fetch();
	}

	/**
	 * Builds a WHERE clause from nested array conditions with AND/OR logic.
	 *
	 * Recursively processes an array of conditions to generate SQL WHERE clause syntax
	 * with proper parameter placeholders. Supports nested logical operators for complex queries.
	 *
	 * @param array $conditions Array of conditions in the following formats:
	 *                         - Simple conditions: ['column' => 'value']
	 *                         - AND conditions: ['AND' => [condition1, condition2, ...]]
	 *                         - OR conditions: ['OR' => [condition1, condition2, ...]]
	 *                         - Nested: ['AND' => [['OR' => [...]], ['column' => 'value']]]
	 * @param array &$values Reference to array that collects parameter values in order
	 *
	 * @return string Generated WHERE clause without the "WHERE" keyword
	 *
	 * @example
	 * // Simple condition
	 * $conditions = ['name' => 'John', 'status' => 'active'];
	 * // Result: "name = ? AND status = ?"
	 * // Values: ['John', 'active']
	 *
	 * @example
	 * // Complex nested condition
	 * $conditions = [
	 *     'status' => 'active',
	 *     'AND' => [
	 *         ['name' => 'John'],
	 *         'OR' => [
	 *             ['city' => 'Prague'],
	 *             ['city' => 'Brno']
	 *         ]
	 *     ]
	 * ];
	 * // Result: "status = ? AND (name = ? AND (city = ? OR city = ?))"
	 * // Values: ['active', 'John', 'Prague', 'Brno']
	 */
	private function buildWhereClause(array $conditions, array &$values): string {
		$clauses = [];

		foreach ($conditions as $key => $value) {
			if ($key === 'AND') {
				$subClauses = [];
				foreach ($value as $subCondition) {
					if (is_array($subCondition)) {
						$subClauses[] = '(' . $this->buildWhereClause($subCondition, $values) . ')';
					} else {
						// Handle simple key-value pairs within AND
						foreach ($subCondition as $column => $val) {
							$subClauses[] = $column . ' = ?';
							$values[] = $val;
						}
					}
				}
				$clauses[] = '(' . implode(' AND ', $subClauses) . ')';

			} elseif ($key === 'OR') {
				$subClauses = [];
				foreach ($value as $subCondition) {
					if (is_array($subCondition)) {
						$subClauses[] = '(' . $this->buildWhereClause($subCondition, $values) . ')';
					} else {
						// Handle simple key-value pairs within OR
						foreach ($subCondition as $column => $val) {
							$subClauses[] = $column . ' = ?';
							$values[] = $val;
						}
					}
				}
				$clauses[] = '(' . implode(' OR ', $subClauses) . ')';

			} else {
				// Simple column = value condition
				$clauses[] = $key . ' = ?';
				$values[] = $value;
			}
		}

		return implode(' AND ', $clauses);
	}

}