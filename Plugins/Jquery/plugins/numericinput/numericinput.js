$(function () {
    $.fn.forceNumericOnly = function (withDecimals) {

        function getCursorPosition(input) {
            if (!input)
                return; // No (input) element found
            if ('selectionStart' in input) {
                // Standard-compliant browsers
                return input.selectionStart;
            } else if (document.selection) {
                // IE
                input.focus();
                var sel = document.selection.createRange();
                var selLen = document.selection.createRange().text.length;
                sel.moveStart('character', -input.value.length);
                return sel.text.length - selLen;
            }
        }

        return this.each(function () {
            if (!$(this).data('numeric_inited')) {
                var input = this;
                $(this).keydown(function (e) {

                    var key = e.charCode || e.keyCode || 0;

                    var isMinus = key == 173 || key == 109 || key == 189 || e.key == '-';
                    if (isMinus) {
                        if (this.value.indexOf('-') > -1) {
                            return false;
                        }
                        if (this.selectionStart != 0) {
                            return false;
                        }
                    }


                    // allow backspace, tab, delete, arrows, numbers and keypad numbers ONLY
                    var allow = key == 8 ||
                            key == 9 ||
                            key == 13 ||
                            key == 35 || key == 36 || // Home End
                            key == 46 ||
                            key == 116 || // F5
                            isMinus ||
                            (key >= 37 && key <= 40) ||
                            (key >= 48 && key <= 57) ||
                            (key >= 96 && key <= 105);

                    if (withDecimals) {

                        allow = allow ||
                                (key == 188 || key == 190 || key == 191 || key == 110)
                                && this.value.split('.').length < 2 && getCursorPosition(this) > 0;

                        setTimeout(function () {
                            input.value = input.value.replace('?', '.').replace('/', '.').replace(',', '.');
                        });

                    }
                    return allow;
                });
                $(this).data('numeric_inited', 1);
            }
        });
    };
    $(".numeric").forceNumericOnly();
    $(".numeric-decimals").forceNumericOnly(true);
});
