$(function () {
    habilita_datas();
    $('#programado').click(function () {
        habilita_datas($(this).val());
    });

});

function habilita_datas(valor) {
    if ($('#programado').is(':checked')) {
        $('#programado').val('Y');
        $(".js-data-programada input[type=text]").attr('required', true);
        $(".js-data-programada").css('display', 'block');
    } else {
        $('#programado').val('N');
        $(".js-data-programada input[type=text]").attr('required', false);
        $(".js-data-programada").css('display', 'none');
        $(".js-data-programada input[type=text]").val('');
    }
}
