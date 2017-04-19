function sortProperties(obj) {
    // convert object into array
    var sortable = [];
    for (var key in obj)
        if (obj.hasOwnProperty(key))
            sortable.push([key, obj[key]]); // each item is an array in format [key, value]

    // sort items by value
    sortable.sort(function (a, b) {
        var x = a[1].toLowerCase(),
            y = b[1].toLowerCase();
        return x < y ? -1 : x > y ? 1 : 0;
    });
    return sortable; // array in format [ [ key1, val1 ], [ key2, val2 ], ... ]
}
jQuery(function ($) {
    var propsByIblockId = {};
    var propVariantsByPropId = {};
    $('.propLink_IBLOCK, .propListValueLink_IBLOCK, .propValueLink_IBLOCK').on('change', function () {
        var iblockId = $(this).val();
        var filter = $(this).data('type-filter') ? $(this).data('type-filter') : false;
        if (iblockId) {
            var id = $(this).attr('id');
            if (!propsByIblockId[iblockId]) {
                getProps(iblockId, function () {
                    updateSelect(id, iblockId, filter);
                });
            } else {
                updateSelect(id, iblockId, filter);
            }
        }

    }).change();

    $('.propValueLink_PROP').on('change', function () {
        var propId = $(this).val();
        var id = $(this).attr('id').replace('_PROPS', '');
        if (propId) {
            if (!propVariantsByPropId[propId]) {
                getPropVariants(propId, function () {
                    generateInput(id, propId, 'propValueLink');
                });
            } else {
                generateInput(id, propId, 'propValueLink');
            }
        } else {
            $("#" + id + "_VALUE").remove();
            $("#" + id + "_HIDDEN").val('');
        }
    });
    $('.propListValueLink_PROP').on('change', function () {
        var propId = $(this).val();
        var id = $(this).attr('id').replace('_PROPS', '');
        if (propId) {
            if (!propVariantsByPropId[propId]) {
                getPropVariants(propId, function () {
                    generateInput(id, propId, 'propListValueLink');
                });
            } else {
                generateInput(id, propId, 'propListValueLink');
            }
        } else {
            $("#" + id + "_VALUE").remove();
            $("#" + id + "_HIDDEN").val('');
        }
    });
    $('.propValueLink_IBLOCK').closest('td').on('keyup', '.propValueLink_VALUE_TEXT', function () {
        var val = $(this).val();
        var id = $(this).attr('id').replace('_VALUE', '');
        var hidden = $('#' + id + '_HIDDEN');
        if (val) {
            var glued;
            pid = $('#' + id + '_PROPS').val();
            glued = {
                'PROPERTY_ID': pid,
                'VALUE': val
            };
            glued = JSON.stringify(glued);
            hidden.val(glued);
        } else {
            hidden.val('');
        }
    });
    $('.propValueLink_IBLOCK').closest('td').on('change', '.propValueLink_VALUE_SELECT', function () {
        var val = $(this).val();
        id = $(this).attr('id').replace('_VALUE', '');
        var glued;
        pid = $('#' + id + '_PROPS').val();
        glued = {
            'PROPERTY_ID': pid,
            'VALUE': val
        };
        glued = JSON.stringify(glued);
        $('#' + id + '_HIDDEN').val(glued);
    });

    $('.propListValueLink_IBLOCK').closest('td').on('change', '.propListValueLink_VALUE_SELECT', function () {
        id = $(this).attr('id').replace('_VALUE', '');
        $('#' + id + '_HIDDEN').val($(this).val());
    });

    function getProps(iblockId, callback) {
        var data = {
            iblockId: iblockId
        };

        $.ajax({
            url: '/bitrix/admin/directline.proplink_prop_link_helper.php',
            method: 'GET',
            data: data,
            dataType: 'json',
            success: function (r) {
                if (r.SUCCESS == 'Y') {
                    propsByIblockId[iblockId] = r.PROPS;

                    callback();
                }
            }
        });
    }

    function getPropVariants(propId, callback) {
        var data = {
            propId: propId
        };
        $.ajax({
            url: '/bitrix/admin/directline.proplink_prop_link_helper.php',
            method: 'GET',
            data: data,
            dataType: 'json',
            success: function (r) {
                if (r.SUCCESS == 'Y') {
                    propVariantsByPropId[propId] = {};
                    propVariantsByPropId[propId]['TYPE'] = r.PROPERTY_TYPE;
                    if (r.PROPERTY_TYPE == 'L' && typeof r.LIST == 'object') {
                        propVariantsByPropId[propId]['LIST'] = r.LIST;
                    }
                    callback();
                }
            }
        });
    }

    function generateInput(id, propId, className) {
        var prop = propVariantsByPropId[propId];
        if (!$.isArray(prop.LIST)) {
            prop.LIST = sortProperties(prop.LIST);
        }

        var control;
        var oldControl = $("#" + id + "_VALUE");
        var propSelect = $("#" + id + "_PROPS");
        value = propSelect.data('initial-value');
        oldControl.remove();
        if (prop.TYPE == 'L') {
            control = $('<select></select>').addClass(className + '_VALUE_SELECT').attr({id: id + '_VALUE'});
            $.each(prop.LIST, function (i, v) {
                var option = $('<option></option>').val(v[0]).text(v[1]);
                if (v[0] == value) {
                    option.attr('selected', true);

                }
                option.appendTo(control);
            });
        } else {
            control = $('<input/>').addClass(className + '_VALUE_TEXT').attr({
                type: 'text',
                id: id + '_VALUE'
            }).val(value);
        }
        propSelect.removeAttr('data-initial-value');
        propSelect.removeData('initial-value');

        control.insertAfter('#' + id + "_PROPS").change().keyup();
    }

    function updateSelect(id, iblockId, propType) {
        var propType = typeof propType !== 'undefined' ? propType : false;
        var select = $('#' + id + '_PROPS');
        select.empty();
        var initialValue = select.data('initial-propid');
        $('<option></option>').text('--').val('').appendTo(select);
        $.each(propsByIblockId[iblockId], function (i, v) {
            if (!propType || propType == v.TYPE) {
                var option = $('<option></option>');
                option.text('[' + v.ID + '] ' + v.NAME + ' (' + v.CODE + ')').val(v.ID);
                if (initialValue && initialValue == v.ID) {
                    option.attr('selected', true);
                }
                option.appendTo(select);
            }
        });
        select.change();
    }
});