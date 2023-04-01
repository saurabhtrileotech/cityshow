(function ($) {
    //alert(record_type);
    'use strict';
    // Students table
    $(document).ready(function () {
        var dTable = $('#city_table').DataTable({
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
                url: cityListAjax,
                "dataType": "json",
                "type": "get",
            },
            columns: [
                { "data": "name" },
                { "data": "postcode" },
                { "data": "action", sortable: false },
            ]

        });

        $(document).on("click", ".js-city-delete", function () {
            var id = $(this).data("id");
            console.log(id);
            Swal.fire({
                text: "Are you sure you want to delete city?",
                icon: "info",
                showCancelButton: !0,
                confirmButtonText: "Yes",
                cancelButtonText: "No",
                reverseButtons: !0,
            }).then(
                function (e) {
                    if (e.value === true) {
                        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr(
                            "content"
                        );
                        $.ajax({
                            type: "DELETE",
                            dataType: "json",
                            url: `city/delete/`+id,
                            data: {
                                "_token": CSRF_TOKEN,
                                },
                            success: function (data) {
                                if (data.status == "true") {
                                    dTable.draw(false);
                                    setTimeout(function () {
                                        Swal.fire({
                                        icon: 'error',
                                        tex:data.message});
                                    }, 1000);
                                } else {
                                    dTable.draw(false);
                                    setTimeout(function () {
                                        Swal.fire(data.message);
                                    }, 1000);
                                }
                            },
                        });
                    } else {
                        e.dismiss;
                    }
                },
                function (dismiss) {
                    return false;
                }
            );
        });
    });

})(jQuery);