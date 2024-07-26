$(function () {
    let products = null;

    function loadProducts() {
        $("#div-products").append("<ul id='product-list'></ul>");
        products.forEach(product => {
            $('#product-list').append(
                `
                <div class='product-list'>
                    <ul>
                        <div class='card'>
                            <img src='http://localhost/public/assets/images/${product.link}' alt=${product.id}>                        
                            <div class='container'>
                                <h5><b>${product.product_name}</b></h5> 
                                <p>Rs. ${product.price}</p> 
                            </div>               
                            <button class='submit-button' id='${product.id}'>add to cart</button>
                        </div>
                    </ul>
                </div>
                
                `
            );
        });
    }

    // send a product to server to add cart
    async function sendProductToCart(productId) {
        const data = { id: productId };
        let response = await fetch('http://localhost/products',
            {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            }
        );
        let result = await response.json();
        if (result === "success") {
            console.log("product added");
        } else {
            console.log("couldn't add product");
        }
    }


    // Select all elements with the class 'myButton'
    // var elements = $('.myButton');

    // Filter to get only button elements
    // var buttons = elements.filter('button');
    $("button").on("click", () => {
        const $currentButton = $(this);
        const productId = $currentButton.attr('id');
        sendProductToCart(productId);
    });

    //get products data
    // ->
    async function fetchProducts() {

        const url = 'http://localhost/products/load-products';

        try {
            const response = await fetch(url);

            if (!response.ok) {
                throw new Error(`Response status: ${response.status}`);
            }

            // need to handle empty respocnse
            let result = await response.json();
            products = result;
            loadProducts();
            console.log();

        } catch (error) {
            $('#update-button').text("couldn't load products.");
        }
    }

    fetchProducts();
});

