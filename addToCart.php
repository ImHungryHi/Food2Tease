<?php
/**
 * ------------------------------
 * Includes
 * ------------------------------
 */
  require_once '../config.php';
  require_once './library/OneArchy/Session.php';
  require_once './library/OneArchy/Database.php';

  try {
    OneArchySession::start();
    $id = -1;
    $sauce = -1;
    $extraSauce = 0;
    $extraFries = 0;
    $quantity = -1;
    $arrArticles = array();
    $arrUpdated = array();

    if (OneArchySession::exists('shopcartItems')) {
      $arrArticles = OneArchySession::get('shopcartItems');
    }
    else {
      OneArchySession::set('shopcartItems', []);
    }

    if (isset($_GET['id']) && isset($_GET['sauce']) && isset($_GET['extraSauce']) && isset($_GET['extraFries']) && isset($_GET['quantity']) && $_GET['id'] !== '' && $_GET['sauce'] !== '' && $_GET['extraSauce'] !== '' && $_GET['extraFries'] !== '' && $_GET['quantity'] !== '') {
      $id = intval($_GET['id']);
      $sauce = intval($_GET['sauce']);
      $extraSauce = intval($_GET['extraSauce']);
      $extraFries = intval($_GET['extraFries']);
      $quantity = intval($_GET['quantity']);

      if ($id > 0 && $sauce > 0 && $quantity > 0) {
        $db = new OneArchyDB(F2T_DB_HOST, F2T_DB_NAME, F2T_DB_USER, F2T_DB_PASS);
  			$db->connect();

        // Are there any items in the database with this id-sauce pair?
        $query = 'SELECT COUNT(*) AS count FROM Products_has_Condiments
          WHERE Products_has_Condiments.productId = ? AND Products_has_Condiments.condimentId = ?';
        $results = $db->queryOne($query, [$id, $sauce]);
        $query = 'SELECT COUNT(*) AS count FROM Condiments WHERE id = ?';
        $extraResults = $db->queryOne($query, [$extraSauce]);

        if (intval($extraResults['count']) < 1) {
          $extraSauce = 0;
        }

        if ($extraFries > 0) {
          $extraFries = 1;
        }
        else {
          $extraFries = 0;
        }

        if (isset($results['count']) && intval($results['count'] > 0)) {
          // Add to session
          if (OneArchySession::exists('shopcartItems')) {
            // Extract, add and reinsert
            $arrArticles = OneArchySession::get('shopcartItems');
            $isPresent = false;

            foreach ($arrArticles as $article) {
              if ($article['id'] === $id && $article['sauceId'] === $sauce && $article['extraSauceId'] === $extraSauce && $article['extraFries'] === $extraFries) {
                $arrUpdated[] = ['id' => $article['id'], 'sauceId' => $article['sauceId'], 'extraSauceId' => $article['extraSauceId'], 'extraFries' => $article['extraFries'], 'quantity' => intval($article['quantity']) + $quantity];
                $isPresent = true;
              }
              else {
                $arrUpdated[] = $article;
              }
            }

            if (!$isPresent) {
              $arrUpdated[] = ['id' => $id, 'sauceId' => $sauce, 'extraSauceId' => $extraSauce, 'extraFries' => $extraFries, 'quantity' => $quantity];
            }

            OneArchySession::set('shopcartItems', $arrUpdated);
          }
          else {
            OneArchySession::set('shopcartItems', [['id' => $id, 'sauceId' => $sauce, 'extraSauceId' => $extraSauce, 'extraFries' => $extraFries, 'quantity' => $quantity]]);
          }
        }
      }
      elseif ($id > 0 && $sauce === 0 && $quantity > 0) {
        $db = new OneArchyDB(F2T_DB_HOST, F2T_DB_NAME, F2T_DB_USER, F2T_DB_PASS);
  			$db->connect();

        // Are there any products in the database with this id?
        $query = 'SELECT COUNT(*) AS count FROM Products WHERE id = ?';
        $results = $db->queryOne($query, [$id]);
        $query = 'SELECT COUNT(*) AS count FROM Condiments WHERE id = ?';
        $extraResults = $db->queryOne($query, [$extraSauce]);

        if (intval($extraResults['count']) < 1) {
          $extraSauce = 0;
        }

        if ($extraFries > 0) {
          $extraFries = 1;
        }
        else {
          $extraFries = 0;
        }

        if (isset($results['count']) && (intval($results['count']) > 0)) {
          // Check if there are sauces
          $query = 'SELECT COUNT(*) AS count FROM Products_has_Condiments
            WHERE Products_has_Condiments.productId = ?';
          $results = $db->queryOne($query, [$id]);

          if (isset($results['count']) && intval($results['count']) > 0) {
            // This item has sauces, we've been duped! No insertion here
          }
          else {
            // Add to session
            if (OneArchySession::exists('shopcartItems')) {
              // Extract, add and reinsert
              $arrArticles = OneArchySession::get('shopcartItems');
              $arrUpdated = array();
              $isPresent = false;

              foreach ($arrArticles as $article) {
                if ($article['id'] === $id && $article['sauceId'] === $sauce && $article['extraSauceId'] === $extraSauce && $article['extraFries'] === $extraFries) {
                  $arrUpdated[] = ['id' => $article['id'], 'sauceId' => 0, 'extraSauceId' => $extraSauce, 'extraFries' => $extraFries, 'quantity' => intval($article['quantity']) + $quantity];
                  $isPresent = true;
                }
                else {
                  $arrUpdated[] = $article;
                }
              }

              if (!$isPresent) {
                $arrUpdated[] = ['id' => $id, 'sauceId' => 0, 'extraSauceId' => $extraSauce, 'extraFries' => $extraFries, 'quantity' => $quantity];
              }

              OneArchySession::set('shopcartItems', $arrUpdated);
            }
            else {
              OneArchySession::set('shopcartItems', [['id' => $id, 'sauceId' => 0, 'extraSauceId' => $extraSauce, 'extraFries' => $extraFries, 'quantity' => $quantity]]);
            }
          }
        }
      }
    }
  }
  catch (Exception $e) {
    // Don't do anything
  }

  header('Location: index.php');
?>
