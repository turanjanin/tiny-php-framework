<?php

/**
 * Class DB
 */
class DB
{
	/**
	 * @var DB Singleton instance of DB class.
	 */
	private static $dbInstance;
	/**
	 * @var PDO
	 */
	protected $pdo;

	/**
	 * @var string[] List of executed queries.
	 */
	protected $queries = array();

	/**
	 * Tries to connect to database. Otherwise, shows error message.
	 */
	private function __construct()
	{
		try {
			// Connect to database.
			$connectionString = 'mysql:host=' . Config::get('db_server') . ';dbname=' . Config::get('db_name');
			$this->pdo = new PDO($connectionString, Config::get('db_user'), Config::get('db_password'));
			$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
			
			$this->pdo->exec('SET NAMES utf8');
			
		} catch(PDOException $e) {
			if (Config::get('debug')) {
				Helper::debugError($e, ['description' => 'Database connection error']);
			} else {
				Helper::showError('Database is unavailable. Try again later.');
			}
		}
	}

	/**
	 * Executes database query.
	 *
	 * Executes query on database after replacing {db_prefix} which can be used
	 * as a placeholder for database table prefix.
	 * In order to prevent SQL injection, this method utilizes PDO's named parameters
	 *
	 * @see http://php.net/manual/en/pdo.prepare.php
	 *
	 * @param string $sql Query to be executed.
	 * @param array $params List of query parameters to be used.
	 * @return PDOStatement
	 */
	public static function query($sql, $params = array())
	{
		$db = DB::instance();
		
		$sql = str_replace('{db_prefix}', Config::get('db_prefix'), $sql);
		
		$timeStart = microtime(true);
		
		try {
			$statement = $db->pdo->prepare($sql);
			
			if (count($params) > 0) {
				foreach ($params as $key => $value) {
					$param = (is_numeric($value)) ? PDO::PARAM_INT : PDO::PARAM_STR;
					$statement->bindValue(':' . $key, $value, $param);
				}
			}
			
			$statement->execute();
		
		} catch(PDOException $e) {
			if (Config::get('debug')) {
				$trace = $e->getTrace();

				$data = array(
					'file' => $trace[1]['file'],
					'line' => $trace[1]['line'],
					'query' => $sql,
					'params' => $params
				);
				
				Helper::debugError($e, $data);

			} else {
				Helper::showError('There are some problems with the database. Please, try again later.');
			}
		}
		
		$timeEnd = microtime(true);

		$db->queries[] = array(
			'query' => $sql,
			'params' => $params,
			'duration' => $timeEnd - $timeStart
		);
		
		return $statement;
	}

	/**
	 * Fetches all rows from a database.
	 *
	 * @param string $sql Query to be executed.
	 * @param array $params Optional query parameters to be replaced.
	 * @return array
	 */
	public static function getArray($sql, $params = array())
	{
		return self::query($sql, $params)->fetchAll();
	}

	/**
	 * Fetches single row from database.
	 *
	 * Fetches single row from database and returns an array indexed by column number
	 * as returned in your result set, starting at column 0. This method can be used in
	 * combination with list() function.
	 *
	 * @uses DB::query()
	 * @param string $sql Query to be executed.
	 * @param array $params Optional query parameters to be replaced.
	 * @return array
	 */
	public static function getRow($sql, $params = array())
	{
		return self::query($sql, $params)->fetch(PDO::FETCH_NUM);
	}

	/**
	 * Inserts new row in given table.
	 *
	 * @uses DB::query()
	 * @param string $table Database table for insertion.
	 * @param array $data Data to be inserted in form column => value.
	 * @return PDOStatement
	 */
	public static function insert($table, array $data)
	{
		$fields = array_keys($data);

		$sql = 'INSERT INTO ' . $table . ' (' . implode(', ', $fields) . ') ';
		$sql .= 'VALUES (:' . implode(', :', $fields) . ')';

		return self::query($sql, $data);
	}

	/**
	 * Returns the ID of the last inserted row.
	 *
	 * @return string
	 */
	public static function lastInsertId()
	{
		return self::instance()->pdo->lastInsertId();
	}

	/**
	 * This magic method must be private to support Singleton pattern.
	 */
	private function __clone()
	{
	}

	/**
	 * Returns singleton instance of DB class.
	 *
	 * @return DB
	 */
	public static function instance()
	{
		if (!self::$dbInstance) {
			self::$dbInstance = new DB();
		}
		
		return self::$dbInstance;
	}
}
