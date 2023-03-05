(function ($) {
    "use strict";
    // Roles data table
    $(document).ready(function () {
        


        var dTable = $("#cms_pages_table").DataTable({
            processing: true,
            responsive: false,
            serverSide: true,
            ajax: {
                url: "CMS-Pages/get-list",
                type: "get",
            },
            columns: [
                {
                    data: "type",
                    name: "type",
                    orderable: true,
                    searchable: true,
                },
                {
                    data: "action",
                    name: "action",
                    orderable: true,
                    searchable: true,
                },
            ],
        });
    });
})(jQuery);
