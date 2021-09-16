<?php
/**
 * Since the Plonk database functions seem to be broken (mysql) I put some PDO stuff here instead
 */
class OneArchyDB {
  protected $hostname;
  protected $dbname;
  protected $username;
  protected $password;
  protected $dbHandler;

  /**
   * Your everyday standard constructor. Basically sets the hostname, dbname, username and password fields for use later on.
   * No connection though, just in case you don't want to do anything yet.
   * @return void
   */
  public function __construct($hostname, $dbname, $username, $password) {
    $this->hostname = (string) $hostname;
		$this->dbname	= (string) $dbname;
		$this->username	= (string) $username;
		$this->password	= (string) $password;
  }

  /**
   * Object destroyer - just disconnects the database.
   * @return void
   */
  public function __destruct() {
    $this->disconnect();
  }

  /**
   * This was the most secure creation of a database connection that I could find. If there's any securer method, feel free to @ me.
   * @return void
   */
  public function connect() {
    $pdoOptions = [
      PDO::ATTR_EMULATE_PREPARES => false,
      PDO::ATTR_STRINGIFY_FETCHES => false,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ];

    $this->dbHandler = new PDO('mysql:host=' . $this->hostname . ';dbname=' . $this->dbname, $this->username, $this->password, $pdoOptions);
  }

  /**
   * Just... destroys the database object (ie sets it to null). Cuts off the connection to the database.
   * @return void
   */
  public function disconnect() {
    // PDO makes it easy to disconnect. Just set the PDO object to NULL
    $this->dbHandler = NULL;
  }

  /**
   * Queries the database with select|insert|update|delete statements and returns the first item in its result set.
   * @return array
   */
  public function queryOne($query, $params = NULL) {
    if ($this->dbHandler === NULL) {
      $this->connect();
    }

    $stmt = $this->dbHandler->prepare($query);
    $stmt->execute($params);
    $result = $stmt->fetch();

    return $result;
  }

  /**
   * Queries the database with select|insert|update|delete statements and returns the result in its entirety.
   * @return array
   */
  public function queryAll($query, $params = NULL) {
    if ($this->dbHandler === NULL) {
      $this->connect();
    }

    $stmt = $this->dbHandler->prepare($query);
    $stmt->execute($params);
    $results = $stmt->fetchAll();

    return $results;
  }

  /**
   * Should return an id of the last insert (should be done ASAP if you want to know) in string form - so remember to parse!
   * @return string
   */
  public function getLastId() {
    if ($this->dbHandler === NULL) {
      $this->connect();
    }

    return $this->dbHandler->lastInsertId();
  }

  /**
   * Returns whatever the error thrown in the database was.
   * @return string
   */
  public function getError() {
    if ($this->dbHandler === NULL) {
      throw new Exception ('There can&apos;t be an error if there is no database object.');
    }
    else {
      return $this->dbHandler->errorCode();
    }
  }

  /**
   * Submits an error to the database for logging. Message preferably gives a hint as to where the error happened, arrDebugInfo contains parameters that may have
   * caused it and will be dumped into a string as well.
   * @param string $message
   * @param array $arrDebugInfo
   * @return void
   */
  public function logError($message, $arrDebugInfo) {
    if ($this->dbHandler === NULL) {
      $this->connect();
    }

    $message = (string) $message;
    $arrDebugInfo = (array) $arrDebugInfo;

    try {
      $query = 'INSERT INTO ErrorLogging(message, debugInfo) VALUES(?, ?)';
      $stmt = $this->dbHandler->prepare($query);
      $stmt->execute([$message, var_export($arrDebugInfo, true)]);
    }
    catch (Exception $ex) {
      throw new Exception('How do you debug debugging? This takes us to a whole new level of stuff-uppery...');
    }
  }
}
?>
