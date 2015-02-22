Zepto(function ($) {
    $(document).on('change paste keyup', '#link', function () {
        var input = $(this);
        var link = input.val();
        if (link.length) {
            $.getJSON('get.php?url=' + encodeURIComponent(link), function (result) {
                if (result && result.error) {
                    setError(input);
                } else {
                    var audio = result.data;
                    if ($.isArray(audio) && audio.length) {
                        removeError(input);
                        var template = _.template($("#audioList").html());
                        $("#audio").html(template({items: audio}));
                    } else {
                        setError(input);
                    }
                }
            });
        }
    });

    $(document).on('click', 'button', function (e) {
        e.preventDefault();
        window.location = 'download.php?' + $(this).data('attachment');
    });

    function setError(input) {
        input.parent().removeClass('has-success').addClass('has-error');
    }

    function removeError(input) {
        input.parent().removeClass('has-error').addClass('has-success');
    }
});
