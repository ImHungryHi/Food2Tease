function updateItems() {
  document.getElementById('btnUpdate').click();
}

function deleteArticle(id) {
  document.getElementById('btnDeleteFor_' + id).click();
}

function updateQuantity(id) {
  let condimentSelectorField = document.getElementById('selExtraSauceFor_' + id);
  let extraFriesCheckbox = document.getElementById('chkExtraFriesFor_' + id);
  let itemPrice = parseFloat(document.getElementById('itemPrice_' + id).innerText.replace('€ ', ''));
  let extraPrice = parseFloat(document.getElementById('extraPrice_' + id).innerText);
  let condimentPrice = 0;
  let extraFriesPrice = 0;

  if (extraFriesCheckbox !== null) {
    extraFriesPrice = extraPrice * parseInt(extraFriesCheckbox.value);
  }

  if (condimentSelectorField !== null) {
    if (condimentSelectorField.selectedIndex > 0) {
      condimentPrice = parseFloat(condimentSelectorField[condimentSelectorField.selectedIndex].innerText.split('€ ')[1]);
    }
  }

  document.getElementById('itemTotal_' + id).innerText = '€ ' + ((itemPrice + condimentPrice + extraFriesPrice) * document.getElementById('selQuantityFor_' + id).value).toFixed(2);
  sumAllPrices();
}

function updateSauceType(id) {
  let condimentSelectorField = document.getElementById('selExtraSauceFor_' + id);
  let extraFriesCheckbox = document.getElementById('chkExtraFriesFor_' + id);
  let itemPrice = parseFloat(document.getElementById('itemPrice_' + id).innerText.replace('€ ', ''));
  let extraPrice = parseFloat(document.getElementById('extraPrice_' + id).innerText);
  let condimentPrice = 0;
  let extraFriesPrice = 0;

  if (extraFriesCheckbox !== null) {
    extraFriesPrice = extraPrice * parseInt(extraFriesCheckbox.value);
  }

  if (condimentSelectorField !== null) {
    if (condimentSelectorField.selectedIndex > 0) {
      condimentPrice = parseFloat(condimentSelectorField[condimentSelectorField.selectedIndex].innerText.split('€ ')[1]);
    }
  }

  itemPrice += condimentPrice + extraFriesPrice;
  document.getElementById('itemTotal_' + id).innerText = '€ ' + (itemPrice * document.getElementById('selQuantityFor_' + id).value).toFixed(2);
  sumAllPrices();
}

function updateFries(id) {
  let extraFriesCheckbox = document.getElementById('chkExtraFriesFor_' + id);
  let extraCondimentField = document.getElementById('selExtraSauceFor_' + id);
  let itemPrice = parseFloat(document.getElementById('itemPrice_' + id).innerText.replace('€ ', ''));
  let intQuantity = parseInt(document.getElementById('selQuantityFor_' + id).value);
  let extraPrice = parseFloat(document.getElementById('extraPrice_' + id).innerText);
  let extraFriesChecked = extraFriesCheckbox.value;
  let extraFriesPrice = 0;
  let extraCondimentPrice = 0;

  if (extraCondimentField !== null) {
    if (extraCondimentField.selectedIndex > 0) {
      extraCondimentPrice = parseFloat(extraCondimentField[extraCondimentField.selectedIndex].innerText.split('€ ')[1]);
    }
  }

  if (parseInt(extraFriesChecked) === 0) {
    extraFriesCheckbox.value = 1;
    extraFriesPrice = extraPrice;
  }
  else {
    extraFriesCheckbox.value = 0;
  }

  document.getElementById('itemTotal_' + id).innerText = '€ ' + (intQuantity * (extraFriesPrice + extraCondimentPrice + itemPrice)).toFixed(2);
  sumAllPrices();
}

function sumAllPrices() {
  let totalFields = document.querySelectorAll('.itemTotal');
  let quantityFields = document.querySelectorAll('.itemQuantity');
  let grandTotal = 0;
  let totalNumber = 0;

  for (x = 0; x < totalFields.length; x++) {
    let subTotalPrice = parseFloat(totalFields[x].innerText.replace('€ ', ''));
    grandTotal += subTotalPrice;
  }

  for (x = 0; x < quantityFields.length; x++) {
    totalNumber += parseInt(quantityFields[x].value);
  }

  document.getElementById('shopcartQuantity').innerText = totalNumber;
  document.getElementById('grandTotal').innerText = '€ ' + grandTotal.toFixed(2);
  document.getElementById('shopcartTotal').innerText = grandTotal.toFixed(2);
}
