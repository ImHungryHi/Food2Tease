<?php
/**
 * Shopcart controller klasse
 * ------------------------------
 */

/**
 * ------------------------------
 * Includes
 * ------------------------------
 */

  // ShopcartDB
  require_once 'shopcart.db.php';

class ShopcartController extends PlonkController
{
	// De verschillende views van de module
	protected $views = array(
    'shopcart'
	);

	// De verschillende acties
	protected $actions = array(
    'shopcart'
  );

	/**
	 * De view van de shopcart pagina
	 * @return
	 */
	public function showShopcart()
  {
    /**
     * Hoofdlayout
     */
  		// ken waardes toe aan hun bijhorende variabelen
  		$this->mainTpl->assign('pageTitle', 'Food2Tease - Winkelwagen');
  		$this->mainTpl->assign('pageMeta',
      '<link rel="stylesheet" href="modules/shopcart/css/shopcart.css" />
    <script src="modules/shopcart/js/shopcart.js"></script>');
      $this->pageTpl->assign('formUrl', $_SERVER['PHP_SELF'] . '?' . PlonkWebsite::$moduleKey . '=shopcart');
      $this->mainTpl->assign('shopcartUrl', $_SERVER['PHP_SELF'] . '?' . PlonkWebsite::$moduleKey . '=shopcart');
      $arrExtraSauces = ShopcartDB::getExtraSauces();

      // Check the POST array for actions, we have either update and return to home, delete a certain item OR checkout
      if (isset($_POST['action'])) {
        if ($_POST['action'] === 'updateAndBack') {
          // Update the shopping cart and go back to the homepage
          $this->updateShopcartInSession();
          PlonkWebsite::redirect($_SERVER['PHP_SELF'] . '?' . PlonkWebsite::$moduleKey . '=home');
        }
        elseif ($_POST['action'] === 'checkout') {
          // Update the shopping cart, send the order and go to the checkout page
          $this->updateShopcartInSession();
          PlonkWebsite::redirect($_SERVER['PHP_SELF'] . '?' . PlonkWebsite::$moduleKey . '=checkout');
        }
        elseif (stripos($_POST['action'], 'btnDeleteFor_') !== false) {
          $this->updateShopcartInSession();
        }
      }

      // Check if there is a comment in the session and update the comment field if there is
      if (OneArchySession::exists('shopcartComment')) {
        $this->pageTpl->assign('txtCommentContent', (string) OneArchySession::get('shopcartComment'));
      }
      else {
        $this->pageTpl->assign('txtCommentContent', '');
      }

      // Iteratie van winkelwagen artikelen
      if (!OneArchySession::exists('shopcartItems')) {
        $this->pageTpl->assignOption('oHasNoItems');
        $this->mainTpl->assignOption('oShopcartHidden');
        $this->mainTpl->assign('shopcartQuantity', '0');
        $this->mainTpl->assign('shopcartTotal', '0');
        $this->mainTpl->assign('shopcartQuantitySmall', '0');
        $this->mainTpl->assign('shopcartTotalSmall', '0');
      }
      else {
        // Get all items from the session, we'll throw these into an associative array an loop through to display
        $arrArticles = OneArchySession::get('shopcartItems');

        // We're expecting an array, if anything else is in the session, we're going to have to do some shopping first
        if (gettype($arrArticles) === 'array' && count($arrArticles) > 0) {
          $arrArticleIds = array_column($arrArticles, 'id');
          array_multisort($arrArticleIds, SORT_ASC, $arrArticles);
          $shopcartQuantity = 0;
          $total = 0;
          $arrArticleInfos = array();

          $arrArticleInfos = ShopcartDB::getProductInfoByIds($arrArticleIds);
          $arrArticleSauces = ShopcartDB::getProductSauces($arrArticleIds);

          if ($arrArticleInfos === NULL || gettype($arrArticleInfos) !== 'array' || $arrArticleInfos === []) {
            $this->pageTpl->assignOption('oHasNoItems');
            $this->mainTpl->assignOption('oShopcartHidden');
            $this->mainTpl->assign('shopcartQuantity', '0');
            $this->mainTpl->assign('shopcartTotal', '0');
            $this->mainTpl->assign('shopcartQuantitySmall', '0');
            $this->mainTpl->assign('shopcartTotalSmall', '0');
          }
          else {
            for ($x = 0; $x < count($arrArticles); $x++) {
              foreach ($arrArticleInfos as $articleInfos) {
                if ($arrArticles[$x]['id'] === $articleInfos['id']) {
                  $arrArticles[$x]['info'] = $articleInfos;
                }
              }

              foreach ($arrArticleSauces as $articleSauce) {
                if ($arrArticles[$x]['id'] === $articleSauce['productId']) {
                  $arrArticles[$x]['sauces'][] = $articleSauce;
                }
              }
            }

            $this->pageTpl->assignOption('oHasItems');
            $this->pageTpl->setIteration('iItems');

            for ($x = 0; $x < count($arrArticles); $x++) {
              $subTotal = 0;
              $itemPrice = 0;
              $extraFriesText = 'Extra friet';
              $extraFriesPrice = FRIES_PRICE;

              if (stripos($arrArticles[$x]['info']['name'], 'aghetti') !== false) {
                $extraFriesText = 'Extra kaas';
                $extraFriesPrice = CHEESE_PRICE;
              }

              $this->pageTpl->assignIteration('extraFriesText', $extraFriesText);
              $this->pageTpl->assignIteration('extraFriesPrice', number_format($extraFriesPrice, 2, '.', ' '));

              if (intval($arrArticles[$x]['quantity']) > 0) {
                $friesPrice = 0;
                $condimentPrice = 0;

                if ($arrArticles[$x]['extraFries'] > 0) {
                  $friesPrice = $extraFriesPrice;
                }

                if ($arrArticles[$x]['extraSauceId'] === 0) {
                  $subTotal = (((float) $arrArticles[$x]['info']['price'] + $friesPrice) * (float) $arrArticles[$x]['quantity']);
                }
                else {
                  if ($arrExtraSauces !== NULL) {
                    foreach ($arrExtraSauces as $sauceInfo) {
                      if ($sauceInfo['id'] === $arrArticles[$x]['extraSauceId']) {
                        $condimentPrice = floatval($sauceInfo['price']);
                      }
                    }
                  }

                  $itemPrice = ((float) $arrArticles[$x]['info']['price'] + $friesPrice + $condimentPrice);
                  $subTotal = ($itemPrice * (float) $arrArticles[$x]['quantity']);
                }

                if ($arrArticles[$x]['sauceId'] !== 0) {
                  $selectedSauceName = '';

                  foreach($arrArticles[$x]['sauces'] as $sauceInfo) {
                    if ($sauceInfo['condimentId'] === $arrArticles[$x]['sauceId']) {
                      $selectedSauceName = $sauceInfo['condimentName'];
                    }
                  }

                  $this->pageTpl->assignIterationOption('oHasSauce');
                  $this->pageTpl->assignIteration('artSauce', $selectedSauceName);
                }

                if ($arrArticles[$x]['info']['description'] !== NULL) {
                  $this->pageTpl->assignIterationOption('oHasDescription');
                  $this->pageTpl->assignIteration('artDescription', $arrArticles[$x]['info']['description']);
                }

                if (isset($arrArticles[$x]['sauces']) && count($arrArticles[$x]['sauces']) > 1) {
                  $this->pageTpl->assignIterationOption('oHasSauces');
                  $this->pageTpl->setIteration('iSauces', 'iItems');

                  for ($y = 0; $y < count($arrArticles[$x]['sauces']); $y++) {
                    $this->pageTpl->assignIteration('sauceValue', $arrArticles[$x]['sauces'][$y]['condimentId']);
                    $this->pageTpl->assignIteration('sauceText', $arrArticles[$x]['sauces'][$y]['condimentName']);

                    if ($arrArticles[$x]['sauces'][$y]['condimentId'] === $arrArticles[$x]['sauceId']) {
                      $this->pageTpl->assignIterationOption('oSauceSelected');
                    }

                    $this->pageTpl->refillIteration('iSauces');
                  }

                  $this->pageTpl->parseIteration('iSauces');
                }

                if ($arrExtraSauces !== [] && stripos($arrArticles[$x]['info']['name'], 'aghetti') === false) {
                  if (!isset($arrArticles[$x]['extraSauceId'])) {
                    $this->pageTpl->assignIterationOption('oNoExtraSauceSelected');
                  }
                  elseif (intval($arrArticles[$x]['extraSauceId']) < 1) {
                    $this->pageTpl->assignIterationOption('oNoExtraSauceSelected');
                  }

                  $this->pageTpl->assignIterationOption('oHasExtraSauces');
                  $this->pageTpl->setIteration('iExtraSauces', 'iItems');

                  for ($y = 0; $y < count($arrExtraSauces); $y++) {
                    $this->pageTpl->assignIteration('extraSauceValue', $arrExtraSauces[$y]['id']);
                    $this->pageTpl->assignIteration('extraSauceText', $arrExtraSauces[$y]['name']);
                    $this->pageTpl->assignIteration('extraCondimentPrice', $arrExtraSauces[$y]['price']);

                    if ($arrExtraSauces[$y]['id'] === $arrArticles[$x]['extraSauceId']) {
                      $this->pageTpl->assignIterationOption('oExtraSauceSelected');
                    }

                    $this->pageTpl->refillIteration('iExtraSauces');
                  }

                  $this->pageTpl->parseIteration('iExtraSauces');
                }

                if (intval($arrArticles[$x]['extraFries']) > 0) {
                  $this->pageTpl->assignIteration('extraFriesValue', '1');
                  $this->pageTpl->assignIterationOption('oExtraFriesChecked');
                }
                else {
                  $this->pageTpl->assignIteration('extraFriesValue', '0');
                }

                $itemId = $arrArticles[$x]['id'] . 'x' . $arrArticles[$x]['sauceId'] . 'x' . $arrArticles[$x]['extraSauceId'] . 'x' . $arrArticles[$x]['extraFries'];
                $this->pageTpl->assignIteration('itemId', $itemId);
                $this->pageTpl->assignIteration('artTitle', $arrArticles[$x]['info']['name']);
                $this->pageTpl->assignIteration('artPrice', $arrArticles[$x]['info']['price']);
                $this->pageTpl->assignIteration('itemPrice', $itemPrice);
                $this->pageTpl->assignIteration('artQuantity', $arrArticles[$x]['quantity']);
                $this->pageTpl->assignIteration('artTotal', number_format($subTotal, 2, '.', ' '));
                $shopcartQuantity += intval($arrArticles[$x]['quantity']);
                $total += $subTotal;
                $this->pageTpl->refillIteration('iItems');
              }
            }

            $this->pageTpl->parseIteration();
            $this->pageTpl->assign('shopcartTotal', number_format($total, 2, '.', ' '));
            $this->mainTpl->assignOption('oShopcartVisible');
            $this->mainTpl->assign('shopcartQuantity', $shopcartQuantity);
            $this->mainTpl->assign('shopcartTotal', number_format($total, 2, '.', ' '));
            $this->mainTpl->assign('shopcartQuantitySmall', $shopcartQuantity);
            $this->mainTpl->assign('shopcartTotalSmall', number_format($total, 2, '.', ' '));
          }
        }
        else {
          $this->pageTpl->assignOption('oHasNoItems');
          $this->mainTpl->assignOption('oShopcartHidden');
          $this->mainTpl->assign('shopcartQuantity', '0');
          $this->mainTpl->assign('shopcartTotal', '0');
          $this->mainTpl->assign('shopcartQuantitySmall', '0');
          $this->mainTpl->assign('shopcartTotalSmall', '0');
        }
      }
	}

  /**
   * Update via POST
   * @return
   */
  public function doShopcart() {
    // Do some stuff to put all updates into the session/cookie
    $this->view = 'shopcart';
  }

  /**
   * Update all POST info into our shopcart
   * @return
   */
  private function updateShopcartInSession() {
    $arrArticles = array();
    $arrAllIds = array();
    $deleteForId = '';

    // Get the id for which product-sauce id pair needs to be deleted. If not set, these will both remain -1 as this value is never used otherwise
    if (isset($_POST['action']) && stripos($_POST['action'], 'btnDeleteFor_') !== false) {
      $deleteForId = explode('_', $_POST['action'])[1];
    }

    // Parse the POST array into something that resembles the session array a little more
    foreach ($_POST as $k => $v) {
      $arrSplit = stripos($k, '_') !== false ? explode('_', $k) : $k;

      if ($arrSplit !== $k && count($arrSplit) > 1) {
        if ($deleteForId !== $arrSplit[1] && !in_array($arrSplit[1], $arrAllIds) && substr_count($arrSplit[1], 'x') === 3) {
          $arrAllIds[] = $arrSplit[1];
        }
      }
    }

    foreach ($arrAllIds as $idKey) {
      $intId = intval(explode('x', $idKey)[0]);
      $intSauceId = 0;
      $intExtraSauceId = 0;
      $intExtraFries = 0;
      $intQuantity = 0;
      $blnFoundIt = false;

      if (isset($_POST['selSauceFor_' . $idKey]) && $_POST['selSauceFor_' . $idKey] !== '') {
        $intSauceId = intval($_POST['selSauceFor_' . $idKey]);
      }

      if (isset($_POST['selExtraSauceFor_' . $idKey]) && $_POST['selExtraSauceFor_' . $idKey] !== '') {
        $intExtraSauceId = intval($_POST['selExtraSauceFor_' . $idKey]);
      }

      if (isset($_POST['chkExtraFriesFor_' . $idKey]) && $_POST['chkExtraFriesFor_' . $idKey] !== '') {
        $intExtraFries = intval($_POST['chkExtraFriesFor_' . $idKey]);
      }

      if (isset($_POST['selQuantityFor_' . $idKey]) && $_POST['selQuantityFor_' . $idKey] !== '') {
        $intQuantity = intval($_POST['selQuantityFor_' . $idKey]);
      }

      if (count($arrArticles) === 0 && $intQuantity > 0) {
        $arrArticles[] = ['id' => $intId, 'sauceId' => $intSauceId, 'extraSauceId' => $intExtraSauceId, 'extraFries' => $intExtraFries, 'quantity' => $intQuantity];
      }
      elseif ($intQuantity > 0) {
        for ($x = 0; $x < count($arrArticles); $x++) {
          if ($arrArticles[$x]['id'] === $intId && $arrArticles[$x]['sauceId'] === $intSauceId && $arrArticles[$x]['extraSauceId'] === $intExtraSauceId && $arrArticles[$x]['extraFries'] === $intExtraFries) {
            $arrArticles[$x]['quantity'] += $intQuantity;
            $blnFoundIt = true;
            break;
          }
        }

        if (!$blnFoundIt) {
          $arrArticles[] = ['id' => $intId, 'sauceId' => $intSauceId, 'extraSauceId' => $intExtraSauceId, 'extraFries' => $intExtraFries, 'quantity' => $intQuantity];
        }
      }
    }

    // Pass all articles through to the session in their altered states
    OneArchySession::set('shopcartItems', $arrArticles);

    if (isset($_POST['txtComment'])) {
      OneArchySession::set('shopcartComment', (string) $_POST['txtComment']);
    }
  }
}
?>
