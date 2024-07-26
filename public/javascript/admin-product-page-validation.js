$(function () {
    // added products ids and new quantity
    let productTable;
    let addedProducts = [];

    // $( ".form" ).on( "submit", function(e) {
    //     e.preventDefault();
    // } );

    function createProductTable() {
        productTable = $('#product-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "http://localhost/products/load-products/pagination",
                type: "POST",
                // data: function(d) {
                //     // Add custom data to the request
                //     d.customKey1 = 'customValue1';
                //     d.customKey2 = 'customValue2';
                // },
                error: function (xhr, error, thrown) {
                    // Handle the error here
                    console.error("Error during AJAX request:", error, thrown);
                },
            },
            columns: [
                {
                    title: "product id",
                    data: "id"
                },
                {
                    title: "product name",
                    data: "product_name"
                },
                {
                    title: "product price",
                    data: "price"
                },
                {
                    title: "add date",
                    data: "input_date"
                },
                {
                    title: "quantity",
                    data: "quantity"
                },
                {
                    render: function () {
                        return '<button type="button" class="btn btn-primary">add</button>';
                    }
                }
            ]
        });
    }

    createProductTable();

    let addedProductTable = new DataTable('#added-product-table',
        {
            columns: [
                {
                    title: "product id",
                    data: "id"
                },
                {
                    title: "product name",
                    data: "product_name"
                },
                {
                    title: "product price",
                    data: "price"
                },
                {
                    title: "quantity",
                    data: "quantity"
                },
                {
                    render: function () {
                        return `<div>
                    <button type="button" id="remove" class="btn btn-primary">remove</button>
                    <button type="button" id="+" class="btn btn-primary">+</button>
                    <button type="button" id="-" class="btn btn-primary">-</button>
                    </div>`;
                    }
                }
            ]
        }
    );

    // send updated products to server
    async function sendAddedProducts() {
        let response = await fetch('http://localhost/products',
            {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(addedProducts)
            }
        );
        let result = await response.json();
        if (result === "success") {
            window.location.href = "http://localhost/products";
        } else {
            console.log("failed");
        }
    }


    function updateAddedProducts() {
        addedProductTable.clear();  // Clear existing row
        addedProductTable.rows.add(addedProducts);  // Add new data
        addedProductTable.draw();  // Redraw the table
    }

    // upload new products to server as bulk
    async function uploadNewProducts() {
        const fileInput = document.getElementById('file-input');
        const products = fileInput.files[0];
        const formData = new FormData();
        formData.append('products', products);
        console.log("step 0");

        try {
            console.log("step 01");
            const response = await fetch('http://localhost/products/upload-products', {
                method: 'POST',
                body: formData,
            });
            console.log("step 02");
            const result = await response.json();
            console.log("step 03");
            document.getElementById('result').innerText = result;
            console.log("step 04");
        } catch (error) {
            document.getElementById('result').innerText = 'Upload failed.';
        }
    }


    $('#product-table').on('click', 'button', function () {
        const rowData = productTable.row($(this).parents('tr')).data();
        const rowProductId = rowData.id;

        console.log(rowProductId);
        // let isPorductAdded = false;

        // const checkProductInclude = (_products, _productId) => {
        //     return _products.some((product) => {
        //         return product.id === _productId;
        //     });
        // }

        // isPorductAdded = checkProductInclude(addedProducts,rowProductId);

        // if (!isPorductAdded) {
        //     addedProducts.push(rowData);
        //     updateAddedProducts();
        // }
    });

    $('#added-product-table').on('click', 'button', function () {
        console.log(products);
        const $currentButton = $(this);

        const rowData = addedProductTable.row($(this).parents('tr')).data();
        const rowProductId = rowData.id;

        if ($currentButton.attr('id') === "remove") {
            let filteredAddedProducts;

            filteredAddedProducts = addedProducts.filter((product) => {
                return product.id !== rowProductId;
            });

            addedProducts = filteredAddedProducts;
            updateAddedProducts();
        } else if ($currentButton.attr('id') === "+") {
            // let changedAddedProducts;
            let matchedKey = null;

            // changedAddedProducts = addedProducts.map((product)=>{
            //     if(rowProductId === product.id){
            //         product.quantity = product.quantity + 1;
            //         return product;
            //     }

            //     return product;
            // });

            addedProducts.forEach((product, key) => {
                if (product.id === rowProductId) {
                    matchedKey = key;
                }
            });

            addedProducts[matchedKey].quantity = addedProducts[matchedKey].quantity + 1;
            // updateAddedProducts();
        } else if ($currentButton.attr('id') === "-") {
            // let changedAddedProducts;
            let matchedKey = null;

            // changedAddedProducts = addedProducts.map((product)=>{
            //     if(rowProductId === product.id){
            //         product.quantity = product.quantity - 1;
            //         return product;
            //     }

            //     return product;
            // });

            addedProducts.forEach((product, key) => {
                if (product.id === rowProductId) {
                    matchedKey = key;
                }
            });

            addedProducts[matchedKey].quantity = addedProducts[matchedKey].quantity - 1;
            updateAddedProducts();
        }

    });


    $('#update-button').on('click', function () {
        sendAddedProducts();
    });

    $('#product-upload').on('click', function () {
        uploadNewProducts();
    });


    $(".js-example-basic-single").select2({
        closeOnSelect: false
    });
    $(".js-example-basic-multiple").select2({
        closeOnSelect: false
    });

    $(".enable").on("click", function () {
        $(".js-example-basic-single").prop("disabled", false);
        $(".js-example-basic-multiple").prop("disabled", false);
    });

    $(".disable").on("click", function () {
        $(".js-example-basic-single").prop("disabled", true);
        $(".js-example-basic-multiple").prop("disabled", true);
    });

    $("#filter-button").on("click", function () {
        productTable.destroy();
        createProductTable();
    });



    // send post requset to /testing to send data of updated product qunatity
    // async function getResponse(){
    //     const data = {id:true,quantity:'3'};
    //     let response = await fetch('http://localhost/testing',
    //         {
    //             method: 'POST',
    //             headers: {
    //                 'Accept': 'application/json',
    //                 'Content-Type': 'application/json'
    //             },
    //             body: JSON.stringify(data)
    //         }
    //     );
    //     let result = await response.json();
    //     console.log(result);
    // }

    // getResponse();







    // const startingValue = parseInt($("p.add-quantity").text());



    // $('.form').submit(function(event) {
    //     event.preventDefault(); // Prevents default form submission

    //     // Serialize form data
    //     var formData = $(this).serialize();

    //     // Perform AJAX request to send data to PHP script
    //     $.ajax({
    //         url: 'http://localhost/testing', // Replace with your PHP script file path
    //         method: 'POST',
    //         data: "hello", // Form data to send
    //         success: function(response) {
    //             console.log('Form submitted successfully:', response);
    //             // No client-side redirection here; let PHP handle it
    //         },
    //         error: function(xhr, status, error) {
    //             console.error('Error submitting form:', error);
    //             // Handle errors
    //         }
    //     });
    // });

    // Function to manually trigger form submission
    // function triggerFormSubmit() {
    //     $('.form').submit(); // Remove existing submit handler and submit form
    // }

    // Example: Call triggerFormSubmit() somewhere in your code
    // Example usage:
    // $('#test-button').click(function() {
    //     // redirect browser to another page and there will no url to go back 
    //     // window.location.href("http://localhost/homepage");
    //     window.location.href = "http://localhost/homepage";
    // });



    // $('#div-list').on('click', '.subtract-input', function() {
    //     // Find the corresponding paragraph element
    //     let $paragraph = $(this).siblings('.add-quantity');
    //     let $paragraphValue = parseInt($paragraph.text());

    //     if(!$paragraph.data("initialValue")){
    //         $paragraph.data( "initialValue", $paragraphValue);
    //     }

    //     $paragraph.text($paragraphValue - 1);

    //     if(parseInt($paragraph.text())<$paragraph.data("initialValue")){
    //         $paragraph.text($paragraph.data("initialValue")); 
    //     }
    // });

    // $('#div-list').on('click', '.add-input', function() {
    //     // Find the corresponding paragraph element
    //     let $paragraph = $(this).siblings('.add-quantity');
    //     let $paragraphValue = parseInt($paragraph.text());

    //     if(!$paragraph.data("initialValue")){
    //         $paragraph.data( "initialValue", $paragraphValue);
    //     }

    //     $paragraph.text($paragraphValue + 1);
    // });

    // $('#div-list').on('click', '#submit-button', function() {
    //     // Find the corresponding paragraph element
    //     let $paragraph = $(this).siblings('.add-quantity');
    //     let $paragraphValue = $paragraph.text();

    //     alert($paragraphValue);
    // });

});


