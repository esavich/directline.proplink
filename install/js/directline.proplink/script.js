jQuery(function ($) {
    var propsByIblockId = {};
    $('.propLink_IBLOCK').on('change', function () {
        var iblockId = $(this).val();
        if (iblockId) {
            var id = $(this).attr('id');
            if (!propsByIblockId[iblockId]) {
                getProps(iblockId, function () {
                    updateSelect(id, iblockId);
                });
            } else {
                updateSelect(id, iblockId);
            }
        }

    }).change();

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

    function updateSelect(id, iblockId) {
        var select = $('#' + id + '_PROPS');
        select.empty();
        var initialValue = select.data('initial-value');
        $('<option></option>').text('--').appendTo(select);
        $.each(propsByIblockId[iblockId], function (i, v) {
            var option = $('<option></option>');
            option.text('[' + v.ID + '] ' + v.NAME + ' (' + v.CODE + ')').val(v.ID);
            if (initialValue && initialValue == v.ID) {
                option.attr('selected', true);
            }
            option.appendTo(select);
        });
    }
})