(function ($) {
    //alert(record_type);
    'use strict';
    // Students table
    $(document).ready(function () {
        var dTable = $('#product_table').DataTable({
            serverSide: true,
            processing: true,
            language: {
                processing: '<i class="ace-icon fa fa-spinner fa-spin orange bigger-500" style="font-size:60px;margin-top:50px;"></i>'
            },
            scroller: {
                loadingIndicator: false
            },
            pagingType: "full_numbers",
            ajax: {
                url: productListAjax,
                "dataType": "json",
                "type": "get",
            },
            columns: [
                { "data": "product_name" },
                { "data": "price" },
                { "data": "shopkeeper" },
                { "data": "shop_name" },
                { "data": "action", sortable: false },
            ]

        });
    });

})(jQuery);