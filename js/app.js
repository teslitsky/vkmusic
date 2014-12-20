(function ($) {
  $(function () {
    $('#download').click(function (e) {
      e.preventDefault();
      var link = $('#link');
      var input = link.val();
      if (input.length) {
        link.parent().removeClass('has-error');
        window.location.href = '/get.php?url=' + encodeURIComponent(input);
      } else {
        link.parent().addClass('has-error');
      }
    });
  });
})(jQuery);
