<?php
/**
 * And you wondered where the model went? Well, look no further. Actually do, the stuff below is pretty interesting
 */
class ThanksDB {
	/**
	 * Gets article information based on IDs that were given via array
	 * @param array $ids
	 * @return array
	 */
	public static function getArticleInfoByIds($ids)
  {
    // rework params
    $ids = (array) $ids;
		$parsedIds = array();

    foreach ($ids as $id) {
      $parsedIds[] = (int) $id;
    }

		try {
			$db = new OneArchyDB(F2T_DB_HOST, F2T_DB_NAME, F2T_DB_USER, F2T_DB_PASS);
			$db->connect();

			$in = str_repeat('?,', count($parsedIds) - 1) . '?';
			$query = 'SELECT * FROM Products WHERE id IN (' . $in . ')';
			$products = $db->queryAll($query, $parsedIds);
		}
		catch (Exception $e) {
      $errorMessage = 'Er is een fout opgetreden bij het oproepen van artikelinfos.';

      if ($db !== null) {
        $db->logError($errorMessage, ['exception' => $e->getMessage(), 'query' => $query, 'results' => $products, 'parameterLine' => $in, 'ids' => $ids, 'parsedIds' => $parsedIds, 'site' => 'thanks']);
      }

			throw new Exception($errorMessage);
		}

    return $products;
	}

	/**
	 * Gets article information based on ID
	 * @param int $id
	 * @return array
	 */
	public static function getProductInfoById($id)
  {
    // rework params
    $id = (int) $id;

		try {
			$db = new OneArchyDB(F2T_DB_HOST, F2T_DB_NAME, F2T_DB_USER, F2T_DB_PASS);
			$db->connect();

			$query = 'SELECT * FROM Products WHERE id = ?';
			$product = $db->queryOne($query, [$id]);
		}
		catch (Exception $e) {
      $errorMessage = 'Er is een fout opgetreden bij het oproepen van artikelinfo.';

      if ($db !== null) {
        $db->logError($errorMessage, ['exception' => $e->getMessage(), 'query' => $query, 'results' => $product, 'id' => $id, 'site' => 'thanks']);
      }

			throw new Exception($errorMessage);
		}

    return $product;
	}

	/**
	 * Gets article information based on IDs that were given via array
	 * @param array $idPairs
	 * @return array
	 */
	public static function getInfoByIdPairs($idPairs)
	{
	  // rework params
	  $idPairs = (array) $idPairs;
		$parsedIds = array();
		$products = array();

	  foreach ($idPairs as $id) {
	    $parsedIds[] = [(int) $id[0], (int) $id[1]];
	  }

		try {
			$db = new OneArchyDB(F2T_DB_HOST, F2T_DB_NAME, F2T_DB_USER, F2T_DB_PASS);
			$db->connect();

			foreach ($parsedIds as $idPair) {
				$query = '';

				if ($idPair[1] === 0) {
					$query = 'SELECT Products.id AS productId, Products.name AS productName, Products.price AS productPrice
						FROM Products WHERE Products.id = ?';
					// Nobody said we can't cheat... Better yet, I make the rules!
					$idPair = [$idPair[0]];
				}
				else {
					$query = 'SELECT Products.id AS productId, Products.name AS productName, Products.price AS productPrice, Condiments.id AS condimentId, Condiments.name AS condimentName, Condiments.price AS condimentPrice
						FROM Products INNER JOIN Products_has_Condiments ON Products.id = Products_has_Condiments.productId
							INNER JOIN Condiments ON Products_has_Condiments.condimentId = Condiments.id
								WHERE Products.id = ? AND Condiments.id = ?';
				}

				$products[] = $db->queryOne($query, $idPair);
			}
		}
		catch (Exception $e) {
      $errorMessage = 'Er is een fout opgetreden bij het oproepen van artikelinfos per id paren.';

      if ($db !== null) {
        $db->logError($errorMessage, ['exception' => $e->getMessage(), 'query' => $query, 'results' => $products, 'idPairs' => $idPairs, 'parsedIds' => $parsedIds, 'site' => 'thanks']);
      }

			throw new Exception($errorMessage);
		}

	  return $products;
	}

	/**
	 * Gets article information based on IDs that were given via array
	 * @param array $ids
	 * @return array
	 */
	public static function getInfoByProductIds($ids)
	{
	  // rework params
	  $ids = (array) $ids;
		$parsedIds = array();

	  foreach ($ids as $id) {
	    $parsedIds[] = (int) $id;
	  }

		try {
			$db = new OneArchyDB(F2T_DB_HOST, F2T_DB_NAME, F2T_DB_USER, F2T_DB_PASS);
			$db->connect();

			$in = str_repeat('?,', count($parsedIds) - 1) . '?';
			$query = 'SELECT Products.id AS productId, Products.name AS productName, Products.price AS productPrice, Products.description AS description, Condiments.id AS condimentId, Condiments.name AS condimentName, Condiments.price AS condimentPrice
				FROM Products INNER JOIN Products_has_Condiments ON Products.id = Products_has_Condiments.productId
					INNER JOIN Condiments ON Products_has_Condiments.condimentId = Condiments.id
						WHERE Products.id IN (' . $in . ')';
			$products = $db->queryAll($query, $parsedIds);
		}
		catch (Exception $e) {
      $errorMessage = 'Er is een fout opgetreden bij het oproepen van artikelinfos per id.';

      if ($db !== null) {
        $db->logError($errorMessage, ['exception' => $e->getMessage(), 'query' => $query, 'results' => $products, 'parameterLine' => $in, 'ids' => $ids, 'parsedIds' => $parsedIds, 'site' => 'thanks']);
      }

			throw new Exception($errorMessage);
		}

	  return $products;
	}

	/**
	 * Gets sauce information based on product ID
	 * @param int $ids
	 * @return array
	 */
	public static function getSauceInfoByProductId($id)
	{
	  // rework params
	  $id = (int) $id;
		$results = array();

		try {
			$db = new OneArchyDB(F2T_DB_HOST, F2T_DB_NAME, F2T_DB_USER, F2T_DB_PASS);
			$db->connect();

			$query = 'SELECT Condiments.id AS id, Condiments.name AS name, Condiments.price AS price
				FROM Products_has_Condiments INNER JOIN Condiments ON Products_has_Condiments.condimentId = Condiments.id
						WHERE Products_has_Condiments.productId = ?';
			$results = $db->queryAll($query, [$id]);
		}
		catch (Exception $e) {
      $errorMessage = 'Er is een fout opgetreden bij het oproepen van sausinfo.';

      if ($db !== null) {
        $db->logError($errorMessage, ['exception' => $e->getMessage(), 'query' => $query, 'results' => $results, 'id' => $id, 'site' => 'thanks']);
      }

			throw new Exception($errorMessage);
		}

	  return $results;
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
        $db->logError($errorMessage, ['exception' => $e->getMessage(), 'query' => $query, 'results' => $results, 'site' => 'thanks']);
      }

			throw new Exception($errorMessage);
		}

	  return $results;
	}
}
?>
