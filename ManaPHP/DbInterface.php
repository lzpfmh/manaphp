<?php

namespace ManaPHP {

    use ManaPHP\Events\EventsAwareInterface;

    /**
     * \ManaPHP\Db\AdapterInterface initializer
     */
    interface DbInterface extends EventsAwareInterface
    {

        /**
         * Returns the first row in a SQL query result
         *
         * @param string $sqlQuery
         * @param array $binds
         * @param int $fetchMode
         * @return array|false
         */
        public function fetchOne($sqlQuery, $binds = null, $fetchMode = \PDO::FETCH_ASSOC);


        /**
         * Dumps the complete result of a query into an array
         *
         * @param string $sqlQuery
         * @param array $binds
         * @param int $fetchMode
         * @return array
         */
        public function fetchAll($sqlQuery, $binds = null, $fetchMode = \PDO::FETCH_ASSOC);


        /**
         * Inserts data into a table using custom RBDMS SQL syntax
         *
         * @param    string $table
         * @param    array $columnValues
         * @return    boolean
         */
        public function insert($table, $columnValues);


        /**
         * Updates data on a table using custom RBDMS SQL syntax
         *
         * @param    string $table
         * @param    array $columnValues
         * @param    string $whereCondition
         * @return    boolean
         */
        public function update($table, $whereCondition, $columnValues);


        /**
         * Deletes data from a table using custom RBDMS SQL syntax
         *
         * @param  string $table
         * @param  string $whereCondition
         * @param  array $binds
         * @return boolean
         */
        public function delete($table, $whereCondition, $binds = null);


        /**
         * Appends a LIMIT clause to $sqlQuery argument
         *
         * @param    string $sqlQuery
         * @param    int $number
         * @param   int $offset
         * @return    string
         */
        public function limit($sqlQuery, $number, $offset = null);


        /**
         * Returns a SQL modified with a FOR UPDATE clause
         *
         * @param string $sqlQuery
         * @return string
         */
        public function forUpdate($sqlQuery);


        /**
         * Returns a SQL modified with a LOCK IN SHARE MODE clause
         *
         * @param string $sqlQuery
         * @return string
         */
        public function sharedLock($sqlQuery);


        /**
         * Active SQL statement in the object
         *
         * @return string
         */
        public function getSQLStatement();


        /**
         * Active SQL statement in the object with replace the bind with value
         *
         * @param int $preservedStrLength
         * @return string
         * @throws \ManaPHP\Db\Exception
         */
        public function getEmulatePrepareSQLStatement($preservedStrLength = -1);


        /**
         * Active SQL statement in the object
         *
         * @return array
         */
        public function getSQLBindParams();


        /**
         * Sends SQL statements to the database server returning the success state.
         * Use this method only when the SQL statement sent to the server return rows
         *
         * @param  string $sqlStatement
         * @param  array $binds
         * @param int $fetchMode
         * @return \PDOStatement
         */
        public function query($sqlStatement, $binds = null, $fetchMode = \PDO::FETCH_ASSOC);


        /**
         * Sends SQL statements to the database server returning the success state.
         * Use this method only when the SQL statement sent to the server don't return any row
         *
         * @param  string $sqlStatement
         * @param  array $binds
         * @return int
         */
        public function execute($sqlStatement, $binds = null);


        /**
         * Returns the number of affected rows by the last INSERT/UPDATE/DELETE reported by the database system
         *
         * @return int
         */
        public function affectedRows();


        /**
         * Escapes a column/table/schema name
         *
         * @param string $identifier
         * @return string
         */
        public function escapeIdentifier($identifier);


        /**
         * Returns insert id for the auto_increment column inserted in the last SQL statement
         *
         * @return int
         */
        public function lastInsertId();


        /**
         * Starts a transaction in the connection
         *
         * @return boolean
         */
        public function begin();


        /**
         * Checks whether the connection is under a transaction
         *
         *<code>
         *    $connection->begin();
         *    var_dump($connection->isUnderTransaction()); //true
         *</code>
         */
        public function isUnderTransaction();

        /**
         * Rollbacks the active transaction in the connection
         *
         * @return boolean
         */
        public function rollback();


        /**
         * Commits the active transaction in the connection
         *
         * @return boolean
         */
        public function commit();


        /**
         * Return internal PDO handler
         *
         * @return \PDO
         */
        public function getInternalHandler();
    }
}
