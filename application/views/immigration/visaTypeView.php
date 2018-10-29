<script>
$(document).ready(function() {
    var generalTab = new isarray_tabs();
    //Update Item
    $(".panel").off("click", ".edit-visa-type-details");
    $(".panel").on("click", ".edit-visa-type-details", function(e) {
        var uniqueID = generalTab.getActiveTab().attr("id").replace("tab-", "");
        var request = new isarray_request();
        
        if(!$(e.currentTarget).hasClass("disabled")) {
            request.init({
                type:"form",
                subType:"edit",
                url:"/immigration/index",
                action: "visaType",
                id: $("#tab-"+uniqueID).attr("data-value"),
                uniqueID: uniqueID,
                loading:true,
                success: function(data) {
                    var form = new isarray_forms({
                        title:"Edit Visa",
                        formID:"editVisaTypeForm",
                        modal:true,
                        draggable:true,
                        data:data
                    });
                    form.renderModalForm(updateVisaTypeDetails, data.uniqueID);
                }
            });
        }
    });
    
        
    
});

function updateVisaTypeDetails(uniqueID) {
    var form = new isarray_forms();
    form.setValidator("#editVisaTypeForm");
    
    if($("#editVisaTypeForm").valid()) {
        var request = new isarray_request();
        request.init({
            url:"/immigration/index",
            type:"update",
            action:"visaType",
            elementID: "#editVisaTypeForm",
            id: $("#tab-"+uniqueID).attr("data-value"),
            uniqueID: uniqueID,
            loading:$(".form-btn"),
            success: function(data) {
                if(data.status == true) {
                    $(".modal-header button.close").trigger("click");
                    $("[data-title='visas']").removeClass("loaded");
                    messages.applicationMessage(data.message, "success");
                    getPanelDetails("/immigration/visa-type-details", "#visaTypeDetails", data.uniqueID);
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
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <div  class="panel panel-primary inner-view-panel">
                <div class="panel-heading">Visa Type Details</div>
                <ul class="list-group panel-slider" id="visaTypeDetails">
                    
                </ul>
                <div class="panel-footer">

                </div>               
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <div  class="panel panel-primary inner-view-panel">
                <div class="panel-heading">Visa Documentation Types</div>
                <div class="panel-slider" id="visaDocumentationTypes">
                    
                </div>
                <div class="panel-footer">

                </div>               
            </div>
        </div>
    </div>
</div>