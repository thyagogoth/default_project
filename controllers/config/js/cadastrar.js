$(function () {
    var cep = '';
    $('#cep').blur(function () {
        cep = $('#cep').val();
        if (cep !== '') {
            $.ajax({
                url: $("#raiz").attr('content') + 'config/web-service/get-cep/' + cep,
                type: 'get',
                async: true,
                beforeSend: desabilita(),
                success: function (resposta) {
                    resposta = JSON.parse(resposta);
                    if (resposta.status != 'erro') {
                        $('#rua').val(resposta.logradouro);
                        $('#bairro').val(resposta.bairro);
                        $('#cidade').val(resposta.localidade);
                        $('#uf').val(resposta.uf);
                        habilita();
                    }
                }
            });
        }
    });
    $('.ajax-loading').hide('fast');
});

function desabilita() {
    $('.ajax-loading').show('fast');
}

function habilita() {
    $('.ajax-loading').hide('fast');
    $(".chosen-select").trigger("chosen:updated");
}
