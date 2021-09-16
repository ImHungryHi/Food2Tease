<?php
require_once 'Database.php';

class OneArchySession {
  /**
  * Creates a session in the database system
  * @return
  */
  public static function start() {
    if (session_id()) return;

    // 2 hours lifetime, root path, no domain, non-secure, httponly for some security and adding all of this into an array with samesite cookies
    $params = [
    	'lifetime' => 7200,
    	'path' => '/',
    	'domain' => '',
    	'secure' => false,
    	'httponly' => true,
    	'samesite' => 'lax'	// Either Lax or Strict (allows cross-site get requests or not)
    ];
    $customName = base64_encode('FoodDqdUzyIqheqXnCkMtLXveTqG2Tease');

    // Actually start it with the above parameters + custom hashed name (bcrypt = blowfish, slightly less secure than scrypt but cross-platform instead)
    session_set_cookie_params($params);
    session_name($customName);
    session_start();
    session_regenerate_id();
  }

  /**
  * Creates a session in the database system
  * @return
  */
  public static function destroy() {
		if (!session_id()) self::start();

		// Delete all session data
		foreach ($_SESSION as $k => $v) unset($_SESSION[$k]);

		// Destroy the session
		@session_destroy();
  }

  /**
  * Get information from the database based on a key input. Throws exception if it doesn't exist.
  * @return object
  */
  public static function get($key) {
    if (!session_id()) self::start();

    // Check if
    if (self::exists($key)) return $_SESSION[$key];

    throw new Exception('The session key ' . $key . ' doesn\'t exist.');
  }

  /**
  * Set a value in the database session
  * @return
  */
  public static function set($key, $value) {
    // Start session if needed and parse key into string
    if (!session_id()) self::start();
    $key = (string) $key;

    $_SESSION[$key] = $value;
  }

  /**
  * Set a value in the database session
  * @return
  */
  public static function remove($key) {
    // Start session if needed and parse key into string
    if (!session_id()) self::start();
    $key = (string) $key;

    unset($_SESSION[$key]);
  }

  /**
  * Checks if a key is present in the session
  * @return bool
  */
  public static function exists($key) {
    // Start session if needed and parse key into string
    if (!session_id()) self::start();
    $key = (string) $key;

    // Do the check and return true if found
    if (isset($_SESSION[$key])) return true;

    // We've not found anything at this point so it doesn't exist
    return false;
  }
}
?>
