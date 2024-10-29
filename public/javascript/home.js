$(function () {
    function setInitialStatus(){
        $.ajax({
            url: "http://localhost/auth/status", // URL of the server-side script
            type: 'POST',              // 'POST' for sending data
            success: function (response) {
                let responseObject = JSON.parse(response);
                if (!responseObject.isGuest) {
                    $( ".div-banner" ).append( 
                        `
                            <a class="nav-link link-max" href="/products/add-products">Explore <span>products</span></a>
                        `
                    );
                    $('#div-auth').remove();
                    $('#div-link li:nth-child(2)').remove();
                    let links = [["/products/add-products","Products"],["/cart","Cart"],["/orders","Orders"]];
                    links.forEach(link => {
                        $( "#div-link" ).append( 
                            `
                                <li class="nav-item">
                                    <a class="nav-link ps-0 mr-70" href="${link[0]}">${link[1]}</a>
                                </li>
                            `
                        );
                    });
                    if(responseObject.userType === "admin" || responseObject.userType === "seller"){
                        $( "#div-link" ).append( 
                            `
                                <li class="nav-item">
                                    <a class="nav-link ps-0 mr-70" href="/manage/products">Manage</a>
                                </li>
                            `
                        );
                    }

                    return;
                }
                $( ".div-banner" ).append( 
                    `
                        <a class="nav-link link-max" href="/login">Sign <span>in now</span></a>
                    `
                );
            },
            error: function (xhr, status, error) {
                // handle error
                console.log("error occured!");
            }
        });
    }

    setInitialStatus();
});
