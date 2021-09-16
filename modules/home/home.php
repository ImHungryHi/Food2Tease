<?php

/**
 * Home controller klasse
 * ------------------------------
 */

/**
 * ------------------------------
 * Includes
 * ------------------------------
 */

  // HomeDB
  require_once 'home.db.php';

class HomeController extends PlonkController
{
	// De verschillende views van de module
	protected $views = array(
    'home'
	);

	// De verschillende acties
	protected $actions = array(
    'home'
  );

	/**
	 * De view van de homepagina
	 * @return
	 */
	public function showHome()
  {
    /**
     * Hoofdlayout
     */
  		// ken waardes toe aan hun bijhorende variabelen
  		$this->mainTpl->assign('pageTitle', 'Food2Tease - Bladeren');
  		$this->mainTpl->assign('pageMeta', '<link type="text/css" rel="stylesheet" href="modules/home/css/home.css" />
    <script src="modules/home/js/home.js"></script>');

    /**
     * Paginaspecifieke layout
     */
      // Shopcart filled? Show and parse it with data. Otherwise, hide it.
      $arrShopcart = [];
      $arrExtraSauces = HomeDB::getExtraSauces();
      $this->mainTpl->assign('shopcartUrl', $_SERVER['PHP_SELF'] . '?' . PlonkWebsite::$moduleKey . '=shopcart');

      if (OneArchySession::exists('shopcartItems')) {
        $arrShopcart = OneArchySession::get('shopcartItems');
      }

      if (count($arrShopcart) > 0) {
        $shopcartQuantity = 0;
        $shopcartTotal = 0;

        foreach ($arrShopcart as $shopcartItem) {
          if (isset($shopcartItem['id']) && isset($shopcartItem['sauceId']) && isset($shopcartItem['quantity']) && isset($shopcartItem['extraSauceId']) && isset($shopcartItem['extraFries'])) {
            $productId = intval($shopcartItem['id']);
            $condimentId = intval($shopcartItem['sauceId']);
            $tempQuantity = intval($shopcartItem['quantity']);
            $shopcartQuantity += $tempQuantity;
            $extraCondimentId = intval($shopcartItem['extraSauceId']);
            $extraFries = intval($shopcartItem['extraFries']);
            $extraFriesPrice = FRIES_PRICE;
            $tempPrice = 0;

            if ($extraCondimentId !== 0) {
              $tempPrice = floatval(HomeDB::getProductPrice($productId) + HomeDB::getCondimentPrice($extraCondimentId));
            }
            else {
              $tempPrice = floatval(HomeDB::getProductPrice($productId));
            }

            if ($productId === 26) {
              $extraFriesPrice = CHEESE_PRICE;
            }

            if ($extraFries !== 0) {
              $tempPrice += floatval($extraFriesPrice);
            }

            $shopcartTotal += ($tempPrice * $tempQuantity);
          }
        }

        $this->mainTpl->assignOption('oShopcartVisible');
        $this->mainTpl->assign('shopcartQuantity', $shopcartQuantity);
        $this->mainTpl->assign('shopcartTotal', number_format($shopcartTotal, 2, '.', ' '));
        $this->mainTpl->assign('shopcartQuantitySmall', $shopcartQuantity);
        $this->mainTpl->assign('shopcartTotalSmall', number_format($shopcartTotal, 2, '.', ' '));

        if ($shopcartQuantity < 2) {
          $this->mainTpl->assignOption('oShopcartNonPlural');
        }
      }
      else {
        $this->mainTpl->assignOption('oShopcartHidden');
        $this->mainTpl->assign('shopcartQuantity', '0');
        $this->mainTpl->assign('shopcartTotal', '0');
        $this->mainTpl->assign('shopcartQuantitySmall', '0');
        $this->mainTpl->assign('shopcartTotalSmall', '0');
      }

      // opvullen van de form
      $this->pageTpl->assign('formUrl', $_SERVER['PHP_SELF'] . '?' . PlonkWebsite::$moduleKey . '=home');
      $arrCategories = HomeDB::getFoodCategories();
      $this->pageTpl->setIteration('iCategories');
      $arrProducts = array();

      foreach ($arrCategories as $categoryRow) {
        if (isset($_POST['selCategory']) && $_POST['selCategory'] === $categoryRow['type']) {
          $this->pageTpl->assignIterationOption('oCategorySelected');
        }

        $this->pageTpl->assignIteration('categoryValue', $categoryRow['type']);
        $this->pageTpl->assignIteration('categoryText', $categoryRow['type']);
        $this->pageTpl->refillIteration('iCategories');
      }

      $this->pageTpl->parseIteration('iCategories');

      if (!isset($_POST['selCategory']) || (isset($_POST['selCategory']) && ($_POST['selCategory'] === '' || $_POST['selCategory'] === '-1'))) {
        $arrProducts = HomeDB::getAllProducts();
      }
      else {
        $arrProducts = HomeDB::getProductsByCategory($_POST['selCategory']);
      }

      // Set the values for the other sort fields so we know where to go when clicked again
      $sortAlfaValue = 'noSort';
      $sortPriceValue = 'noSort';

      if (isset($_POST['action']) && $_POST['action'] === 'submit') {
        // action === submit means filtering was done (sauce)
        if (isset($_POST['txtAlfabetSort']) && $_POST['txtAlfabetSort'] === 'highLow') {
          $sortAlfaValue = 'highLow';
        }
        elseif (isset($_POST['txtAlfabetSort']) && $_POST['txtAlfabetSort'] === 'lowHigh') {
          $sortAlfaValue = 'lowHigh';
        }
        else {
          $sortAlfaValue = 'noSort';
        }

        if (isset($_POST['txtPriceSort']) && $_POST['txtPriceSort'] === 'highLow') {
          $sortPriceValue = 'highLow';
        }
        elseif (isset($_POST['txtPriceSort']) && $_POST['txtPriceSort'] === 'lowHigh') {
          $sortPriceValue = 'lowHigh';
        }
        else {
          $sortPriceValue = 'noSort';
        }
      }
      elseif (isset($_POST['action']) && $_POST['action'] === 'sortAlfa') {
        // the alfabetical sort link was clicked - sortPriceValue remains empty
        if (isset($_POST['txtAlfabetSort']) && $_POST['txtAlfabetSort'] === 'highLow') {
          $sortAlfaValue = 'lowHigh';
        }
        elseif (isset($_POST['txtAlfabetSort']) && $_POST['txtAlfabetSort'] === 'lowHigh') {
          $sortAlfaValue = 'highLow';
        }
        else {
          $sortAlfaValue = 'lowHigh';
        }
      }
      elseif (isset($_POST['action']) && $_POST['action'] === 'sortPrice') {
        // the price sort link was clicked - sortAlfaValue remains empty
        if (isset($_POST['txtPriceSort']) && $_POST['txtPriceSort'] === 'highLow') {
          $sortPriceValue = 'lowHigh';
        }
        elseif (isset($_POST['txtPriceSort']) && $_POST['txtPriceSort'] === 'lowHigh') {
          $sortPriceValue = 'highLow';
        }
        else {
          $sortPriceValue = 'lowHigh';
        }
      }

      $tempAlfa = array_column($arrProducts, 'name');
      $tempPrice = array_column($arrProducts, 'price');

      switch ($sortAlfaValue) {
        case 'highLow':
          $this->pageTpl->assign('alfaSortText', 'Hoog naar laag');
          $this->pageTpl->assign('alfaSortClass', 'sort-up');
          array_multisort($tempAlfa, SORT_DESC, $arrProducts);
          break;
        case 'lowHigh':
          array_multisort($tempAlfa, SORT_ASC, $arrProducts);
          $this->pageTpl->assign('alfaSortText', 'Laag naar hoog');
          $this->pageTpl->assign('alfaSortClass', 'sort-down');
          break;
        default:
          $this->pageTpl->assign('alfaSortText', 'Laag naar hoog');
          $this->pageTpl->assign('alfaSortClass', 'sort');
      }

      switch ($sortPriceValue) {
        case 'highLow':
          $this->pageTpl->assign('priceSortText', 'Hoog naar laag');
          $this->pageTpl->assign('priceSortClass', 'sort-up');
          array_multisort($tempPrice, SORT_DESC, $arrProducts);
          break;
        case 'lowHigh':
          array_multisort($tempPrice, SORT_ASC, $arrProducts);
          $this->pageTpl->assign('priceSortText', 'Laag naar hoog');
          $this->pageTpl->assign('priceSortClass', 'sort-down');
          break;
        default:
          $this->pageTpl->assign('priceSortText', 'Laag naar hoog');
          $this->pageTpl->assign('priceSortClass', 'sort');
      }

      if ($sortPriceValue === 'noSort' && $sortAlfaValue === 'noSort') {
        array_multisort(array_column($arrProducts, 'type'), SORT_DESC, $tempAlfa, SORT_ASC, $arrProducts);
      }

      $this->pageTpl->assign('txtAlfabetSort', $sortAlfaValue);
      $this->pageTpl->assign('txtPriceSort', $sortPriceValue);
      $this->pageTpl->setIteration('iItems');

      for ($ix = 0; $ix < count($arrProducts); $ix++) {
      //foreach ($arrProducts as $product) {
        $imgUrl = 'core/img/thumb_placeholder_4x3.jpg';
        $arrSauces = HomeDB::getCondimentsForProduct(intval($arrProducts[$ix]['id']));

        if (stripos($arrProducts[$ix]['name'], 'aghetti') !== false) {
          $this->pageTpl->assignIteration('extraFriesText', 'Extra kaas?');
        }
        else {
          $this->pageTpl->assignIteration('extraFriesText', 'Extra friet?');
        }

        if ($arrSauces !== []) {
          $this->pageTpl->assignIterationOption('oHasSauces');
          $this->pageTpl->setIteration('iSauces', 'iItems');

          foreach ($arrSauces as $sauce) {
            $this->pageTpl->assignIteration('sauceValue', $sauce['id']);
            $this->pageTpl->assignIteration('sauceText', $sauce['name']);
            $this->pageTpl->refillIteration('iSauces');
          }

          $this->pageTpl->parseIteration('iSauces');
        }

        if($arrExtraSauces !== [] && stripos($arrProducts[$ix]['name'], 'aghetti') === false) {
          $this->pageTpl->assignIterationOption('oHasExtraSauces');
          $this->pageTpl->setIteration('iExtraSauces', 'iItems');

          foreach ($arrExtraSauces as $sauce) {
            $this->pageTpl->assignIteration('extraSauceValue', $sauce['id']);
            $this->pageTpl->assignIteration('extraSauceText', $sauce['name'] . ' - &euro; ' . number_format($sauce['price'], 2, '.', ' '));
            $this->pageTpl->refillIteration('iExtraSauces');
          }

          $this->pageTpl->parseIteration('iExtraSauces');
        }

        if (file_exists('core/img/thumb_' . $arrProducts[$ix]['id'] . '_4x3.jpg')) {
          $imgUrl = 'core/img/thumb_' . $arrProducts[$ix]['id'] . '_4x3.jpg';
        }

        $this->pageTpl->assignIteration('itemTitle', $arrProducts[$ix]['name']);
        $this->pageTpl->assignIteration('imgAlt', $arrProducts[$ix]['name']);
        $this->pageTpl->assignIteration('imgUrl', $imgUrl);
        $this->pageTpl->assignIteration('price', number_format($arrProducts[$ix]['price'], 2, '.', ' '));
        $this->pageTpl->assignIteration('itemId', $arrProducts[$ix]['id']);

        if ($arrProducts[$ix]['description'] !== NULL) {
          $this->pageTpl->assignIterationOption('oHasDescription');
          $this->pageTpl->assignIteration('itemDescription', $arrProducts[$ix]['description']);
        }

        if (isset($_POST['txtQuantity_' . $arrProducts[$ix]['id']])) {
          $this->pageTpl->assignIteration('quantity', '1');
        }
        else {
          $this->pageTpl->assignIteration('quantity', '1');
        }

        if ($ix%3 !== 0) {
          $this->pageTpl->assignIterationOption('oOffsetColumn');
        }
        else {
          $this->pageTpl->assignIterationOption('oFirstColumn');
        }

        // Finally, set the iteration up for a next - you guessed it - iteration
        $this->pageTpl->refillIteration('iItems');
      }

      $this->pageTpl->parseIteration('iItems');
	}

	/**
	 * Doe acties op basis van de POST array
	 * @return
	 */
	public function doHome()
  {
    $this->view = 'home';
	}
}
?>
