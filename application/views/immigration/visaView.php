<script>

var visas = function() {
    
    var self = this;
    
    self.generalTab = new isarray_tabs();
    self.uniqueID = this.generalTab.getActiveTab().attr("id").replace("tab-", "");
    self.request = new isarray_request();
    
    self.action = "visa";
    self.load = $(".form-btn");
    self.form = "#editVisaForm";
    self.url = "/immigration/index";
    
    self.getEditForm = function(e) {
        if(!$(e.currentTarget).hasClass("disabled")) {
            self.request.init({
                type:"form",
                subType:"edit",
                url:self.url,
                action: "visa",
                id: $("#tab-"+self.uniqueID).attr("data-value"),
                uniqueID: self.uniqueID,
                loading:$(".form-btn"),
                success: function(data) {
                    var form = new isarray_forms({
                        title:"Edit Visa",
                        formID:self.form.replace("#", ""),
                        modal:true,
                        draggable:true,
                        data:data
                    });
                    form.renderModalForm(self.updateVisaDetails, data.uniqueID);
                            
                    //Update validation on status seletion
                    $("body").off("click", "#visaStatus");
                    $("body").on("click", "#visaStatus", function() {
                        var val = $(this).val();
                        
                        if(val == 8) {
                            $("#visaDateSubmitted").removeAttr("disabled").addClass("required");
                            $("#visaDateDeclined").attr("disabled", true).removeClass("required");
                        } else if(val == 10) {
                            $("#visaDateSubmitted").attr("disabled", true).removeClass("required");
                            $("#visaDateDeclined").removeAttr("disabled").addClass("required");
                        } else if(val == 11) {
                            $("#editVisaForm .form-group").removeClass("hide");
                        } else {
                            $("#visaDateSubmitted").attr("disabled", true).removeClass("required");
                            $("#visaDateDeclined").attr("disabled", true).removeClass("required");
                            $("#visaDateOnhold").removeClass("required");
                            $("#editVisaForm .onholdVisa").addClass("hide");
                        }
                    });
                }
            });      
        }
     }
    
    self.addVisaCommentForm = function(e) {
        if(!$(e.currentTarget).hasClass("disabled")) {
            self.request.init({
                type:"form",
                url:self.url,
                action: "visaComment",
                id: $("#tab-"+self.uniqueID).attr("data-value"),
                uniqueID: self.uniqueID,
                loading:$(".form-btn"),
                success: function(data) {
                    var form = new isarray_forms({
                        title:"Add Visa Comment",
                        formID:"addVisaCommentForm",
                        modal:true,
                        draggable:true,
                        data:data
                    });
                    form.renderModalForm(self.addVisaComment, data.uniqueID);
                }
            });
        }
    }
    
    
    self.editVisaCommentForm = function(e) {
        if(!$(e.currentTarget).hasClass("disabled")) {
            self.request.init({
                type:"form",
                subType:"edit",
                url:self.url,
                action: "visaComment",
                id: $(e.currentTarget).attr("data-value"),
                uniqueID: self.uniqueID,
                loading:$(".form-btn"),
                success: function(data) {
                    var form = new isarray_forms({
                        title:"Edit Visa Comment",
                        formID:"editVisaCommentForm",
                        modal:true,
                        draggable:true,
                        data:data
                    });
                    var options = {
                        e: e,
                        uniqueID: data.uniqueID
                    }
                    form.renderModalForm(self.updateVisaComment, options);
                }
            });
        }
    }
    
    self.uploadActualVisaForm = function(e) {
        if(!$(e.currentTarget).hasClass("disabled")) {
            self.request.init({
                type:"form",
                subType:"upload-actual-visa",
                url:self.url,
                action: "visa",
                id: $("#tab-"+self.uniqueID).attr("data-value"),
                uniqueID: self.uniqueID,
                loading:$(".form-btn"),
                success: function(data) {
                    var form = new isarray_forms({
                        title:"Edit Visa",
                        formID:self.form.replace("#", ""),
                        modal:true,
                        draggable:true,
                        data:data
                    });
                    form.renderModalForm(self.uploadVisaDocument, data.uniqueID);
                }
            });
        }
    }
    
    self.uploadVisaDocument = function() {
        var form = new isarray_forms();
        form.setValidator(self.form);

        if($(self.form).valid()) {
            self.request.init({
                url:self.url,
                type:"update",
                subType:"upload-actual-visa",
                action:self.action,
                elementID: self.form,
                id: $("#tab-"+self.uniqueID).attr("data-value"),
                uniqueID: self.uniqueID,
                loading: self.load,
                success: function(data) {
                    if(data.status == true) {
                        $(".modal-header button.close").trigger("click");

                        self.generalTab.setTabForReloading("Visas");
                        self.generalTab.setTabForReloading("Current Visas");

                        getPanelDetails("/immigration/visa-details", "#visaDetails", data.uniqueID);
                        $(".visa-status", $($("#tab-"+data.uniqueID+" a").attr("href"))).text(data.visaStatus);
                        self.updateStatus(data.visaStatus);

                        $(".upload-actual-visa", "#tab-"+data.uniqueID).text("View Visa")
                                .removeClass('upload-actual-visa')
                                .addClass("view-actual-visa");
                        
                        messages.applicationMessage(data.message, "success");
                    } else {
                        messages.element("#formMessages", data.message, "danger");
                    }
                }
            });
        }
    }
    
    self.updateVisaDetails = function() {
        var form = new isarray_forms();
        form.setValidator(self.form);
        
        var request = new isarray_request();
        
        if($(self.form).valid()) {
            request.init({
                url:self.url,
                type:"update",
                action:self.action,
                elementID: self.form,
                id: $("#tab-"+self.uniqueID).attr("data-value"),
                uniqueID: self.uniqueID,
                loading: self.load,
                success: function(data) {
                    if(data.status == true) {
                        $(".modal-header button.close").trigger("click");

                        self.generalTab.setTabForReloading("Visas");

                        getPanelDetails("/immigration/visa-details", "#visaDetails", data.uniqueID);
                        getPanelDetails("/immigration/visa-progress", "#visaProgress", data.uniqueID);
                        $(".visa-status", $($("#tab-"+data.uniqueID+" a").attr("href"))).text(data.visaStatus);
                        self.updateStatus(data.visaStatus);
                        
                        $(".upload-actual-visa", "#tab-"+data.uniqueID).text("View Visa")
                                .removeClass('upload-actual-visa')
                                .addClass("view-actual-visa");
                        
                        messages.applicationMessage(data.message, "success");
                    } else {
                        messages.element("#formMessages", data.message, "danger");
                    }
                }
            });
        }
    }
    
    self.addVisaComment = function(e) {   
        var form = new isarray_forms();
        form.setValidator("#addVisaCommentForm");
        
        if($("#addVisaCommentForm").valid()) {
            self.request.init({
                url:self.url,
                type:"create",
                action:"visaComment",
                elementID:"#addVisaCommentForm",
                id: $("#tab-"+self.uniqueID).attr("data-value"),
                loading:true,
                success: function(data) {
                    if(data.status == true) {
                        $(".modal-header button.close").trigger("click");
                        messages.applicationMessage(data.message, "success");
                        getPanelDetails("/immigration/visa-comments", "#visaComments", data.uniqueID);
                    } else {
                        messages.applicationMessage(data.message, "danger")
                    }
                }
            });
        }
    };
    
    self.updateVisaComment = function(options) {   
        var form = new isarray_forms();
        form.setValidator("#editVisaCommentForm");
        
        if($("#editVisaCommentForm").valid()) {
            self.request.init({
                url:self.url,
                type:"update",
                action:"visaComment",
                elementID:"#editVisaCommentForm",
                id: $(options.e.currentTarget).attr("data-value"),
                uniqueID: options.uniqueID,
                loading:true,
                success: function(data) {
                    if(data.status == true) {
                        $(".modal-header button.close").trigger("click");
                        messages.applicationMessage(data.message, "success");
                        getPanelDetails("/immigration/visa-comments", "#visaComments", data.uniqueID);
                    } else {
                        messages.applicationMessage(data.message, "danger")
                    }
                }
            });
        }
    };
    
    self.visaView = function(e) {    
        self.request.init({
            url:self.url,
            type:"read",
            subType:"view-actual-visa",
            action:"visa",
            id: $("#tab-"+self.uniqueID).attr("data-value"),
            loading:true,
            success: function(data) {
                if(data.status == true) {
                    messages.confirm("Please click Ok to open certificate in a new tab", function() {
                        window.open(data.href, "_blank");
                    });
                } else {
                    messages.applicationMessage(data.message, "danger")
                }
            }
        });
    };
    
    self.deleteVisaComment = function(e) {    
        self.request.init({
            url:self.url,
            type:"delete",
            action:"visaComment",
            id: $(e.currentTarget).attr("data-value"),
            loading:true,
            success: function(data) {
                if(data.status == true) {
                    var tabs = new isarray_tabs();
                    tabs.removeRow(e);
                    messages.applicationMessage(data.message, "success");
                } else {
                    messages.applicationMessage(data.message, "danger")
                }
            }
        });
    };


    
    self.checkListView = function(e) {    
        var options = {
            title: "Visa Checklist",
            url: "/immigration/visa-documents-checklist",
            search: false,
            page: false,
            identifier: "checklist",
            limit: false,
            icon: false,
            val: $(e.currentTarget).attr("data-value"),
        }
        console.log(options);
        var tab = new isarray_tabs(options);
        if(tab.checkForDuplicateTab(options.title, options.identifier)) {
            if(!$(e.currentTarget).hasClass("disabled")) {
                tab.addTab();
                tab.updateTab();
            }
        }
    };
    
    self.updateStatus = function(status) {
        $("#visaStatus p").text(status);
    }
}
    
