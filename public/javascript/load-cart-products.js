$(function () {

    // fetch products from cart
    async function getProductsInCart() {
        const response = await fetch('http://localhost/orders/load-orders', {
            method: 'POST',
        });
        if (!response.ok) {
            throw new Error(`Failed to fetch products in cart - ${response.statusText}`);
        }

        return await response.json();
    };
   
    function loadCartProductsIntoDiv(){
        $products = getProductsInCart();
        $("#cart").html(result);
    }

    loadCartProductsIntoDiv();
});