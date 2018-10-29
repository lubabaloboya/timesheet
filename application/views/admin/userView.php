<script>
$(document).ready(function() {
    var generalTab = new isarray_tabs();
    //Update Item
    $(".panel").off("click", ".edit-user-details");
    $(".panel").on("click", ".edit-user-details", function(e) {
        var uniqueID = generalTab.getActiveTab().attr("id").replace("tab-", "");
        var request = new isarray_request();
        
        if(!$(e.currentTarget).hasClass("disabled")) {
            request.init({
                type:"form",
                subType:"edit",
                url:"/admin/index",
                action: "user",
                id: $("#tab-"+uniqueID).attr("data-value"),
                uniqueID: uniqueID,
                loading:true,
                success: function(data) {
                    var form = new isarray_forms({
                        title:"Edit User",
                        formID:"editUserForm",
                        modal:true,
                        draggable:true,
                        data:data
                    });
                    form.renderModalForm(updateUserDetails, data.uniqueID);
                }
            });
        }
    });
    
    $("body").off("change", "#userRoleID");
    $("body").on("change", "#userRoleID", function() {
        var value = $("#userRoleID").val();
        if(value == 4){
            $("#editUserForm .form-group").removeClass("hide");
        }else{
            $("#editUserForm .expatriate").addClass("hide");
        }
    });

});

function updateUserDetails(uniqueID) {
    var form = new isarray_forms();
    form.setValidator("#editUserForm");
    
    if($("#editUserForm").valid()) {
        var request = new isarray_request();
        request.init({
            url:"/admin/index",
            type:"update",
            action:"user",
            elementID: "#editUserForm",
            id: $("#tab-"+uniqueID).attr("data-value"),
            uniqueID: uniqueID,
            loading:$(".form-btn"),
            success: function(data) {
                if(data.status == true) {
                    $(".modal-header button.close").trigger("click");
                    $("[data-title='users']").removeClass("loaded");
                    messages.applicationMessage(data.message, "success");
                    getPanelDetails("/admin/user-details", "#userDetails", data.uniqueID);
                } else {
                    messages.element("#formMessages", data.message, "danger");
                }
            }
        });
    }
}
</script>
<div class="container-fluid" id="innerView">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <div  class="panel panel-primary">
                <div class="panel-heading">User Details</div>
                <ul class="list-group panel-slider" id="userDetails">
                    
                </ul>
                <div class="panel-footer">

                </div>               
            </div>
        </div>
    </div>
</div>

