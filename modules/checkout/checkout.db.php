<?php
/**
 * And you wondered where the model went? Well, look no further. Actually do, the stuff below is pretty interesting
 */
class CheckoutDB {
	/**
	 * Checks if all article info is valid (condimentId and productId). Returns true if it is.
	 * @param array $articleInfo
	 * @return bool
	 */
	public static function checkProductArrayValidity($articleInfo) {
		// Rework params
		$articleInfo = (array) $articleInfo;

		// Create some new arrays for Checking
		$productIds = array();
		$condimentIds = array();
		$productCount = ['count' => 0];
		$condimentCount = ['count' => 0];

		foreach ($articleInfo as $articleRow) {
			if (!in_array($articleRow['id'], $productIds)) {
				$productIds[] = $articleRow['id'];
			}

			if ($articleRow['sauceId'] !== 0 && !in_array($articleRow['sauceId'], $condimentIds)) {
				$condimentIds[] = $articleRow['sauceId'];
			}
		}

		try {
			$db = new OneArchyDB(F2T_DB_HOST, F2T_DB_NAME, F2T_DB_USER, F2T_DB_PASS);
			$db->connect();

			if (count($productIds) > 0) {
				$parameterLine = '(' . implode(',', array_fill(0, count($productIds), '?')) . ')';
				$query = 'SELECT COUNT(*) AS count FROM Products WHERE id IN ' . $parameterLine;
				$productCount = $db->queryOne($query, $productIds);
			}

			if (count($condimentIds) > 0) {
				$parameterLine = '(' . implode(',', array_fill(0, count($condimentIds), '?')) . ')';
				$query = 'SELECT COUNT(*) AS count FROM Condiments WHERE id IN ' . $parameterLine;
				$condimentCount = $db->queryOne($query, $condimentIds);
			}

			if (count($productIds) === $productCount['count'] && count($condimentIds) === $condimentCount['count']) {
				return true;
			}
		}
		catch (Exception $e) {
      $errorMessage = 'Er is een fout opgelopen bij het tellen van producten en sauzen.';

      if ($db !== null) {
				$debugInfo = [
					'exception' => $e->getMessage(),
					'articleInfo' => $articleInfo,
					'productIds' => $productIds,
					'condimentIds' => $condimentIds,
					'productCount' => $productCount,
					'condimentCount' => $condimentCount,
					'parameterLine' => $parameterLine,
					'query' => $query,
					'site' => 'checkout'
				];

        $db->logError($errorMessage, $debugInfo);
      }

			throw new Exception($errorMessage);
		}

		return false;
	}

