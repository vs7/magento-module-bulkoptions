var markChanged = function (instance, td, row, col, prop, value, cellProperties) {
    var deleteOption = instance.getDataAtRowProp(row, 'delete');
    if (prop == 'checked' || prop == 'delete') {
        Handsontable.renderers.CheckboxRenderer.apply(this, arguments);
    } else {
        Handsontable.renderers.TextRenderer.apply(this, arguments);
    }
    if (cellProperties.changed) {
        jQuery(td).addClass('changed');
    }
    if (deleteOption) {
        if (prop != 'delete') {
            cellProperties.readOnly = true;
        }
        jQuery(td).addClass('delete');
    } else {
        if (prop != 'delete' && prop != 'id') {
            cellProperties.readOnly = false;
        }
        jQuery(td).removeClass('delete');
    }
};

jQuery(document).ready(function () {
    jQuery.noConflict();

    var $container = jQuery("#bulkoptions");

    $container.handsontable({
        data: data,
        rowHeaders: false,
        colHeaders: colHeaders,
        columns: columns,
        cells: function (row, col, prop) {
            this.renderer = markChanged;
            var cellProperties = {};
            if (col === 0) cellProperties.readOnly = true;
            return cellProperties;
        },
        currentRowClassName: 'currentRow',
        currentColClassName: 'currentCol',
        autoWrapRow: true,
        columnSorting: true,
        manualColumnResize: true
    });

    $container.on('mouseup', 'input:checkbox', function (event) {
        $container.handsontable('render');
    });

    $container.handsontable('getInstance').addHook('afterChange', function(changes) {
        var ele = this;
        jQuery.each(changes, function (index, element) {
            if (element[1] == 'delete') return;
            var cell = jQuery(ele.getCell(element[0],ele.propToCol(element[1])));
            var rowId = ele.getDataAtRowProp(element[0], 'id');
            var cellMeta = ele.getCellMeta(element[0],ele.propToCol(element[1]));
            if (rowId && dataById[rowId]) {
                if (dataById[rowId][element[1]] != element[3]) {
                    cell
                        .addClass('changed')
                        .qtip({
                            content: {
                                text: 'was: ' + dataById[rowId][element[1]]
                            },
                            position: {
                                my: 'bottom center',
                                at: 'top center'
                            }
                        });
                    cellMeta.changed = true;
                } else {
                    cell
                        .removeClass('changed')
                        .qtip('destroy', true);
                    cellMeta.changed = false;
                }
            } else {
                if (element[3]) {
                    cell
                        .addClass('changed')
                        .qtip({
                            content: {
                                text: 'new'
                            },
                            position: {
                                my: 'bottom center',
                                at: 'top center'
                            }
                        });
                    cellMeta.changed = true;
                } else {
                    cell
                        .removeClass('changed')
                        .qtip('destroy', true);
                    cellMeta.changed = false;
                }
            }
        });
    });

    jQuery('#add_new_option_button').on('click', function () {
        $container.handsontable('alter', 'insert_row', 0);
        $container.handsontable('setDataAtCell', 0, 0, 'option_' + ($container.handsontable('getData').length - 1));
    });

    varienGlobalEvents.attachEventHandler('showTab', function () {
        if ($container.is(":visible")) {
            $container.handsontable('render');
        }
    });

    varienGlobalEvents.attachEventHandler('formSubmit', function () {
        generateInputs(jQuery('#bulkoptions-inputs'), $container.handsontable('getData'));
    });

    function generateInputs(container, data) {
        container.empty();
        for (var key in data) {
            for (var label in data[key]) {
                var id = data[key]['id'];
                var value = "";
                if (data[key][label] != null) {
                    value = data[key][label];
                }
                if (storeId = label.match(/store(\d+)/)) {
                    container.append('<input type="hidden" name="option[value][' + id + '][' + storeId[1] + ']" value="' + value + '" />');
                }
                if (label == 'delete') {
                    var deleteFlag = "";
                    if (value) {
                        deleteFlag = "1";
                    }
                    container.append('<input type="hidden" name="option[delete][' + id + ']" value="' + deleteFlag + '" />');
                }
                if (label == 'sort_order') {
                    container.append('<input type="hidden" name="option[order][' + id + ']" value="' + value + '" />');
                }
                if (label == 'checked' && value == '1') {
                    container.append('<input type="hidden" name="default[]" value="' + id + '" />');
                }
            }
        }
    }
});