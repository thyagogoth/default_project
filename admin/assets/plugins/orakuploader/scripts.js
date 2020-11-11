var copiar = false;

var _htmlCopiar = '';
if ( $('#orak_copy').length > 0 ) {
    copiar = $('#orak_copy').attr('content');
}
$(function () {
    var dir_imgs = '';
    if (document.getElementById('dir_imgs') != '' && document.getElementById('js-id-cadastro').value) {
        var id_img = document.getElementById('js-id-cadastro').value;
        dir_imgs = document.getElementById('dir_imgs');
        dir_imgs = dir_imgs.content + '_' + id_img;
    }
    $('#imagem_up').orakuploader({
        orakuploader: true,
        orakuploader_path: admin + '/assets/plugins/orakuploader/',
        orakuploader_main_path: 'uploads',
        orakuploader_thumbnail_path: 'uploads',
        orakuploader_url_path: 'uploads',
        orakuploader_use_main: false,
        orakuploader_use_sortable: true,
        orakuploader_use_dragndrop: true,
        orakuploader_add_image: admin + '/assets/plugins/orakuploader/images/add.png',
        orakuploader_add_label: 'Procurar imagens',
        orakuploader_resize_to: 1200,
        //orakuploader_thumbnail_size: 980,
        orakuploader_miniaturas: site + '/uploads',
        orakuploader_url_admin: admin
    });
    var id = document.getElementById('js-id-cadastro');
    id = id.value;
    if (id) {
        $.ajax({
            url: admin + '/' + module + '/get-imagens/?id=' + id+'&modulo='+module,
            data: '',
            success: function (resposta) {
                if (resposta.trim() !== 'false') {
                    resposta = resposta.split('|');
                    var html = '';
                    for (i = 0; i < resposta.length; i++) {
                        if ( copiar == 'true' ) {
                            _htmlCopiar = '<div class="js-copia-url picture_copy " title="Copiar URL da imagem" id="' + resposta[i] + '" url="' + site + '/uploads/' + dir_imgs + '/' + resposta[i] + '"></div>';
                        }
                        html += '<div class="multibox file" style="cursor: move;" id="' + resposta[i] + '" filename="' + resposta[i] + '"><div class="js-deleta-img picture_delete" id="' + resposta[i] + '"></div>'+_htmlCopiar+'<img class="picture_uploaded" src="' + admin + '/thumb.php?src=../uploads/' + dir_imgs + '/' + resposta[i] + '&x=138&y=108&q=80&type=fill"> <input value="' + resposta[i] + '" name="imagem_up[]" type="hidden" ></div>';
                    }
                    $("#imagem_up").append(html);
                }
            }
        });
    }

    $( "body" ).on( "click", ".js-copia-url", function() {
        var $temp = $("<input>");
        $("body").append($temp);
        $temp.val($(this).attr('url')).select();
        document.execCommand("copy");
        $temp.remove();
        toastr.success('O caminho do item foi copiado para a Área de transferência (CTRL-C)', 'Feito')
    });
});

function remove_img_bd(img, url) {
    $.ajax({
        url: admin + '/' + module + '/ajax-remove-imagem/?nome=' + img,
        data: '',
        success: function (resposta) {
            toastr.success('A imagem foi removida', 'Feito')
        }
    });
}
