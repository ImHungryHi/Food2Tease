function updateItems() {
  document.getElementById('btnBrowse').click();
}

function chkMultiChecked (chkBox) {
  var hideables = document.getElementsByClassName('hideable');
  var txtClassName = 'hideable';

  if (chkBox.checked) {
    txtClassName = 'field hideable';
    //for (x = 0; x < hideables.length; x++) {
      //hideables[x].className = 'hideable';
    //}
  }
  else {
    txtClassName = 'field hideable hidden';
    //for (x = 0; x < hideables.length; x++) {
      //hideables[x].className = 'hideable hidden';
    //}
  }

  Array.from(hideables).forEach(elmnt => elmnt.className = txtClassName);
}
