$(document).ready(function () {

    if ($(".popupblock").length) {
        $.fn.popupblock = function (arg1) {
            if ('hide' == arg1) {
                $(this).fadeOut(250, function () {
                    $('.popupblock-shroud').remove();
                });
                return;
            }

            if ($(this).hasClass('popupblock-modal')) {
                $("body").append('<div class="popupblock-shroud" />');
            }

            $(this).fadeIn(100, function () {
                if (typeof arg1 === 'function') {
                    arg1(this);
                }
            });

        };
        $("body").click(function () {
            $(".popupblock").popupblock('hide');
        });
        $(".popupblock, a[onclick], a.js").click(function (e) {
            e.stopPropagation();
        });
        $('body').keydown(function (e) {
            if (e.which == 27) {
                $(".popupblock").popupblock('hide');
            }
        });
    }

});