$(function() {
    // initial products get from db
    let products = null;
    // added products ids and new quantity
    let addedProducts = [];

    // $( ".form" ).on( "submit", function(e) {
    //     e.preventDefault();
    // } );


    let productTable = new DataTable('#product-table',
        {
            columns: [
                {   title: "product id",
                    data: 0 
                },
                {   title: "product name",
                    data: 1 
                },
                {   title: "product price",
                    data: 2 
                },
                {   title: "quantity",
                    data: 5 
                },
                {   
                    render: function() {
                    return '<button type="button" class="btn btn-primary">add</button>';
                    }
                }
            ]
        }
    );

    let addedProductTable = new DataTable('#added-product-table',
        {
            columns: [
                {   title: "product id",
                    data: 0 
                },
                {   title: "product name",
                    data: 1 
                },
                {   title: "product price",
                    data: 2 
                },
                {   title: "quantity",
                    data: 5 
                },
                {   
                    render: function() {
                    return `<div>
                    <button type="button" class="btn btn-primary">remove</button>
                    <button type="button" class="btn btn-primary">+</button>
                    <button type="button" class="btn btn-primary">-</button>
                    </div>`;
                    }
                }
            ]
        }
    );

    // send updated products to server
    async function sendAddedProducts(){
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
        if(result === "success"){
            window.location.href = "http://localhost/products";
        }else{
            console.log("failed");
        }
    }
    

    function updateAddedProducts(){
        addedProductTable.clear();  // Clear existing row
        addedProductTable.rows.add(addedProducts);  // Add new data
        addedProductTable.draw();  // Redraw the table
    }

    function populateProductTable(){
        // table.clear();  // Clear existing rows
        productTable.rows.add(products);  // Add new data
        productTable.draw();  // Redraw the table
    }

    //get products data
    async function getProducts(){
        let response = await fetch('http://localhost/products/load-products');
        let result = await response.json();
        products = result;
        populateProductTable();
    }

    getProducts();


    $('#product-table').on('click','button',function(){
        let rowData = productTable.row($(this).parents('tr')).data();
        let checker = false;
        addedProducts.forEach((product)=>{
            if(product[0] === rowData[0]){
                checker = true;
            }
        });
        
        if(!checker){
            addedProducts.push(rowData);
            updateAddedProducts();
        }
        
    });

    $('#added-product-table').on('click','button',function(){
        let $currentButton = $(this);

        if($currentButton.text() === "remove"){
            let rowData = addedProductTable.row($(this).parents('tr')).data();
            let productKey = null;
            
            addedProducts.forEach((product, key)=>{
                if(product[0] === rowData[0]){
                    productKey = key;
                }
            });

            addedProducts.splice(productKey, 1);
            updateAddedProducts();
        }else if($currentButton.text() === "+"){
            let rowData = addedProductTable.row($(this).parents('tr')).data();
            let productKey = null;
            
            addedProducts.forEach((product, key)=>{
                if(product[0] === rowData[0]){
                    productKey = key;
                }
            });

            let newQunatity = addedProducts[productKey][5] + 1;
            addedProducts[productKey][5] = newQunatity;
            updateAddedProducts();
        }else if($currentButton.text() === "-"){
            let rowData = addedProductTable.row($(this).parents('tr')).data();
            let productKey = null;
            
            addedProducts.forEach((product, key)=>{
                if(product[0] === rowData[0]){
                    productKey = key;
                }
            });

            let newQunatity = addedProducts[productKey][5] - 1;
            addedProducts[productKey][5] = newQunatity;
            updateAddedProducts();
        }
        
    });

    
    $('#update-button').on('click',function(){
        sendAddedProducts();
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
    $('#test-button').click(function() {
        // redirect browser to another page and there will no url to go back 
        // window.location.href("http://localhost/homepage");
        window.location.href = "http://localhost/homepage";
    });

    

    $('#div-list').on('click', '.subtract-input', function() {
        // Find the corresponding paragraph element
        let $paragraph = $(this).siblings('.add-quantity');
        let $paragraphValue = parseInt($paragraph.text());

        if(!$paragraph.data("initialValue")){
            $paragraph.data( "initialValue", $paragraphValue);
        }

        $paragraph.text($paragraphValue - 1);

        if(parseInt($paragraph.text())<$paragraph.data("initialValue")){
            $paragraph.text($paragraph.data("initialValue")); 
        }
    });

    $('#div-list').on('click', '.add-input', function() {
        // Find the corresponding paragraph element
        let $paragraph = $(this).siblings('.add-quantity');
        let $paragraphValue = parseInt($paragraph.text());

        if(!$paragraph.data("initialValue")){
            $paragraph.data( "initialValue", $paragraphValue);
        }
        
        $paragraph.text($paragraphValue + 1);
    });

    $('#div-list').on('click', '#submit-button', function() {
        // Find the corresponding paragraph element
        let $paragraph = $(this).siblings('.add-quantity');
        let $paragraphValue = $paragraph.text();

        alert($paragraphValue);
    });

});


