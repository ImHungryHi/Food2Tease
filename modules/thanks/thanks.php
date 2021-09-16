<?php
/**
 * Thanks controller klasse
 * ------------------------------
 */

/**
 * ------------------------------
 * Includes
 * ------------------------------
 */

  // ThanksDB
  require_once 'thanks.db.php';

class ThanksController extends PlonkController
{
	// De verschillende views van de module
	protected $views = array(
    'thanks'
	);

	// De verschillende acties
	protected $actions = array();

	/**
	 * De view van de thanks pagina
	 * @return
	 */
	public function showThanks()
  {
    /**
     * Hoofdlayout
     */
  		// ken waardes toe aan hun bijhorende variabelen
  		$this->mainTpl->assign('pageTitle', 'Food2Tease - Afrekening');
  		$this->mainTpl->assign('pageMeta',
      '<link type="text/css" rel="stylesheet" href="modules/thanks/css/thanks.css" />');
      $this->pageTpl->assign('browseLink', $_SERVER['PHP_SELF'] . '?' . PlonkWebsite::$moduleKey . '=home');
      $this->mainTpl->assign('shopcartUrl', $_SERVER['PHP_SELF'] . '?' . PlonkWebsite::$moduleKey . '=shopcart');
      $this->mainTpl->assignOption('oShopcartHidden');
      $this->mainTpl->assign('shopcartQuantity', '0');
      $this->mainTpl->assign('shopcartTotal', '0');
      $this->mainTpl->assign('shopcartQuantitySmall', '0');
      $this->mainTpl->assign('shopcartTotalSmall', '0');

      // Iteratie van winkelwagen artikelen
      if (!OneArchySession::exists('shopcartItems')) {
        PlonkWebsite::redirect($_SERVER['PHP_SELF'] . '?' . PlonkWebsite::$moduleKey . '=home');
      }
      else {
        // Array nog toevoegen van op te zoeken artikelen
        $arrArticles = OneArchySession::get('shopcartItems');
        $arrArticleIds = array_column($arrArticles, 'id');
        $arrExtraSauces = ThanksDB::getExtraSauces();
        array_multisort($arrArticleIds, SORT_ASC, $arrArticles);
        $total = 0;
        $this->pageTpl->setIteration('iItems');

        for ($x = 0; $x < count($arrArticles); $x++) {
          $arrArticles[$x]['sauces'] = ThanksDB::getSauceInfoByProductId($arrArticles[$x]['id']);
          $arrArticles[$x]['info'] = ThanksDB::getProductInfoById($arrArticles[$x]['id']);
          $extraFriesPrice = FRIES_PRICE;
          $extraFriesText = 'friet';
          $subTotal = 0;
          $extrasContent = '';
          $sauceName = '';
          $extras = 0;

          if (stripos($arrArticles[$x]['info']['name'], 'aghetti') !== false) {
            $extraFriesPrice = CHEESE_PRICE;
            $extraFriesText = 'kaas';
          }

          if ($arrArticles[$x]['extraSauceId'] !== 0) {
            foreach ($arrExtraSauces as $extraSauce) {
              if ($extraSauce['id'] === $arrArticles[$x]['extraSauceId']) {
                $extrasContent = $extraSauce['name'];
                $extras += $extraSauce['price'];
                $this->pageTpl->assignIterationOption('oExtraCondimentPrice');
                $this->pageTpl->assignIteration('extraCondimentPrice', number_format($extraSauce['price'], 2, '.', ' '));
              }
            }

            if ($arrArticles[$x]['extraFries'] > 0) {
              $extrasContent .= ' en ' . $extraFriesText;
              $extras += $extraFriesPrice;
              $this->pageTpl->assignIterationOption('oFriesPrice');
              $this->pageTpl->assignIteration('friesPrice', number_format($extraFriesPrice, 2, '.', ' '));
            }
          }
          else {
            if ($arrArticles[$x]['extraFries'] > 0) {
              $extrasContent .= $extraFriesText;
              $extras += $extraFriesPrice;
              $this->pageTpl->assignIterationOption('oFriesPrice');
              $this->pageTpl->assignIteration('friesPrice', number_format($extraFriesPrice, 2, '.', ' '));
            }
          }

          $subTotal = (((float) $arrArticles[$x]['info']['price'] + $extras) * (float) $arrArticles[$x]['quantity']);

          if ($arrArticles[$x]['sauceId'] !== 0) {
            foreach ($arrArticles[$x]['sauces'] as $artSauce) {
              if ($artSauce['id'] === $arrArticles[$x]['sauceId']) {
                $sauceName = $artSauce['name'];
              }
            }

            $this->pageTpl->assignIterationOption('oHasSauce');
            $this->pageTpl->assignIteration('artSauce', $sauceName);
          }

          if ($extrasContent !== '') {
            $this->pageTpl->assignIterationOption('oHasExtras');
            $this->pageTpl->assignIteration('extraContent', $extrasContent);
          }

          $this->pageTpl->assignIteration('artTitle', $arrArticles[$x]['info']['name']);
          $this->pageTpl->assignIteration('artPrice', $arrArticles[$x]['info']['price']);
          $this->pageTpl->assignIteration('artQuantity', $arrArticles[$x]['quantity']);
          $this->pageTpl->assignIteration('artTotal', number_format($subTotal, 2, '.', ' '));
          $total += $subTotal;
          $this->pageTpl->refillIteration();
        }

        $this->pageTpl->parseIteration();
        $this->pageTpl->assign('checkoutTotal', number_format((float) $total, 2, '.', ''));

        // Destroy the session and its data, we'll not be needing it anymore
        OneArchySession::destroy();
      }
	}
}
?>
