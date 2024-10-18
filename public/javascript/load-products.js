$(function () {
    $('td').css('box-shadow', 'none');

    let productTable;

    // fetch products from cart
    function createProductTable() {
        productTable = new DataTable("#product-table", {
            hover: false,
            initComplete: function() {
                // Remove the specified classes from the target <th> element
                $('th[data-dt-column="0"]').removeClass('dt-orderable-desc dt-ordering-asc');
                $('th[data-dt-column="0"]').removeClass('dt-orderable-asc');
            },
            processing: true,
            serverSide: true,
            ajax: {
                url: "http://localhost/product/get-products",
                type: "POST",
                error: function (xhr, error, thrown) {
                    // Handle the error here
                    console.error("Error during AJAX request:", error, thrown);
                },
            },
            columns: [
                { 
                    data: 'productCard',
                    defaultContent: "<img src=''>",
                },
            ]
        });
    }

    function updateOrders(){
        productTable.clear();  // Clear existing row
        productTable.draw(false);  // Redraw the table
    }

    $('#myButton').on('click', function(){
        updateOrders();
    });

    createProductTable();
});