let btnScrollTop = document.getElementById('btnBackToTop');

$(document).ready(function () {
    $('[data-toggle="tooltip"]').tooltip();

    btnScrollTop = document.getElementById('btnBackToTop');
});

window.onscroll = function() { scrollFunction() };

function scrollFunction() {
  if (btnScrollTop !== null) {
    if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
      btnScrollTop.style.display = 'block';
    }
    else {
      btnScrollTop.style.display = 'none';
    }
  }
}

function backToTop() {
  document.body.scrollTop = 0;
  document.documentElement.scrollTop = 0;
}
