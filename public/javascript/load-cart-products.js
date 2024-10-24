$(function () {
    $('td').css('box-shadow', 'none');

    let productTable;

    // fetch products from cart
    function createCartTable() {
        productTable = new DataTable("#product-table", {
            hover: false,
            initComplete: function () {
                // Remove the specified classes from the target <th> element
                $('th[data-dt-column="0"]').removeClass('dt-orderable-desc dt-ordering-asc');
                $('th[data-dt-column="0"]').removeClass('dt-orderable-asc');
            },
            processing: true,
            serverSide: true,
            ajax: {
                url: "http://localhost/cart/get-products",
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

    function updateCart(productId, quantity_) {
        let urlForRequest;
        let data;
        if(quantity_ === 0){
            urlForRequest = "http://localhost/cart/remove-product";
            data = {
                product_id: productId
            }
        }else{
            urlForRequest = "http://localhost/cart/update-product";
            data = {
                product_id: productId,
                quantity: quantity_
            }
        }

        $.ajax({
            url: urlForRequest, // URL of the server-side script
            type: 'POST',              // 'POST' for sending data
            contentType: 'application/json',
            data: JSON.stringify(data),
            success: function (response) {
                let responseObject = JSON.parse(response);
                if (responseObject.success) {
                    productTable.clear();  // Clear existing row
                    productTable.draw(false);  // Redraw the table
                    return;
                }
                // invalid request 
                console.log(responseObject.result);
            },
            error: function (xhr, status, error) {
                // handle error
                console.log("error occured!");
            }
        });
    }

    createCartTable();

    $(productTable.table().body()).on('click', '.inc-button', function () {
        let quantity = parseInt($(this).nextAll('p').find('small').text());
        // 'this' refers to the clicked button
        let rowData = productTable.row($(this).parents('tr')).data(); // Get the row's data
        updateCart(rowData['id'], quantity + 1);
    });

    $(productTable.table().body()).on('click', '.sub-button', function () {
        let quantity = parseInt($(this).nextAll('p').find('small').text());
        // 'this' refers to the clicked button
        let rowData = productTable.row($(this).parents('tr')).data(); // Get the row's data
        updateCart(rowData['id'], quantity - 1);
    });

    $(productTable.table().body()).on('click', '.remove-button', function () {
        // 'this' refers to the clicked button
        let rowData = productTable.row($(this).parents('tr')).data(); // Get the row's data
        updateCart(rowData['id'], 0);
    });
});