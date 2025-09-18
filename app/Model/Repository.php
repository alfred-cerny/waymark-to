<?php

declare(strict_types=1);

namespace WaymarkTo\Model;

use Nette;

abstract class Repository extends \LeanMapper\Repository {
	
	public function persist(Entity|\LeanMapper\Entity $entity): void {
		parent::persist($entity);
		$entity->setAsNotNew();
	}

	public function findOne(array $conditions): ?Entity {
		$entities = $this->find($conditions, 1);
		return count($entities) > 0 ? $entities[0] : null;
	}

	public function find(array $conditions, int $limit = 10): array {
		$values = [];
		$whereClause = $this->buildWhereClause($conditions, $values);

		$rows = $this->connection
			->select('*')
			->from($this->getTable())
			->where($whereClause, $values)
			->limit($limit)
			->fetchAll();

		$entities = [];
		foreach ($rows as $row) {
			$entities[] = $this->createEntity($row);
		}

		return $entities;
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