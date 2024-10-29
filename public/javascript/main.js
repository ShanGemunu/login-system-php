$(function () {
    function setInitialStatus(){
        $.ajax({
            url: "http://localhost/auth/status", // URL of the server-side script
            type: 'POST',              // 'POST' for sending data
            success: function (response) {
                let responseObject = JSON.parse(response);
                if (!responseObject.isGuest) {
                    if(responseObject.userType === "admin" || responseObject.userType === "seller"){
                        $( "div.div-link" ).append( 
                            `
                                <li class="nav-item">
                                    <a class="nav-link ps-0 mr-70" href="/manage/products">Manage</a>
                                </li>
                            `
                        );
                    }

                    return;
                }
            },
            error: function (xhr, status, error) {
                // handle error
                console.log("error occured!");
            }
        });
    }

    function triggerLogout() {
        $.ajax({
            url: "http://localhost/logout", // URL of the server-side script
            type: 'POST',              // 'POST' for sending data
            success: function (response) {
                window.location.replace("http://localhost/");
             },
            error: function (xhr, status, error) {
                // handle error
                console.log("error occured!");
            }
        });
    }

    setInitialStatus();

    $(".button-log-out").on("click", function () {
        triggerLogout();
    });
});
