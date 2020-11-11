$(document).ready(function () {
    var i = $('#n_redes').val();
    $('#addRede').click(function () {
        $('#redes').append(getHtmlRede(++i));
    });
    $('.js-excluir').click(function () {
        var _string = $(this).attr('id');
        var _id = _string.split('_')[1];
        if (confirm('Tem certeza que deseja remover o item ' + _id + '?')) {
            $('.dev_' + _id).remove();
        }
    });
});

function getHtmlRede(i) {
    var _cont = $('.js-alt-icone').size();
    _cont = _cont + 1;
    var _html = '<div class="form-group dev_' + _cont + '">' +
        '    <label for="item' + i + '" class="col-sm-2 control-label">Rede social ' + _cont + '</label>' +
        '    <div class="col-sm-2">' +
        '        <input type="text" class="form-control" id="item' + i + '" value="" name="red[' + _cont + '][item]" required>' +
        '    </div>' +
        '    <label for="link' + i + '" class="col-sm-1 control-label">Link ' + _cont + '</label>' +
        '    <div class="col-sm-2">' +
        '        <input type="text" class="form-control mask-link" id="link' + i + '" value="" name="red[' + _cont + '][link]" required>' +
        '    </div>' +
        '    <label for="icone' + i + '" class="col-sm-1 control-label">&Iacute;cone ' + _cont + '</label>' +
        '    <div class="col-sm-2">' +
        '        <select class="chosen-select chosen-transparent form-control js-alt-icone" rel="_' + _cont + '" id="icone' + i + '" name="red[' + _cont + '][icone]" required>' +
        '            <option value="">-- &Iacute;cone</option>' +
        writeHtmlIcones() +
        '       </select>' +
        '    </div>' +
        '    <div class="col-sm-1 show_icon" id="show_icone_' + _cont + '">' +
        '        <i class="fa fa-2x" id="js-icone_ ' + _cont + '"></i>' +
        '    </div>' +
        '</div>';
    return _html;
}

function writeHtmlIcones() {
    var _innerHtml = '';
    $.ajax({
        url: $("#raiz").attr('content') + 'config/js-find-all-icones/',
        type: 'get',
        async: false,
        success: function (resposta) {
            if (resposta != undefined && resposta != '') {
                _innerHtml = resposta;
            }
        }
    });
    return _innerHtml;
}
