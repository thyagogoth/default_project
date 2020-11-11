$(document).ready(function () {

    check_parents_init();
    $('.check_parent').on('click', function () {
        var parent = $(this).parent();
        if ($(this).prop("checked")) {
            parent.find(":checkbox").prop("checked", true);
            checkParents($(this));
        } else {
            checkParents($(this));
            parent.find(":checkbox").prop("checked", false);
            checkParentsUncheck($(this));
        }
    });
    $('.check_children').on('click', function () {
        if ($(this).prop("checked")) {
            checkParents($(this));
        } else {
            checkParentsUncheck($(this));
        }
    });
});

function checkParents(item) {
    var arrParents = item.parents('.check_parent_div');
    arrParents.each(function () {
        var cd = $(this).attr('id').split('_');
        $(this).find('#check_parent_' + cd[1]).prop('checked', true);
    });
}

function checkParentsUncheck(item) {
    var parent = item.parent().parent();
    if (parent.parent().attr('id')) {
        var id = parent.parent().attr('id').split('_');
        var flag = 0;
        parent.find(":checkbox").each(function () {
            if ($(this).prop('checked')) {
                flag = 1;
            }
        });
        if (flag == 1) {
            $('#check_parent_' + id[1]).prop('checked', true);
        } else {
            $('#check_parent_' + id[1]).prop('checked', false);
            checkParentsUncheck($('#check_parent_' + id[1]));
        }
    }
}

function check_parents_init() {
    $(document).find(":checkbox").each(function () {
        if ($(this).prop('checked')) {
            var parent = $(this).parent().parent();
            if (parent.parent().attr('id') != undefined) {
                var id = parent.parent().attr('id').split('_');
                $('#check_parent_' + id[1]).prop('checked', true);
                check_parents_parents_init($('#check_parent_' + id[1]));
            }
        }
    });
}
function check_parents_parents_init(parent) {
    if (parent.parent().parent().parent().attr('id')) {
        var id = parent.parent().parent().parent().attr('id').split('_');
        $('#check_parent_' + id[1]).prop('checked', true);
        check_parents_parents_init($('#check_parent_' + id[1]));
    }
}
