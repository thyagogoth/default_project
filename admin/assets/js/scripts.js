var dataTargetProfit = [
    [1354586000000, 153],
    [1364587000000, 658],
    [1374588000000, 198],
    [1384589000000, 663],
    [1394590000000, 801],
    [1404591000000, 1080],
    [1414592000000, 353],
    [1424593000000, 749],
    [1434594000000, 523],
    [1444595000000, 258],
    [1454596000000, 688],
    [1464597000000, 364]
]
var dataProfit = [
    [1354586000000, 53],
    [1364587000000, 65],
    [1374588000000, 98],
    [1384589000000, 83],
    [1394590000000, 980],
    [1404591000000, 808],
    [1414592000000, 720],
    [1424593000000, 674],
    [1434594000000, 23],
    [1444595000000, 79],
    [1454596000000, 88],
    [1464597000000, 36]
]
var dataSignups = [
    [1354586000000, 647],
    [1364587000000, 435],
    [1374588000000, 784],
    [1384589000000, 346],
    [1394590000000, 487],
    [1404591000000, 463],
    [1414592000000, 479],
    [1424593000000, 236],
    [1434594000000, 843],
    [1444595000000, 657],
    [1454596000000, 241],
    [1464597000000, 341]
]
var dataSet1 = [
    [0, 10],
    [100, 8],
    [200, 7],
    [300, 5],
    [400, 4],
    [500, 6],
    [600, 3],
    [700, 2]
];
var dataSet2 = [
    [0, 9],
    [100, 6],
    [200, 5],
    [300, 3],
    [400, 3],
    [500, 5],
    [600, 2],
    [700, 1]
];

