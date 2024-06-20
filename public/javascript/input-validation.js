$(function() {
    function validateEmail(){
        const value = $("#input-email").val();
        const reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
        if(value.length === 0){
            $("#email-check").html("Email should be filled!");
            return false;
        }else if(!(reg.test(value))){
            $("#email-check").html("Email format is invalid!");
            return false;
        }else if(30<value.length){
            $("#emial-check").html("Invalid Email!");
            return false;
        }else{
            $("#email-check").html("");
            return true;
        }
        
    }

    function validatePassword(){
        const value = $("#input-password").val();
        if(value.length === 0){
            $("#password-check").html("Password should be filled!");
            return false;
        }else{
            $("#password-check").html("");
            return true;
        }
    }

    function validateUserName(){
        const value = $("#input-username").val();
        const reg = /^[A-Za-z]+$/;
        if(value.length === 0){
            $("#username-check").html("Username should be filled!");
            return false;
        }else if(!(reg.test(value))){
            $("#username-check").html("Invalid username!");
            return false;
        }else if(20<value.length){
            $("#username-check").html("Invalid username!");
            return false;
        }else{
            $("#username-check").html("");
            return true;
        }
    }
    
    $( "#input-email" ).on( "keyup", function() {
        validateEmail();
    } );
    $( "#input-password" ).on( "keyup", function() {
        validatePassword();
    } );
    $( "#input-username" ).on( "keyup", function() {
        validateUserName();
    } );


    // $("#register-button").on("click", function(){
    //     let isValidEmail = validateEmail();
    //     let isValidUserName = validateUserName();
    //     let isValidPassword = validatePassword();
        
    //     if(isValidEmail && isValidUserName && isValidPassword){

    //     }
    // });

});