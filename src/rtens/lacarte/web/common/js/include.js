$.each($('[data-include]'), function (i, element) {
    $.get($(element).data('include'), function(data) {
        $(element).html($(data));
    });
});