$(document).ready(function() {
    //Update Item
    $(".panel").off("click", ".edit-visa-details");
    $(".panel").on("click", ".edit-visa-details", function(e) {
        var visa = new visas();
        visa.getEditForm(e);
    });
    
    $(".panel").off("click", ".upload-actual-visa");
    $(".panel").on("click", ".upload-actual-visa", function(e) {
        var visa = new visas();
        visa.uploadActualVisaForm(e);
    });
    
    $(".panel").off("click", ".add-visa-comment");
    $(".panel").on("click", ".add-visa-comment", function(e) {
        var visa = new visas();
        visa.addVisaCommentForm(e);
    });
    
    $(".panel").off("click", ".edit-visa-comment");
    $(".panel").on("click", ".edit-visa-comment", function(e) {
        var visa = new visas();
        visa.editVisaCommentForm(e);
    });
    
    $(".tab-content").off("click", ".view-actual-visa");
    $(".tab-content").on("click", ".view-actual-visa", function(e) {
        var visa = new visas();
        visa.visaView(e);
    });
    
    //Delete Message
    $(".tab-content").off("click", ".delete-visa-comment");
    $(".tab-content").on("click", ".delete-visa-comment", function(e) {
        var tabs = new isarray_tabs();
        var visa = new visas();
        
        if(!$(e.currentTarget).hasClass("disabled")) {
            messages.confirm("Are you sure you want to delete "+tabs.getTitle(e), visa.deleteVisaComment, e);
        }
    });



    $(".tab-content").off('click', ".visa-documents-ckecklist button");
    $(".tab-content").on('click', ".visa-documents-ckecklist button", function(e) {
        
        var visa = new visas();
        visa.checkListView(e);
    });

});




