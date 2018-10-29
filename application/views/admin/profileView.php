<script>
$(document).ready(function() {
    var generalTab = new isarray_tabs();
    
   //Update User Profile details
    $(".panel").off("click", ".edit-profile-details");
    $(".panel").on("click", ".edit-profile-details", function(e) {
        var uniqueID = generalTab.getActiveTab().attr("id").replace("tab-", "");
        var request = new isarray_request();
        
        if(!$(e.currentTarget).hasClass("disabled")) {
            request.init({
                type:"form",
                subType:"profile-details",
                url:"/admin/profile-index",
                action: "profile",
                uniqueID: uniqueID,
                loading:true,
                success: function(data) {
                    var form = new isarray_forms({
                        title:"Edit Profile",
                        formID:"updateProfileDetailsForm",
                        modal:true,
                        draggable:true,
                        data:data
                    });
                    form.renderModalForm(updateProfileDetails, uniqueID);
                }
            });
        }
    });
});

function updateProfileDetails(uniqueID) {
    var form = new isarray_forms();
    form.setValidator("#updateProfileDetailsForm");
    var request = new isarray_request();
    
    if($("#updateProfileDetailsForm").valid()) {
        request.init({
            url:"/admin/profile-index",
            type:"update",
            subType:"profile-details",
            action:"profile",
            elementID:"#updateProfileDetailsForm",
            uniqueID: uniqueID,
            loading:$(".form-btn"),
            success: function(data) {
                if(data.status == true) {
                    $(".modal-header button.close").trigger("click");
                    messages.applicationMessage(data.message, "success");
                    getPanelDetails("/admin/profile-details", "#profileDetails", data.uniqueID);
                } else {
                    messages.element("#formMessages", data.message, "danger");
                }
            }
        });
    }
}

</script>
<div class="container-fluid inner-view">
    <div class="row">
        <div class="col-sm-12 col-md-6">
            <div class="panel panel-primary inner-view-panel">
                <div class="panel-heading">Your Details</div>
                <ul class="list-group panel-slider" id="profileDetails">
                </ul>
                <div class="panel-footer">
                </div>
            </div>
        </div>
        <div class="col-sm-12 col-md-6">
        </div>
    </div>    
</div>