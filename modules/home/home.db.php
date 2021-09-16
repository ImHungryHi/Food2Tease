<?php

/**
 * Database klasse om de homepage te voorzien van de nodige data
 * -------------------------------------------------------------
 */

class HomeDB
{
  /**
   * Queries the database and returns a list of all categories
   * @return array
   */
  public static function getFoodCategories()
  {
		$categories = array();

		try {
			$db = new OneArchyDB(F2T_DB_HOST, F2T_DB_NAME, F2T_DB_USER, F2T_DB_PASS);
			$db->connect();

			$query = 'SELECT DISTINCT type FROM Products';
			$categories = $db->queryAll($query, []);
		}
		catch (Exception $e) {
      $errorMessage = 'Er is een fout opgetreden bij het oproepen van categoriën.';

      if ($db !== null) {
        $db->logError($errorMessage, ['exception' => $e->getMessage(), 'query' => $query, 'categories' => $categories, 'site' => 'home']);
      }

			throw new Exception($errorMessage);
		}

	  return $categories;
  }

  /**
   * Queries the database and returns a list of all products
   * @return array
   */
  public static function getAllProducts()
  {
    $products = array();

    try {
      $db = new OneArchyDB(F2T_DB_HOST, F2T_DB_NAME, F2T_DB_USER, F2T_DB_PASS);
      $db->connect();

      $query = 'SELECT * FROM Products';
      $products = $db->queryAll($query, []);
    }
    catch (Exception $e) {
      $errorMessage = 'Er is een fout opgetreden bij het oproepen van producten.';

      if ($db !== null) {
        $db->logError($errorMessage, ['exception' => $e->getMessage(), 'query' => $query, 'products' => $products, 'site' => 'home']);
      }

      throw new Exception($errorMessage);
    }

    return $products;
  }

  /**
   * Queries the database and returns a list of all products by category
   * @return array
   */
  public static function getProductsByCategory($category)
  {
    // Rework the category and create an array
    $category = (string) $category;
    $products = array();

    try {
      $db = new OneArchyDB(F2T_DB_HOST, F2T_DB_NAME, F2T_DB_USER, F2T_DB_PASS);
      $db->connect();

      $query = 'SELECT * FROM Products WHERE type = ?';
      $products = $db->queryAll($query, [$category]);
    }
    catch (Exception $e) {
      $errorMessage = 'Er is een fout opgetreden bij het oproepen van gefilterde producten.';

      if ($db !== null) {
        $db->logError($errorMessage, ['exception' => $e->getMessage(), 'query' => $query, 'products' => $products, 'category' => $category, 'site' => 'home']);
      }

			throw new Exception($errorMessage);
    }

    return $products;
  }

  /**
   * Queries the database and returns a list of all condiments for a certain product
   * @return array
   */
  public static function getCondimentsForProduct($id)
  {
    $condiments = array();

    try {
      $db = new OneArchyDB(F2T_DB_HOST, F2T_DB_NAME, F2T_DB_USER, F2T_DB_PASS);
      $db->connect();

      $query = 'SELECT Condiments.id AS id, Condiments.name AS name, Condiments.price AS price FROM Condiments
        INNER JOIN Products_has_Condiments ON Condiments.id = Products_has_Condiments.condimentId
          INNER JOIN Products ON Products_has_Condiments.productId = Products.id
            WHERE Products.id = ?';
      $condiments = $db->queryAll($query, [$id]);
    }
    catch (Exception $e) {
      $errorMessage = 'Er is een fout opgetreden bij het oproepen van sauzen voor een product.';

      if ($db !== null) {
        $db->logError($errorMessage, ['exception' => $e->getMessage(), 'query' => $query, 'condiments' => $condiments, 'productId' => $id, 'site' => 'home']);
      }

			throw new Exception($errorMessage);
    }

    return $condiments;
  }

  /**
   * Queries the database and returns the price of a certain product
   * @return int
   */
  public static function getProductPrice($id)
  {
    $results = ['price' => 0];

    try {
      $db = new OneArchyDB(F2T_DB_HOST, F2T_DB_NAME, F2T_DB_USER, F2T_DB_PASS);
      $db->connect();

      $query = 'SELECT price FROM Products WHERE id = ?';
      $results = $db->queryOne($query, [$id]);
    }
    catch (Exception $e) {
      $errorMessage = 'Er is een fout opgetreden bij het oproepen van een productprijs.';

      if ($db !== null) {
        $db->logError($errorMessage, ['exception' => $e->getMessage(), 'query' => $query, 'results' => $results, 'productId' => $id, 'site' => 'home']);
      }

			throw new Exception($errorMessage);
    }

    return floatval($results['price']);
  }

  /**
   * Queries the database and returns the price of a condiment
   * @return float
   */
  public static function getCondimentPrice($id)
  {
    $results = ['price' => 0];

    try {
      $db = new OneArchyDB(F2T_DB_HOST, F2T_DB_NAME, F2T_DB_USER, F2T_DB_PASS);
      $db->connect();

      $query = 'SELECT price FROM Condiments WHERE id = ?';
      $results = $db->queryOne($query, [$id]);
    }
    catch (Exception $e) {
      $errorMessage = 'Er is een fout opgetreden bij het oproepen van een sausprijs.';

      if ($db !== null) {
        $db->logError($errorMessage, ['exception' => $e->getMessage(), 'query' => $query, 'results' => $results, 'sauceId' => $id, 'site' => 'home']);
      }

			throw new Exception($errorMessage);
    }

    return floatval($results['price']);
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
        $db->logError($errorMessage, ['exception' => $e->getMessage(), 'query' => $query, 'results' => $results, 'site' => 'home']);
      }

			throw new Exception($errorMessage);
		}

	  return $results;
	}
}
?>