$(document).ready(function () {

    /* init datatables */
    if ( $('#dt-basic-example').length > 0 ) {
        $('#dt-basic-example').dataTable({
        "order": [[ 0, "desc" ]],
        "iDisplayLength": 25,
        "columnDefs": [
            {"width": "auto", "targets": 0, "visible": false},
            {"width": "auto", "targets": 1}
        ],
        "searching": true,
        "ordering": true,
        "showing": true,
        "paging": true,
        "info": true,
        "select": false,
        // "autoFill": true,
        "language": {
            "processing": "Processando solicitação...",
            // "search":           "Pesquisar por:",
            "lengthMenu": "Exibir _MENU_ registros por página",
            "info": "Exibindo página _PAGE_ de _PAGES_",
            "infoEmpty": "Nenhum registro disponível",
            "infoFiltered": "(filtered de _MAX_ registros encontrados)",
            "infoPostFix": "",
            "loadingRecords": "Chargement en cours...",
            "zeroRecords": "Nenhum item encontrado - desculpe",
            "emptyTable": "Nenhum item cadastrado",
            "paginate": {
                "first": "Primeira",
                "previous": "Anterior",
                "next": "Próxima",
                "last": "Última"
            },
            "aria": {
                "sortAscending": ": ativar para classificar a coluna em ordem crescente",
                "sortDescending": ": ativar para classificar a coluna em ordem decrescente"
            }
        },
        "pagingType": "full_numbers",
        responsive: true,
        fixedHeader: true,
        dom: "<'row mb-3'<'col-sm-12 col-md-5 d-flex align-items-center justify-content-start'f><'col-sm-12 col-md-4 d-flex align-items-center justify-content-end'B><'col-sm-12 col-md-3 d-flex align-items-center justify-content-start'l>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
        buttons: [{
                extend: 'colvis',
                text: 'Visibilidade',
                titleAttr: 'Colunas a exibir',
                className: 'btn-outline-default'
            },
            {
                extend: 'csvHtml5',
                text: 'Exportar',
                titleAttr: 'Gerar arquivo CSV',
                className: 'btn-outline-default'
            },
            {
                extend: 'print',
                text: '<i class="fal fa-print"></i>',
                titleAttr: 'Imprimir tabela',
                className: 'btn-outline-default'
            }

        ],
    });
    }

    /* flot toggle example */
    var flot_toggle = function () {

        var data = [{
                label: "Target Profit",
                data: dataTargetProfit,
                color: myapp_get_color.info_400,
                bars: {
                    show: true,
                    align: "center",
                    barWidth: 30 * 30 * 60 * 1000 * 80,
                    lineWidth: 0,
                    /*fillColor: {
                     colors: [myapp_get_color.primary_500, myapp_get_color.primary_900]
                     },*/
                    fillColor: {
                        colors: [{
                                opacity: 0.9
                            },
                            {
                                opacity: 0.1
                            }
                        ]
                    }
                },
                highlightColor: 'rgba(255,255,255,0.3)',
                shadowSize: 0
            },
            {
                label: "Actual Profit",
                data: dataProfit,
                color: myapp_get_color.warning_500,
                lines: {
                    show: true,
                    lineWidth: 2
                },
                shadowSize: 0,
                points: {
                    show: true
                }
            },
            {
                label: "User Signups",
                data: dataSignups,
                color: myapp_get_color.success_500,
                lines: {
                    show: true,
                    lineWidth: 2
                },
                shadowSize: 0,
                points: {
                    show: true
                }
            }
        ]

        var options = {
            grid: {
                hoverable: true,
                clickable: true,
                tickColor: '#f2f2f2',
                borderWidth: 1,
                borderColor: '#f2f2f2'
            },
            tooltip: true,
            tooltipOpts: {
                cssClass: 'tooltip-inner',
                defaultTheme: false
            },
            xaxis: {
                mode: "time"
            },
            yaxes: {
                tickFormatter: function (val, axis) {
                    return "$" + val;
                },
                max: 1200
            }

        };

        var plot2 = null;

        function plotNow() {
            var d = [];
            $("#js-checkbox-toggles").find(':checkbox').each(function () {
                if ($(this).is(':checked')) {
                    d.push(data[$(this).attr("name").substr(4, 1)]);
                }
            });
            if (d.length > 0) {
                if (plot2) {
                    plot2.setData(d);
                    plot2.draw();
                } else {
                    plot2 = $.plot($("#flot-toggles"), d, options);
                }
            }

        }
        ;

        $("#js-checkbox-toggles").find(':checkbox').on('change', function () {
            plotNow();
        });
        plotNow()
    }
    flot_toggle();
    /* flot toggle example -- end*/

    if ( $('#flot-area').length > 0 ) {
        /* flot area */
        var flotArea = $.plot($('#flot-area'), [{
                data: dataSet1,
                label: 'New Customer',
                color: myapp_get_color.success_200
            },
            {
                data: dataSet2,
                label: 'Returning Customer',
                color: myapp_get_color.info_200
            }
        ], {
            series: {
                lines: {
                    show: true,
                    lineWidth: 2,
                    fill: true,
                    fillColor: {
                        colors: [{
                                opacity: 0
                            },
                            {
                                opacity: 0.5
                            }
                        ]
                    }
                },
                shadowSize: 0
            },
            points: {
                show: true,
            },
            legend: {
                noColumns: 1,
                position: 'nw'
            },
            grid: {
                hoverable: true,
                clickable: true,
                borderColor: '#ddd',
                tickColor: '#ddd',
                aboveData: true,
                borderWidth: 0,
                labelMargin: 5,
                backgroundColor: 'transparent'
            },
            yaxis: {
                tickLength: 1,
                min: 0,
                max: 15,
                color: '#eee',
                font: {
                    size: 0,
                    color: '#999'
                }
            },
            xaxis: {
                tickLength: 1,
                color: '#eee',
                font: {
                    size: 10,
                    color: '#999'
                }
            }

        });
        /* flot area -- end */

        var flotVisit = $.plot('#flotVisit', [{
                data: [
                    [3, 0],
                    [4, 1],
                    [5, 3],
                    [6, 3],
                    [7, 10],
                    [8, 11],
                    [9, 12],
                    [10, 9],
                    [11, 12],
                    [12, 8],
                    [13, 5]
                ],
                color: myapp_get_color.success_200
            },
            {
                data: [
                    [1, 0],
                    [2, 0],
                    [3, 1],
                    [4, 2],
                    [5, 2],
                    [6, 5],
                    [7, 8],
                    [8, 12],
                    [9, 9],
                    [10, 11],
                    [11, 5]
                ],
                color: myapp_get_color.info_200
            }
        ], {
            series: {
                shadowSize: 0,
                lines: {
                    show: true,
                    lineWidth: 2,
                    fill: true,
                    fillColor: {
                        colors: [{
                                opacity: 0
                            },
                            {
                                opacity: 0.12
                            }
                        ]
                    }
                }
            },
            grid: {
                borderWidth: 0
            },
            yaxis: {
                min: 0,
                max: 15,
                tickColor: '#ddd',
                ticks: [
                    [0, ''],
                    [5, '100K'],
                    [10, '200K'],
                    [15, '300K']
                ],
                font: {
                    color: '#444',
                    size: 10
                }
            },
            xaxis: {

                tickColor: '#eee',
                ticks: [
                    [2, '2am'],
                    [3, '3am'],
                    [4, '4am'],
                    [5, '5am'],
                    [6, '6am'],
                    [7, '7am'],
                    [8, '8am'],
                    [9, '9am'],
                    [10, '1pm'],
                    [11, '2pm'],
                    [12, '3pm'],
                    [13, '4pm']
                ],
                font: {
                    color: '#999',
                    size: 9
                }
            }
        });
    }

});

function implementsConfirm(elem) {
    var _target = elem.attr('data-target');
    var _src = elem.attr('data-src');
    var x = $(_target + ' a ').attr('href', _src);
    
    initApp.playSound(admin+"/assets/media/sound", "messagebox")
}

// Class definition
var controls = {
    leftArrow: '<i class="fal fa-angle-left" style="font-size: 1.25rem"></i>',
    rightArrow: '<i class="fal fa-angle-right" style="font-size: 1.25rem"></i>'
}

var runDatePicker = function () {
    // range picker
    $('.date-range-picker').datepicker({
        todayHighlight: true,
        format: 'dd/mm/yyyy',
        language: 'pt_BR',
        templates: controls
    });

}

$(document).ready(function () {
    if ( $('.date-range-picker').length > 0 ) {   
        runDatePicker();
    }
});
