/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$(document).ready(function() {

    if($("#loginHolder").size() > 0) {
        var loginFormRequest = new isarray_request();
        
        loginFormRequest.init({
           url:"/authenticate/login-form",
           type:"form",
           success: function(data) {
                var login = new isarray_login(data, {register: false, forgotMyPassword: false});
                $("#loginHolder h1").after(login.loginForm());
                $("#loginSubmit").parent().addClass("form-group");
                $("#loginSubmit").parent().css("text-align", "center");
            }
        });
    }
    
    $("#loginHolder").on("click", "#loginSubmit", function() {
        var auth = new isarray_login();
        auth.login();
    });
    
    if($("#forgotMyPasswordPanel").size() > 0) {
        
        var forgotMyPasswordPanel = new isarray_request();
        
        forgotMyPasswordPanel.init({
           url:"/authenticate/index",
           type:"form",
           subType:"forgot-my-password",
           success: function(data) {
                var form = new isarray_forms({
                    modal: true,
                    draggable: true,
                    data: data,
                    formID: "forgotMyPasswordForm"
                });
                $("#forgotMyPasswordPanel").prepend(form.init());
                $("#changePasswordSubmit").parent().addClass("form-group");
                $("#changePasswordSubmit").parent().css("text-align", "center");
            }
        });
    }

    $("#forgotMyPasswordPanel").on("click", "#changePasswordSubmit", function() {
        var form = new isarray_forms();
        form.setValidator("#forgotMyPasswordForm");

        if($("#forgotMyPasswordForm").valid()) {
            
            var changePasswordRequest = new isarray_request();
            
            changePasswordRequest.init({
                url:"/authenticate/index",
                type:"update",
                elementID: "#forgotMyPasswordForm",
                subType:"forgot-my-password",
                loading: $("#changePasswordSubmit"),
                success: function(data) {
                    if(data.status == true) {
                        location.assign("/");
                    } else {
                        messages.element("#formMessages", data.message, "danger");
                    }
                }
            });
        }
    });
});