</script>

<div class="container-fluid inner-view">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <div  class="panel panel-primary  inner-view-panel">
                <div class="panel-heading">Details</div>
                <ul class="panel-slider list-group" id="visaDetails">
                    
                </ul>
                <div class="panel-footer">

                </div>               
            </div>
            <div  class="panel panel-primary inner-view-panel">
                <div class="panel-heading">Comments</div>
                <div class="panel-slider" id="visaComments">
                    
                </div>
                <div class="panel-footer">

                </div>               
            </div>
            <div  class="panel panel-primary inner-view-panel">
                <div class="panel-heading">Special Documents</div>
                <div class="panel-slider" id="visaSpecialDocuments">
                    
                </div>
                <div class="panel-footer">

                </div>               
            </div>
            <div  class="panel panel-primary inner-view-panel">
                <div class="panel-heading">Financial Documents</div>
                <div class="panel-slider" id="visaFinancialDocuments">
                    
                </div>
                <div class="panel-footer">

                </div>               
            </div>
            <div  class="panel panel-primary inner-view-panel">
                <div class="panel-heading">Company Documentation</div>
                <div class="panel-slider" id="visaCompanyDocumentation">
                    
                </div>
                <div class="panel-footer">

                </div>               
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <div class="col-md-6">
                <div  class="panel panel-primary inner-view-panel">
                    <div class="panel-heading">Status</div>
                    <div class="" id="visaStatus">
                        <p class="visa-status" style="margin:20px auto; font-size:20px; text-align: center">
                            <? echo $this->model->getStatusName($this->model->status); ?>
                        </p>
                    </div>              
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                <div  class="panel panel-primary inner-view-panel">
                    <div class="panel-heading">Document Checklist</div>
                    <div class="visa-documents-ckecklist" id='visaDocumentsCkecklist'>
                        <p class="visa-status" style="margin:20px auto; font-size:20px; text-align: center">
                            <button type="button" class="btn btn-sm" data-value="<? echo $this->model->ID ?>">Download Document Checklist</button>
                        </p>
                    </div>              
                </div>
            </div>
            <div class="col-md-12">
                    <div  class="panel panel-primary inner-view-panel">
                        <div class="panel-heading">Progress</div>
                        <div class="panel-body" id="visaProgress">
                            
                        </div>              
                    </div>
            </div>
            <div class="col-md-12">
                <div  class="panel panel-primary  inner-view-panel">
                    <div class="panel-heading">Documentation</div>
                    <div class="panel-slider" id="visaDocumentation">

                    </div>
                    <div class="panel-footer">

                    </div>               
                </div>
            </div>
        </div>
    </div>
</div>