<script>
$(document).ready(function() {
    var generalTab = new isarray_tabs();
    //Update Item
    $(".panel").off("click", ".edit-expatriate-details");
    $(".panel").on("click", ".edit-expatriate-details", function(e) {
        var uniqueID = generalTab.getActiveTab().attr("id").replace("tab-", "");
        var request = new isarray_request();
 
        if(!$(e.currentTarget).hasClass("disabled")) {
            request.init({
                type:"form",
                subType:"edit",
                url:"/admin/index",
                action: "expatriate",
                id: $("#tab-"+uniqueID).attr("data-value"),
                uniqueID: uniqueID,
                loading:true,
                success: function(data) {
                    var form = new isarray_forms({
                        title:"Edit Expatriate",
                        formID:"editExpatriateForm",
                        modal:true,
                        draggable:true,
                        data:data
                    });
                    form.renderModalForm(updateUserDetails, data.uniqueID);
                }
            });
        }
    });

    $("body").off("click", "#expatriateJobTitle");
    $("body").on("click", "#expatriateJobTitle", function() {

        var select = $("#expatriateJobTitle");
        var selected = select.val();
        var options_list = select.find('option');

        options_list.sort(function(a, b) {
            return $(a).text() > $(b).text() ? 1 : -1; 
        });

        select.html('').append(options_list);
        select.val(selected); 

    });

});

function updateUserDetails(uniqueID) {
    var form = new isarray_forms();
    form.setValidator("#editExpatriateForm");
    
    if($("#editExpatriateForm").valid()) {
        var request = new isarray_request();
        request.init({
            url:"/admin/index",
            type:"update",
            action:"expatriate",
            elementID: "#editExpatriateForm",
            id: $("#tab-"+uniqueID).attr("data-value"),
            uniqueID: uniqueID,
            loading:$(".form-btn"),
            success: function(data) {
                if(data.status == true) {
                    $(".modal-header button.close").trigger("click");
                    $("[data-title='expatriates']").removeClass("loaded");
                    messages.applicationMessage(data.message, "success");
                    getPanelDetails("/admin/expatriate-details", "#expatriateDetails", data.uniqueID);
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
                <div class="panel-heading">Expatriate Details</div>
                <ul class="list-group panel-slider" id="expatriateDetails">
                    
                </ul>
                <div class="panel-footer">

                </div>               
            </div>
        </div>
    </div>
</div>
