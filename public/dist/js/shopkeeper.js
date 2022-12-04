(function ($) {
    //alert(record_type);
    'use strict';
    // Students table
    $(document).ready(function () {
        var dTable = $('#shopkeeper_table').DataTable({
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
                url: shopkeeperListAjax,
                "dataType": "json",
                "type": "get",
            },
            columns: [
                { "data": "username" },
                { "data": "email" },
                { "data": "status" },
                { "data": "action", sortable: false },
            ]

        });
    });

})(jQuery);