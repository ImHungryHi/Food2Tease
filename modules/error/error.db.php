<?php
/**
 * And you wondered where the model went? Well, look no further. Actually do, the stuff below is pretty interesting
 */
class ErrorDB {
  // Voorlopig geen interesse in foutlogging richting database, kan later eventueel wel geïmplementeerd worden.

	/**
	 * Gets sauce price from the database
	 * @param int $id
	 * @return int
	 */
	public static function getSaucePrice($id)
	{
	  // rework params
	  $id = (int) $id;

		try {
			$db = new OneArchyDB(F2T_DB_HOST, F2T_DB_NAME, F2T_DB_USER, F2T_DB_PASS);
			$db->connect();

			$query = 'SELECT price FROM Condiments WHERE id = ?';
			$price = $db->queryOne($query, [$id]);
		}
		catch (Exception $e) {
      $errorMessage = 'Er is een fout opgetreden bij het oproepen van een sausprijs.';

      if ($db !== null) {
        $db->logError($errorMessage, ['exception' => $e->getMessage(), 'query' => $query, 'results' => $price, 'sauceId' => $id, 'site' => 'error']);
      }

			throw new Exception($errorMessage);
		}

	  return $price['price'];
	}

	/**
	 * Gets sauce price from the database
	 * @param int $id
	 * @return int
	 */
	public static function getProductPrice($id)
	{
	  // rework params
	  $id = (int) $id;

		try {
			$db = new OneArchyDB(F2T_DB_HOST, F2T_DB_NAME, F2T_DB_USER, F2T_DB_PASS);
			$db->connect();

			$query = 'SELECT price FROM Products WHERE id = ?';
			$price = $db->queryOne($query, [$id]);
		}
		catch (Exception $e) {
      $errorMessage = 'Er is een fout opgetreden bij het oproepen van een productprijs.';

      if ($db !== null) {
        $db->logError($errorMessage, ['exception' => $e->getMessage(), 'query' => $query, 'results' => $price, 'productId' => $id, 'site' => 'error']);
      }

			throw new Exception($errorMessage);
		}

	  return $price['price'];
	}
}
?>
