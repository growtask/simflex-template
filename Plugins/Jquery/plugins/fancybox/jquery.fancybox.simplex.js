$(function () {
    $('.fancybox, .lightview').fancybox({helpers: {overlay: {locked: false}}});
    $('.fancybox-js').click(function () {
        fancyboxAjax($(this).attr('href'));
        return false;
    });

});
var fancyboxAjax = function (url, callback) {
    $.get(url, function (r) {
        var params = {content: r, helpers: {overlay: {locked: false}}};
        if (callback) {
            params['afterShow'] = callback;
        }
        $.fancybox(params);
    });
};
