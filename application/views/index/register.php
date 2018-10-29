<script>
$(document).ready(function() {
    $(".navbar-wrapper").addClass("white-bg");
    var request = new isarray_request();
    
    request.init({
        type:"form",
        subType:"register-user",
        url:"/index/index",
        action: "user",
        id: $(".nav-tabs li.active").attr("data-value"),
        loading:true,
        success: function(data) {
            request.completed();
            var form = new isarray_forms({
                title:"Register",
                formID:"registerUserForm",
                data:data
            });
            $(".general-info").html(form.init());
        }
    });
    
    $(".general-info").off("click", "#registersUserSubmit");
    $(".general-info").on("click", "#registersUserSubmit", function() {
        registerUser();
    });
});

function registerUser() {
    var form = new isarray_forms();
    form.setValidator("#registerUserForm");
    var request = new isarray_request();
    
    if($("#registerUserForm").valid()) {
        request.init({
            url:"/index/index",
            type:"create",
            subType:"register-user",
            action:"user",
            elementID: "#registerUserForm",
            id: $(".nav-tabs li.active").attr("data-value"),
            loading:loader.page(".general-info"),
            success: function(data) {
                if(data.status == true) {
                    $("#registerUserForm *").each(function() {
                        $(this).val("");
                    });
                    messages.element("#formMessages", data.message, "success");
                } else {
                    
                    messages.element("#formMessages", data.message, "danger");
                }
            }
        });
    }
}


</script>

<div class="page-header">
    <div class="container">
        <h1>Register</h1>
        <p></p>
    </div>
</div>
<div class="container general-info">
    
</div>
