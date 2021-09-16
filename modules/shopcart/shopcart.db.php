<?php
/**
 * And you wondered where the model went? Well, look no further. Actually do, the stuff below is pretty interesting
 */
class ShopcartDB {
	/**
	 * Gets article information based on IDs that were given via array
	 * @param array $idPars
	 * @return array
	 */
	public static function getInfoByIdPairs($idPairs)
	{
	  // rework params
	  $idPairs = (array) $idPairs;
		$parsedIds = array();

	  foreach ($idPairs as $id) {
	    $parsedIds[] = [(int) $id[0], (int) $id[1]];
	  }

		try {
			$db = new OneArchyDB(F2T_DB_HOST, F2T_DB_NAME, F2T_DB_USER, F2T_DB_PASS);
			$db->connect();
			$products = array();

			foreach ($parsedIds as $idPair) {
				$query = '';

				if ($idPair[1] === 0) {
					$query = 'SELECT Products.id AS productId, Products.name AS productName, Products.price AS productPrice, Products.description AS description
						FROM Products WHERE Products.id = ?';
					// Nobody said we can't cheat... Better yet, I make the rules!
					$idPair = [$idPair[0]];
				}
				else {
					$query = 'SELECT Products.id AS productId, Products.name AS productName, Products.price AS productPrice, Products.description AS description, Condiments.id AS condimentId, Condiments.name AS condimentName, Condiments.price AS condimentPrice
						FROM Products INNER JOIN Products_has_Condiments ON Products.id = Products_has_Condiments.productId
							INNER JOIN Condiments ON Products_has_Condiments.condimentId = Condiments.id
								WHERE Products.id = ? AND Condiments.id = ?';
				}

				$products[] = $db->queryOne($query, $idPair);
			}
		}
		catch (Exception $e) {
      $errorMessage = 'Er is een fout opgetreden bij het oproepen van bepaalde sauzen.';

      if ($db !== null) {
        $db->logError($errorMessage, ['exception' => $e->getMessage(), 'query' => $query, 'results' => $products, 'idPairs' => $idPairs, 'parsedIds' => $parsedIds, 'site' => 'shopcart']);
      }

			throw new Exception($errorMessage);
		}

	  return $products;
	}

	/**
	 * Gets article information based on article IDs
	 * @param array $ids
	 * @return array
	 */
	public static function getProductInfoByIds($ids) {
		$ids = (array) $ids;
		$parsedIds = array();
		$products = array();

		// Reparse individually
		foreach ($ids as $id) {
			$parsedIds[] = (int) $id;
		}

		try {
			$db = new OneArchyDB(F2T_DB_HOST, F2T_DB_NAME, F2T_DB_USER, F2T_DB_PASS);
			$db->connect();

			$parameterLine = '(' . implode(',', array_fill(0, count($ids), '?')) . ')';
			$query = 'SELECT id, name, description, price
				FROM Products WHERE id in ' . $parameterLine;
			$products = $db->queryAll($query, $parsedIds);
		}
		catch (Exception $e) {
      $errorMessage = 'Er is een fout opgetreden bij het oproepen van artikelen.';

      if ($db !== null) {
        $db->logError($errorMessage, ['exception' => $e->getMessage(), 'query' => $query, 'results' => $products, 'ids' => $ids, 'parsedIds' => $parsedIds, 'site' => 'shopcart']);
      }

			throw new Exception($errorMessage);
		}

	  return $products;
	}

	/**
	 * Gets article sauces based on article IDs
	 * @param array $ids
	 * @return array
	 */
	public static function getProductSauces($ids) {
		$ids = (array) $ids;
		$parsedIds = array();
		$results = array();

		// Reparse individually
		foreach ($ids as $id) {
			$parsedIds[] = (int) $id;
		}

		try {
			$db = new OneArchyDB(F2T_DB_HOST, F2T_DB_NAME, F2T_DB_USER, F2T_DB_PASS);
			$db->connect();

			$parameterLine = '(' . implode(',', array_fill(0, count($ids), '?')) . ')';
			$query = 'SELECT Products_has_Condiments.productId AS productId, Condiments.id AS condimentId, Condiments.name AS condimentName, Condiments.price AS condimentPrice
				FROM Products_has_Condiments INNER JOIN Condiments ON Products_has_Condiments.condimentId = Condiments.Id
					WHERE productId in ' . $parameterLine;
			$results = $db->queryAll($query, $parsedIds);
		}
		catch (Exception $e) {
      $errorMessage = 'Er is een fout opgetreden bij het oproepen van artikelen.';

      if ($db !== null) {
        $db->logError($errorMessage, ['exception' => $e->getMessage(), 'query' => $query, 'results' => $results, 'ids' => $ids, 'parsedIds' => $parsedIds, 'site' => 'shopcart']);
      }

			throw new Exception($errorMessage);
		}

	  return $results;
	}

	/**
	 * Gets sauce information based on article ID
	 * @param int $id
	 * @return array
	 */
	public static function getSaucesByArticleId($id)
	{
	  // rework params
	  $id = (int) $id;
		$query = '';
		$sauces = array();

		try {
			$db = new OneArchyDB(F2T_DB_HOST, F2T_DB_NAME, F2T_DB_USER, F2T_DB_PASS);
			$db->connect();

			$query = 'SELECT Condiments.id AS condimentId, Condiments.name AS condimentName, Condiments.price AS condimentPrice
				FROM Products_has_Condiments INNER JOIN Condiments ON Products_has_Condiments.condimentId = Condiments.id
						WHERE Products_has_Condiments.productId = ?';
			$sauces = $db->queryAll($query, [$id]);
		}
		catch (Exception $e) {
      $errorMessage = 'Er is een fout opgetreden bij het oproepen van bepaalde sauzen per artikel.';

      if ($db !== null) {
        $db->logError($errorMessage, ['exception' => $e->getMessage(), 'query' => $query, 'results' => $sauces, 'id' => $id, 'site' => 'shopcart']);
      }

			throw new Exception($errorMessage);
		}

	  return $sauces;
	}

	/**
	 * Gets all extra sauce information
	 * @return array
	 */
	public static function getExtraSauces()
	{
	  // rework params
		$results = array();

		try {
			$db = new OneArchyDB(F2T_DB_HOST, F2T_DB_NAME, F2T_DB_USER, F2T_DB_PASS);
			$db->connect();

			$query = 'SELECT id, name, price
				FROM Condiments WHERE type = "Koude saus"';
			$results = $db->queryAll($query, []);
		}
		catch (Exception $e) {
      $errorMessage = 'Er is een fout opgetreden bij het oproepen van extra-sausinfo.';

      if ($db !== null) {
        $db->logError($errorMessage, ['exception' => $e->getMessage(), 'query' => $query, 'results' => $results, 'site' => 'shopcart']);
      }

			throw new Exception($errorMessage);
		}

	  return $results;
	}
}
?>
