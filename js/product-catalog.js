(function ($, Drupal) {

  // Mark fav products on page load
  Drupal.behaviors.markFavProducts = {
    attach: function (context, settings) {
      once('markFavProducts', 'html', context).forEach(markFavProducts)
    }
  }

  // Add a fav product when the user clicks the icon.
  Drupal.behaviors.addFavProduct = {
    attach: function (context, settings) {
      once('addFavProduct', '.product-catalog--favorite-icon', context)
        .forEach((item) => item.onclick = toggleProductFav);
    }
  }

  /**
   * Callback function to get all the user fav products
   * and mark them in the Doc.
   */
  function markFavProducts() {
    $.ajax({
      url: "/product-catalog/products/user-favs",
      method: "GET",
    }).then(function(data, status, xhr) {
      const resData = JSON.parse(data);
      resData.nids.forEach(function (nid) {
        const favIcon = $(`[data-product-id=${nid}] i`);
        favIcon.addClass('active');
      })
    })
  }

  /**
   * Callback function to toggle a user fav product when clicked.
   */
  function toggleProductFav() {
    const nid = $(this).attr('data-product-id');
    const op = $(this).find('i').hasClass('active') ? 'remove' : 'add';
    $.ajax({
        url: "/product-catalog/products/user-fav",
        method: "POST",
        data: {'nid':nid, 'op':op},
    }).then(function(data, status, xhr) {
      const resData = JSON.parse(data);
      const favIcon = $(`[data-product-id=${resData.nid}] i`);
      if(resData.result === 'added') {
        favIcon.addClass('active');
      }
      else {
        favIcon.removeClass('active');
      }
    })
  }
})(jQuery, Drupal);

