<script>
    $(document).ready(function() {
        $(".navbar-wrapper").addClass("white-bg");
        var request = new isarray_request();
        
        request.init({
            url:"/index/index",
            type:"read",
            subType:"send-invoice",
            action:"member",
            id: getUrlParameter("user"),
            data: function(){
                var data = {
                    code: getUrlParameter("token")
                }
                return convert(JSON.stringify(data));
            },
            success:function(data) {
                if(data.status == true) {
                    $("#activationLoader").hide();
                    $("#activationComplete").fadeIn(1000);
                } else {
//                    $("#activationLoader").hide();
                    $("#activationComplete").fadeIn(1000);
                    $("#activationLoader").html('<div class="alert alert-danger">Oops, your email was not sent, please login and view invoice from your profile</div>');
                }
            }
        });
    });
</script>
<div class="page-header">
    <div class="container">
        <h1>Account Activation</h1>
        <p>The first step to becoming a Professional NDT person has now been completed</p>
    </div>
</div>
<div class="container general-info">
    <div class="alert alert-success" role="alert">
        <span style="font-size:20px; color:green; margin:0 5px -10px 0;" class="glyphicon glyphicon-ok-sign"></span>Your account has been enabled
    </div>
    <ul id="activationComplete" style="display:none;">
        <li>Your account has now been enabled.</li>
        <li>You can now log in using the user name and password received in the acknowledgment email.</li>
        <li>You must now complete the registration process by supplying the information requested and upload the documents required</li>
        <li>Payment of invoice to be done and proof of payment uploaded.</li>
        <li>Account will only be enabled for a further 30 days until all registration information has been received.</li>
        <li>On receipt of all registration documentation your application will be approved and you’re on your way to be registered as an Affiliate Member of the Professional Body.</li>
    </ul>
    <p>If you have any further queries with regards to the registration process please contact us via the <a href="/contact-us">Contact Us</a> page</p>
        <p id="activationLoader"><span class="fa fa-icon fa-2x fa-cog fa-spin"></span> Just a minute while we send you your invoice</p>
</div>