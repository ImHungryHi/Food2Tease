<?php
/**
 * ------------------------------
 * Includes
 * ------------------------------
 */

  // project specific includes
  require_once '../config.php';

  // Plonk & PlonkWebsite
  require_once './library/plonk/plonk.php';
  require_once './library/plonk/website/website.php';

  // OneArchy
  require_once './library/OneArchy/Database.php';
  require_once './library/OneArchy/Session.php';

/**
 * ------------------------------
 * Maak of kraak de website
 * ------------------------------
 */

  try {
    // Start de sessie, we gaan deze overal nodig hebben
    OneArchySession::start();

    // Maak een website aan met zijn modules
    $website = new PlonkWebsite(
      array('home', 'shopcart', 'checkout', 'thanks', 'error')
    );
  } catch (Exception $e) {
    if (defined('DEBUG') && (DEBUG === true))
    {
      OneArchySession::set('errorMessage', $e->getMessage());
      PlonkWebsite::redirect($_SERVER['PHP_SELF'] . '?' . PlonkWebsite::$moduleKey . '=error');
    } else {
      OneArchySession::set('errorMessage', 'Er is een fout opgelopen bij het verwerken van uw verzoek - Probeer later opnieuw.');
      PlonkWebsite::redirect($_SERVER['PHP_SELF'] . '?' . PlonkWebsite::$moduleKey . '=error');
    }
  }
?>
