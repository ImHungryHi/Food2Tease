function sortAlfa() {
  document.getElementById('btnSortAlfa').click();
}

function sortPrice() {
  document.getElementById('btnSortPrice').click();
}

function submitForm() {
  document.getElementById('btnSubmit').click();
}

function sendItem(productId) {
  productId = parseInt(productId);
  let sauceId = 0;
  let extraSauceId = 0;
  let extraFries = 0;
  let extraFriesPriceDefault = 2.5;

  if (document.getElementById('chkExtraFriesFor_' + productId).value === 'Extra kaas?') {
    extraFriesPriceDefault = 1;
    console.log('Extra cheese');
  }

  if (document.getElementById('selSauce_' + productId) !== null) {
    sauceId = parseInt(document.getElementById('selSauce_' + productId).value);
  }

  if (document.getElementById('selExtraSauce_' + productId) !== null) {
    extraSauceId = parseInt(document.getElementById('selExtraSauce_' + productId).value);
  }

  if (document.getElementById('chkExtraFriesFor_' + productId) !== null) {
    if (document.getElementById('chkExtraFriesFor_' + productId).checked) {
      extraFries = 1;
    }
    else {
      extraFries = 0;
    }
  }

  let quantity = parseInt(document.getElementById('txtQuantity_' + productId).value);
  event.preventDefault();
  fetch('addToCart.php?id=' + productId + '&sauce=' + sauceId + '&extraSauce=' + extraSauceId + '&extraFries=' + extraFries + '&quantity=' + quantity);

  // Update the shopping cart on-site as well
  let shopcartQuantityField = document.getElementById('shopcartQuantity');
  let shopcartQuantitySmallField = document.getElementById('shopcartQuantitySmall');
  let shopcartQuantity = parseInt(shopcartQuantityField.innerText);
  shopcartQuantityField.innerText = shopcartQuantity + quantity;
  shopcartQuantitySmallField.innerText = shopcartQuantityField.innerText;
  let shopcartTotalField = document.getElementById('shopcartTotal');
  let shopcartTotalSmallField = document.getElementById('shopcartTotalSmall');
  let shopcartTotal = parseFloat(shopcartTotalField.innerText);
  let priceField = document.getElementById('priceSpan_' + productId);
  let price = parseFloat(priceField.innerText.split('€ ')[1]);
  let condimentField = document.getElementById('selExtraSauce_' + productId);
  let extraFriesPrice = extraFries * extraFriesPriceDefault;

  if (condimentField !== null && parseInt(condimentField.value) !== 0) {
    let condimentPrice = parseFloat(condimentField[condimentField.selectedIndex].innerText.split('€ ')[1]);
    shopcartTotalField.innerText = (shopcartTotal + ((price + extraFriesPrice + condimentPrice) * quantity)).toFixed(2);
  }
  else {
    shopcartTotalField.innerText = (shopcartTotal + ((price + extraFriesPrice) * quantity)).toFixed(2);
  }

  shopcartTotalSmallField.innerText = shopcartTotalField.innerText;

  if ((shopcartQuantity + quantity) > 1) {
    document.getElementById('shopcartPlural').innerText = 'en';
  }
  else {
    document.getElementById('shopcartPlural').innerText = '';
  }

  if (document.getElementById('shopcartLayout').className = 'hidden') {
    document.getElementById('shopcartLayout').className = 'columns is-12 is-clearfix';
  }

  if (document.getElementById('shopcartQuantitySmallDiv').className = 'hidden') {
    document.getElementById('shopcartQuantitySmallDiv').className = 'is-pulled-left';
  }

  if (document.getElementById('shopcartTotalSmallDiv').className = 'hidden') {
    document.getElementById('shopcartTotalSmallDiv').className = 'is-pulled-right';
  }
}
