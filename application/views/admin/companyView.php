<script>
$(document).ready(function() {
    var generalTab = new isarray_tabs();
    //Update Item
    $(".panel").off("click", ".edit-company-details");
    $(".panel").on("click", ".edit-company-details", function(e) {
        var uniqueID = generalTab.getActiveTab().attr("id").replace("tab-", "");
        var request = new isarray_request();
        
        if(!$(e.currentTarget).hasClass("disabled")) {
            request.init({
                type:"form",
                subType:"edit",
                url:"/admin/index",
                action: "company",
                id: $("#tab-"+uniqueID).attr("data-value"),
                uniqueID: uniqueID,
                loading:true,
                success: function(data) {
                    var form = new isarray_forms({
                        title:"Edit Company",
                        formID:"editCompanyForm",
                        modal:true,
                        draggable:true,
                        data:data
                    });
                    form.renderModalForm(updateCompanyDetails, data.uniqueID);
                }
            });
        }
    });
});

function updateCompanyDetails(uniqueID) {
    var form = new isarray_forms();
    form.setValidator("#editCompanyForm");
    
    if($("#editCompanyForm").valid()) {
        var request = new isarray_request();
        request.init({
            url:"/admin/index",
            type:"update",
            action:"company",
            elementID: "#editCompanyForm",
            id: $("#tab-"+uniqueID).attr("data-value"),
            uniqueID: uniqueID,
            loading:$(".form-btn"),
            success: function(data) {
                if(data.status == true) {
                    $(".modal-header button.close").trigger("click");
                    $("[data-title='Companies']").removeClass("loaded");
                    messages.applicationMessage(data.message, "success");
                    getPanelDetails("/admin/company-details", "#companyDetails", data.uniqueID);
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
                <div class="panel-heading">Company Details</div>
                <ul class="list-group panel-slider" id="companyDetails">
                    
                </ul>
                <div class="panel-footer">

                </div>               
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <div  class="panel panel-primary">
                <div class="panel-heading">Company Documents</div>
                <div class="panel-slider" id="companyDocuments">

                </div>
                <div class="panel-footer">

                </div>               
            </div>
        </div>
        
    </div>
</div>

