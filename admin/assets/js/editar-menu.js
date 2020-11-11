$(document).ready(function () {

    let updateOutput = function (e) {
        var list = e.length ? e : $(e.target),
                output = list.data('output');
        if (window.JSON) {
            output.val(window.JSON.stringify(list.nestable('serialize')));//, null, 2));
        } else {
            output.val('JSON browser support required for this demo.');
        }
    };
    // activate Nestable for list 1
    $('#nestable').nestable({
        group: 1
    }).on('change', updateOutput);

    // output initial serialised data
    updateOutput($('#nestable').data('output', $('#nestable-output')));


    $('.edit-this-item').click(function () {
        let code = $(this).attr('data-code');
        $('#edit-action-menu #edit-code').val(code);
        $.getJSON(admin + '/sistema/ajax-get-menu/?id=' + code, function (data) {
            $.each(data, function (i, valor) {
                if (i == "oculto") {
                    if (valor == "Y") {
                        $('#edit-action-menu #edit-oculto').attr('checked', true);
                    } else {
                        $('#edit-action-menu #edit-oculto').attr('checked', false);
                    }
                } else {
                    $('#edit-action-menu #edit-' + i).val(valor);
                }
            });
        });
    });

    $('#nestable_list_menu').on('click', function (e) {
        let target = $(e.target), action = target.data('action');
        if (action === 'expand-all') {
            $('.dd').nestable('expandAll');
        }
        if (action === 'collapse-all') {
            $('.dd').nestable('collapseAll');
        }
    });

    $("#submit-menu").click(function () {
        var dataString = {
            data: $("#nestable-output").val(),
        };
        
        $.ajax({
            type: "POST",
            url: admin+'/sistema/js-update-menu-order',
            data: dataString,
            cache: false,
            success: function (data) {
                toastr.success('O menu foi salvo corretamente.<br/><strong><a href="'+admin+'/atualiza-permissao">Atualize as permissões</a></strong>', 'Menu salvo')
//                $("#load").hide();
            }, error: function (xhr, status, error) {
                toastr.error('Ocorreu uma falha ao salvar o menu', 'Ops!')
                alert(error);
            },
        });
    });
});