	/**
	 * Inserts the necessary information into the database to make an order. Returns the order ID on success
	 * @param array $personalData
	 * @param array $articleInfo
	 * @param string $comment
	 * @return int
	 */
	public static function insertOrder($personalData, $articleInfo, $comment = '')
	{
	  // Rework params
	  $personalData = (array) $personalData;
	  $articleInfo = (array) $articleInfo;
	  $comment = (string) $comment;

		// Initialize some objects for use
		$insertArticles = [];
		$orderId = -1;
		$query = '';
		$clientId = -1;
		$columnsLine = '(lastName, firstName, email, phoneNumber, addressLine1, postal1, city1)';
		$valuesLine = '(?, ?, ?, ?, ?, ?, ?)';
		$clientInfo = [
			$personalData['lastName'], // lastName
			$personalData['firstName'], // firstName
			$personalData['mail'], // email
			$personalData['phone'], // phoneNumber
			$personalData['address'], // addressLine1
			$personalData['postal'], // postal1
			$personalData['city'] // city1
		];

		if (isset($personalData['multiAddress']) && $personalData['multiAddress']) {
			$columnsLine = '(lastName, firstName, email, phoneNumber, addressLine1, postal1, city1, addressLine2, postal2, city2)';
			$valuesLine = '(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
			$clientInfo = [
				$personalData['lastName'], // lastName
				$personalData['firstName'], // firstName
				$personalData['mail'], // email
				$personalData['phone'], // phoneNumber
				$personalData['address'], // addressLine1
				$personalData['postal'], // postal1
				$personalData['city'], // city1
				$personalData['addressExtra'], // addressLine1
				$personalData['postalExtra'], // postal1
				$personalData['cityExtra'] // city1
			];
		}

		try {
			$db = new OneArchyDB(F2T_DB_HOST, F2T_DB_NAME, F2T_DB_USER, F2T_DB_PASS);
			$db->connect();

			// Checking if the client already exists
			$query = 'SELECT id FROM Clients WHERE firstName = ? AND lastName = ? AND addressLine1 = ?';
			$result = $db->queryOne($query, [$personalData['firstName'], $personalData['lastName'], $personalData['address']]);

			// Adding a client if it doesn't exist
			if ($result === false) {
				$query = 'INSERT INTO Clients ' . $columnsLine . ' VALUES ' . $valuesLine;
				$result = $db->queryOne($query, $clientInfo);
				$clientId = $db->getLastId();
			}
			elseif (gettype($result['id']) === 'integer') {
				$clientId = $result['id'];

				if ($result['addressLine2'] !== $personalData['addressExtra']) {
					$query = 'UPDATE Clients SET addressLine2 = ?, postal2 = ?, city2 = ? WHERE id = ?';
					$result = $db->queryOne($query, [$personalData['addressExtra'], $personalData['postalExtra'], $personalData['cityExtra'], $clientId]);
				}
			}

			// Adding an order
			$columnsLine = '(clientId, comment, orderedOn)';
			$valuesLine = '(?, ?, utc_timestamp())';

			$query = 'INSERT INTO Orders ' . $columnsLine . ' VALUES ' . $valuesLine;
			$result = $db->queryOne($query, [$clientId, $comment]);
			$orderId = $db->getLastId();

			// Now rehash the article array to be a simple number-indexed array including the orderId
			if (self::checkProductArrayValidity($articleInfo)) {
				foreach ($articleInfo as $article) {
					if ($article['sauceId'] === 0) {
						$insertArticles[] = [$orderId, $article['id'], null, $article['quantity']];
					}
					else {
						$insertArticles[] = [$orderId, $article['id'], $article['sauceId'], $article['quantity']];
					}
				}
			}

			// Adding articles to the order
			if (count($insertArticles) > 0) {
				$columnsLine = '(orderId, productId, condimentId, quantity)';
				$valuesLine = '(?, ?, ?, ?)';

				foreach ($insertArticles as $insertArticle) {
					$query = 'INSERT INTO Orders_has_Products ' . $columnsLine . ' VALUES ' . $valuesLine;
					$result = $db->queryOne($query, $insertArticle);
				}
			}
		}
		catch (Exception $e) {
      $errorMessage = 'Er is een fout opgetreden bij het toevoegen van een nieuwe klant en/of bestelling.';

      if ($db !== null) {
				$debugInfo = [
					'exception' => $e->getMessage(),
					'personalData' => $personalData,
					'comment' => $comment,
					'articleInfo' => $articleInfo,
					'clientInfo' => $clientInfo,
					'insertArticles' => $insertArticles,
					'columnsLine' => $columnsLine,
					'valuesLine' => $valuesLine,
					'query' => $query,
					'result' => $result,
					'clientId' => $clientId,
					'orderId' => $orderId,
					'site' => 'checkout'
				];

        $db->logError($errorMessage, $debugInfo);
      }

			throw new Exception($errorMessage);
		}

		return $orderId;
	}

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
        $db->logError($errorMessage, ['exception' => $e->getMessage(), 'query' => $query, 'results' => $price, 'sauceId' => $id, 'site' => 'checkout']);
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
        $db->logError($errorMessage, ['exception' => $e->getMessage(), 'query' => $query, 'results' => $price, 'productId' => $id, 'site' => 'checkout']);
      }

			throw new Exception($errorMessage);
		}

	  return $price['price'];
	}

	/**
	 * Gets client info based on order id
	 * @param int $orderId
	 * @return array
	 */
	public static function getClientInfoFromOrder($orderId) {
		$orderId = (int) $orderId;
		$clientInfo = [];

		if ($orderId < 1) {
			throw new Exception('Ongeldig bestelnummer opgegeven.');
		}

    try {
      $db = new OneArchyDB(F2T_DB_HOST, F2T_DB_NAME, F2T_DB_USER, F2T_DB_PASS);
      $db->connect();

      $query = 'SELECT Clients.firstName AS firstName, Clients.lastName AS lastName, Clients.email AS email, Clients.phoneNumber AS phoneNumber, Clients.addressLine1 AS addressLine1, Clients.postal1 AS postal1, Clients.city1 AS city1, Clients.addressLine2 AS addressLine2, Clients.postal2 AS postal2, Clients.city2 AS city2
        FROM Clients INNER JOIN Orders ON Clients.id = Orders.clientId WHERE Orders.id = ?';
      $clientInfo = $db->queryOne($query, [$orderId]);
    }
    catch (Exception $e) {
      $errorMessage = 'Er is een fout opgetreden bij het oproepen van klantinfo.';

      if ($db !== null) {
        $db->logError($errorMessage, ['exception' => $e->getMessage(), 'query' => $query, 'results' => $clientInfo, 'orderId' => $orderId, 'site' => 'checkout']);
      }

			throw new Exception($errorMessage);
    }

		return $clientInfo;
	}

	/**
	 * Gets product info based on id
	 * @param int $id
	 * @return array
	 */
	public static function getProductInfoById($id) {
		$id = (int) $id;
		$productInfo = [];

		if ($id < 1) {
			throw new Exception('Ongeldig productnummer opgegeven: ' . $id);
		}

    try {
      $db = new OneArchyDB(F2T_DB_HOST, F2T_DB_NAME, F2T_DB_USER, F2T_DB_PASS);
      $db->connect();

			$query = 'SELECT name, price FROM Products WHERE id = ?';
			$productInfo = $db->queryOne($query, [$id]);
    }
    catch (Exception $e) {
      $errorMessage = 'Er is een fout opgetreden bij het oproepen van productinfo.';

      if ($db !== null) {
        $db->logError($errorMessage, ['exception' => $e->getMessage(), 'query' => $query, 'results' => $productInfo, 'productId' => $id, 'site' => 'checkout']);
      }

			throw new Exception($errorMessage);
    }

		return $productInfo;
	}

	/**
	 * Gets sauce info based on id
	 * @param int $id
	 * @return array
	 */
	public static function getSauceInfoById($id) {
		$id = (int) $id;
		$sauceInfo = [];

		if ($id < 1) {
			throw new Exception('Ongeldig sausnummer opgegeven: ' . $id);
		}

    try {
      $db = new OneArchyDB(F2T_DB_HOST, F2T_DB_NAME, F2T_DB_USER, F2T_DB_PASS);
      $db->connect();

			$query = 'SELECT name, price FROM Condiments WHERE id = ?';
			$sauceInfo = $db->queryOne($query, [$id]);
    }
    catch (Exception $e) {
      $errorMessage = 'Er is een fout opgetreden bij het oproepen van sausinfo.';

      if ($db !== null) {
        $db->logError($errorMessage, ['exception' => $e->getMessage(), 'query' => $query, 'results' => $sauceInfo, 'sauceId' => $id, 'site' => 'checkout']);
      }

			throw new Exception($errorMessage);
    }

		return $sauceInfo;
	}

	/**
	 * Pass through error handling to the database
	 * @param array info
	 * @return void
	 */
	public static function logError($info) {
		try {
			$db = new OneArchyDB(F2T_DB_HOST, F2T_DB_NAME, F2T_DB_USER, F2T_DB_PASS);
      $db->connect();
			$db->logError($info['errorMessage'], $info['content']);
		}
		catch (Exception $e) {
			throw new Exception('Even error logging failed? Wow! ' . $e->getMessage());
		}
	}
}
?>
