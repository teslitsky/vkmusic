(function ($) {
  $(function () {
    $('#download').click(function (e) {
      var link, title;
      e.preventDefault();
      var input = $('#link').val();
      if (input.length) {
        $('#link').parent().removeClass('has-error');
        var u = new Url(input);
        var q = u.query['w'];
        var wallId = q.replace('wall-', '');
        window.location.href = '/get.php?wall=' + encodeURIComponent(wallId);
      } else {
        $('#link').parent().addClass('has-error');
      }
    });
  });
})(jQuery);
