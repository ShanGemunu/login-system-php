$(function () {
    $('td').css('box-shadow', 'none');

    let orderTable;

// fetch products from cart
    function createOrderTable() {
        orderTable = new DataTable("#orders-table", {
            processing: true,
            serverSide: true,
            ajax: {
                url: "http://localhost/orders",
                type: "POST",
                error: function (xhr, error, thrown) {
                    // Handle the error here
                    console.error("Error during AJAX request:", error, thrown);
                },
            },
            columns: [
                { 
                    data: 'order_id',
                    defaultContent: "<img src=''>",
                },
                { 
                    data: 'order_date',
                    defaultContent: "<img src=''>",
                },
                { 
                    data: 'order_payment_method',
                    defaultContent: "<img src=''>",
                },
                { 
                    data: 'order_status',
                    defaultContent: "<img src=''>",
                },
                { 
                    data: 'products',
                    defaultContent: "<img src=''>",
                }
            ]
        });
    }

    createOrderTable();
});