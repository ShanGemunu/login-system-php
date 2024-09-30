$(function () {

    // fetch products from cart
    function createProductTable() {
        productTable = new DataTable("#product-table", {
            initComplete: function() {
                // Remove the specified classes from the target <th> element
                $('th[data-dt-column="7"]').removeClass('dt-orderable-asc dt-orderable-desc dt-type-numeric');
                $('th[data-dt-column="8"]').removeClass('dt-orderable-asc dt-orderable-desc dt-type-numeric');
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
                { data: 'id' },
                { data: 'input_date' },
                { data: 'link' },
                { data: 'price' },
                { data: 'product_name' },
                { data: 'quantity' },
                {
                    data: 'editPopup',
                    defaultContent: '<button><i class="fa fa-pencil"/> edit</button>',
                    orderable: false
                },
                {
                    data: null,
                    className: 'dt-center editor-edit',
                    defaultContent: '<button><i class="fa fa-pencil"/> delete</button>',
                    orderable: false
                },
            ]
        });
    }

    createProductTable();

    $("#filter-button").on("click", function () {
        productTable.destroy();
        createProductTable();
    });

    // const data = [
    //     {
    //         "name":       "Tiger Nixon",
    //         "position":   "System Architect",
    //         "salary":     "$3,120",
    //         "start_date": "2011/04/25",
    //         "office":     "Edinburgh",
    //         "extn":       "5421"
    //     },
    //     {
    //         "name":       "gemunu",
    //         "position":   "Director",
    //         "salary":     "$5,300",
    //         "start_date": "2011/07/25",
    //         "office":     "Edinburgh",
    //         "extn":       "8422"
    //     }
    // ];

    // $('#example').DataTable( {
    //     initComplete: function(settings, json) {
    //         // Remove the specified classes from the target <th> element
    //         $('th[data-dt-column="6"]').removeClass('dt-orderable-asc dt-orderable-desc dt-type-numeric');
    //         $('th[data-dt-column="7"]').removeClass('dt-orderable-asc dt-orderable-desc dt-type-numeric');
    //     },
    //     data: data,
    //     columns: [
    //         { data: 'name' },
    //         { data: 'position' },
    //         { data: 'salary' },
    //         { data: 'office' },
    //         {
    //             data: 'editPopup',
    //             defaultContent: '<button><i class="fa fa-pencil"/> edit</button>',
    //             orderable: false
    //         },
    //         {
    //             data: null,
    //             defaultContent: '<button><i class="fa fa-pencil"/> delete</button>',
    //             orderable: false
    //         },
    //     ]
    // } );

});