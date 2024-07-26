$(function() {
    let orders = null;

    let orderTable = new DataTable('#order-table',
        {
            columns: [
                {   title: "order id",
                    data: "order_id" 
                },
                {   title: "date",
                    data: "order_date" 
                },
                {   title: "payment method",
                    data: "payment_method" 
                },
                {   title: "status",
                    data: "order_state"
                },
                {   title: "products",
                    data: "products",
                    render: function(products){
                        let productsHtml = "";
                        products.forEach(product => {
                            productsHtml += `<p>${product.product_name}</p>
                            <p>${product.product_price}</p>
                            <p>${product.product_quantity}</p><br>`;
                        });
                        return `
                            <div>
                                ${productsHtml}
                            </div>
                        `;
                    }
                },
                {   
                    render: function() {
                    return '<button type="button" class="btn btn-primary">download slip</button>';
                    }
                }
            ]
        }
    );

    // 
    function updateOrders(){
        orderTable.clear();  // Clear existing row
        orderTable.rows.add(orders);  // Add new data
        orderTable.draw();  // Redraw the table
    }

    // fetch orders
    async function getOrders() {
        const response = await fetch('http://localhost/orders/load-orders', {
          method: 'GET',
        });
        if (!response.ok) {
          throw new Error(`Failed to fetch orders - ${response.statusText}`);
        }

        const result = await response.json();

        if(typeof result === "object"){
            orders = Object.values(result);
            updateOrders();
        }else if(result === "User has No orders"){
            $('#status-p').text("User has No orders");
        }else if(result === "Failed to Load orders"){
            console.log("Failed to Load orders");
        // for the response having value "error"  which sends for unauthneticated requets
        }else{
            return null;
        }
    };

    getOrders();
      
    // get order slip as pdf 
    async function getOrderSlip(orderId){
        const response = await fetch('http://localhost/orders/get-order-slip', 
            {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({"order-id":orderId})
            }
        );
          if (!response.ok) {
            throw new Error(`Failed to fetch products pdf - ${response.statusText}`);
          }

          const blob = await response.blob();
          if(!(blob.type === "application/pdf")){
            console.log("failed");
            return null;
          }
        
          // const text = await blob.text();
          // console.log(blob.type);
          const objectUrl = URL.createObjectURL(blob);
          const link = document.createElement("a");
          link.href = objectUrl;
          link.download = 'slip.pdf';
          link.click();
          URL.revokeObjectURL(objectUrl);
    }

    $('#order-table').on('click','button',function(){
        let rowData = orderTable.row($(this).parents('tr')).data();
        getOrderSlip(rowData.order_id);
    });

});