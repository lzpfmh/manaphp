<?php 

namespace ManaPHP\Mvc\Model\Query {

	/**
	 * ManaPHP\Mvc\Model\Query\BuilderInterface initializer
	 */
	
	interface BuilderInterface {

		public function distinct($distinct);


		public function getDistinct();


		/**
		 * Sets the columns to be queried
		 *
		 * @param string|array $columns
		 * @return static
		 */
		public function columns($columns);


		/**
		 * Return the columns to be queried
		 *
		 * @return string|array
		 */
		public function getColumns();


		/**
		 * Sets the models who makes part of the query
		 *
		 * @param string|array $models
		 * @return static
		 */
		public function from($models);


		/**
		 * Add a model to take part of the query
		 *
		 * @param string $model
		 * @param string $alias
		 * @return static
		 */
		public function addFrom($model, $alias=null);


		/**
		 * Return the models who makes part of the query
		 *
		 * @return string|array
		 */
		public function getFrom();


		/**
		 * Adds a INNER join to the query
		 *
		 * @param string $model
		 * @param string $conditions
		 * @param string $alias
		 * @param string $type
		 * @return static
		 */
		public function join($model, $conditions=null, $alias=null,$type=null);


		/**
		 * Adds a INNER join to the query
		 *
		 * @param string $model
		 * @param string $conditions
		 * @param string $alias
		 * @return static
		 */
		public function innerJoin($model, $conditions=null, $alias=null);


		/**
		 * Adds a LEFT join to the query
		 *
		 * @param string $model
		 * @param string $conditions
		 * @param string $alias
		 * @return static
		 */
		public function leftJoin($model, $conditions=null, $alias=null);


		/**
		 * Adds a RIGHT join to the query
		 *
		 * @param string $model
		 * @param string $conditions
		 * @param string $alias
		 * @return static
		 */
		public function rightJoin($model, $conditions=null, $alias=null);


		/**
		 * Sets conditions for the query
		 *
		 * @param string $conditions
		 * @param array $binds
		 * @return static
		 */
		public function where($conditions, $binds=null);


		/**
		 * Appends a condition to the current conditions using a AND operator
		 *
		 * @param string $conditions
		 * @param array $binds
		 * @return static
		 */
		public function andWhere($conditions, $binds=null);


		/**
		 * Appends a condition to the current conditions using a OR operator
		 *
		 * @param string $conditions
		 * @param array $binds
		 * @return static
		 */
		public function orWhere($conditions, $binds=null);


		/**
		 * Appends a BETWEEN condition to the current conditions
		 *
		 * @param string $expr
		 * @param mixed $minimum
		 * @param mixed $maximum
		 * @return static
		 */
		public function betweenWhere($expr, $minimum, $maximum);


		/**
		 * Appends a NOT BETWEEN condition to the current conditions
		 *
		 *<code>
		 *	$builder->notBetweenWhere('price', 100.25, 200.50);
		 *</code>
		 *
		 * @param string $expr
		 * @param mixed $minimum
		 * @param mixed $maximum
		 * @return static
		 */
		public function notBetweenWhere($expr, $minimum, $maximum);


		/**
		 * Appends an IN condition to the current conditions
		 *
		 * @param string $expr
		 * @param array $values
		 * @return static
		 */
		public function inWhere($expr, $values);


		/**
		 * Appends a NOT IN condition to the current conditions
		 *
		 * @param string $expr
		 * @param array $values
		 * @return static
		 */
		public function notInWhere($expr, $values);


		/**
		 * Return the conditions for the query
		 *
		 * @return string|array
		 */
		public function getWhere();


		/**
		 * Sets a ORDER BY condition clause
		 *
		 * @param string $orderBy
		 * @return static
		 */
		public function orderBy($orderBy);


		/**
		 * Return the set ORDER BY clause
		 *
		 * @return string|array
		 */
		public function getOrderBy();


		/**
		 * Sets a HAVING condition clause
		 *
		 * @param string $having
		 * @return static
		 */
		public function having($having);


		/**
		 * Returns the HAVING condition clause
		 *
		 * @return string|array
		 */
		public function getHaving();


		/**
		 * Sets a LIMIT clause
		 *
		 * @param int $limit
		 * @param int $offset
		 * @return static
		 */
		public function limit($limit, $offset=null);


		/**
		 * Returns the current LIMIT clause
		 *
		 * @return string|array
		 */
		public function getLimit();


		/**
		 * Sets a LIMIT clause
		 *
		 * @param string $group
		 * @return static
		 */
		public function groupBy($group);


		/**
		 * Returns the GROUP BY clause
		 *
		 * @return string
		 */
		public function getGroupBy();


		/**
		 * Returns a PHQL statement built based on the builder parameters
		 *
		 * @return string
		 */
		public function getPhql();


		/**
		 * Returns the query built
		 *
		 * @return \ManaPHP\Mvc\Model\QueryInterface
		 */
		public function getQuery();


		/**
		 * Set default bind parameters
		 *
		 * @param array $binds
		 * @param bool $merge
		 * @return static
		 */
		public function setBinds($binds, $merge = false);
	}
}
