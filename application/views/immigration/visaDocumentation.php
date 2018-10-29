<script>
$(document).ready(function() {  
    //Add Site to multiple areas
    $(".panel").off("click", ".upload-documentation");
    $(".panel").on("click", ".upload-documentation", function(e) {
        var request = new isarray_request();
        
        if(request.isRequestReady(e)) {
            var tab = new isarray_tabs();
            var uniqueID = tab.getActiveTab().attr("id").replace("tab-", "");

            request.init({
                type:"form",
                subType:"upload-document",
                url:"/immigration/index",
                uniqueID: uniqueID,
                id:$(e.currentTarget).attr("data-value"),
                action: "visaDocumentation",
                loading: true,
                success: function(data) {
                    var form = new isarray_forms({
                        title:"Add Documentation",
                        formID:"visaDocumentationForm",
                        modal:true,
                        draggable:true,
                        data:data
                    });
                    var options = {
                        e:e,
                        uniqueID: data.uniqueID
                    }
                    form.renderModalForm(uploadVisaDocument, options);
                    
                    $("#visaDocumentationForm .btn-checkbox").click(function() {
                        
                       var document = $("#document");
                       var expiry = $("#visaDocumentationDateExpiry");
                       
                        if($(this).attr('data-value') == 1) {
                            document.closest('.form-group').hide()
                                .removeAttr("required");
                        
                            expiry.removeAttr("required")
                                .closest('.form-group').hide();
                         } else {
                            document.closest('.form-group').show()
                                .attr("required", true);
                        
                            expiry.removeAttr("required")
                                .attr('.form-group', true).show();
                         }
                        
                    });
                }
            });
        }
    });

    
    $(".panel").off("click", ".add-document-reminder");
    $(".panel").on("click", ".add-document-reminder", function(e) {
        var request = new isarray_request();
        
        if(request.isRequestReady(e)) {
            var tab = new isarray_tabs();
            var uniqueID = tab.getActiveTab().attr("id").replace("tab-", "");

            request.init({
                type:"form",
                subType:"default",
                url:"/immigration/index",
                uniqueID: uniqueID,
                id:$(e.currentTarget).attr("data-value"),
                action: "documentReminder",
                loading: true,
                success: function(data) {
                    var form = new isarray_forms({
                        title:"Add Reminder",
                        formID:"addDocumentReminderForm",
                        modal:true,
                        draggable:true,
                        data:data
                    });
                    var options = {
                        e:e,
                        uniqueID: data.uniqueID
                    }
                    form.renderModalForm(addDocumentReminder, options);

                }
            });
        }
    });


    $(".upload-documentation").attr("style", "color:#f59d3d");

    if (parseInt($(".not-required").attr("data-value")) === 3) {
        $(".not-required").hide();
    }
    
    //Add Site to multiple areas
    $(".panel").off("click", ".validate-document");
    $(".panel").on("click", ".validate-document", function(e) {
        var addSiteRequest = new isarray_request();
        
        if(addSiteRequest.isRequestReady(e)) {
            var tab = new isarray_tabs();
            var uniqueID = tab.getActiveTab().attr("id").replace("tab-", "");

            addSiteRequest.init({
                type:"form",
                subType:"validate-document",
                url:"/immigration/index",
                uniqueID: uniqueID,
                id:$(e.currentTarget).attr("data-value"),
                action: "visaDocumentation",
                loading: true,
                success: function(data) {
                    var form = new isarray_forms({
                        title:"Validate Document",
                        formID:"validateVisaDocumentationForm",
                        modal:true,
                        draggable:true,
                        data:data
                    });
                    var options = {
                        e:e,
                        uniqueID: data.uniqueID
                    }
                    form.renderModalForm(validateDocument, options);
                    
                    $("body").off("click", "#validateVisaDocumentationForm .btn-group");
                    $("body").on("click", "#validateVisaDocumentationForm .btn-group", function() {
                        var val = $(this).next().val();
                        if(val == 3) {
                            $("#reason").addClass("required");
                            $("#reason").removeAttr("disabled");
                        } else {
                            $("#reason").removeClass("required");
                            $("#reason").attr("disabled", true);
                        }
                    });
                }
            });
        }
    });
    
    //Read document information
    $(".panel").off("click", ".document-info");
    $(".panel").on("click", ".document-info", function(e) {
        var request = new isarray_request();
        
        if(request.isRequestReady(e)) {
            var tab = new isarray_tabs();
            var uniqueID = tab.getActiveTab().attr("id").replace("tab-", "");

            request.init({
                type:"read",
                subType:"read-document-info",
                url:"/immigration/index",
                uniqueID: uniqueID,
                id:$(e.currentTarget).attr("data-value"),
                action: "visaDocumentation",
                loading: true,
                success: function(data) {
                    var form = new isarray_forms({
                        title:"Documentation Type Description",
                        formID:"viewVisaDocumentationInfoForm",
                        modal:true,
                        draggable:true,
                        data:data
                    });
                    form.renderModalForm(); 
                    $(".document-information").hide();
                }
            });
        }
    });
    
     //View visa document
    $(".panel").off("click", ".read-documentation");
    $(".panel").on("click", ".read-documentation", function(e) {
        var tab = new isarray_tabs();
        var request = new isarray_request();
        
        if(request.isRequestReady(e)) {
            request.init({
                url:"/immigration/index",
                type:"read",
                subType:"read-document",
                action:"visaDocumentation",
                id:$(e.currentTarget).attr("data-value"),
                loading:true,
                success: function(data) {
                    if(data.status == true) {
                        messages.confirm("Please click Ok to open document in a new tab", function() {
                            window.open(data.href, "_blank");
                        });
                    } else {
                        messages.applicationMessage(data.message, "danger")
                    }
                }
            });
        }
    });
    
    //View visa document
    $(".panel").off("click", ".reinstate-document");
    $(".panel").on("click", ".reinstate-document", function(e) {
        var tab = new isarray_tabs();
        var request = new isarray_request();
        var uniqueID = tab.getActiveTab().attr("id").replace("tab-", "");
        
        if(request.isRequestReady(e)) {
            request.init({
                url:"/immigration/index",
                type:"update",
                subType:"reinstate-document",
                action:"visaDocumentation",
                id:$(e.currentTarget).attr("data-value"),
                uniqueID: uniqueID,
                loading:true,
                data: function() {
                    return convert(JSON.stringify({
                        visaDocumentationNotRequired: 0
                    }));
                },
                success: function(data) {
                    getPanelDetails("/immigration/visa-documentation", "#visaDocumentation", data.uniqueID);
                    $(".visa-status", $($("#tab-"+data.uniqueID+" a").attr("href"))).text(data.visaStatus);
                }
            });
        }
    });
 
    //Delete site
    $(".delete-document").click(function(e) {
        var tab = new isarray_tabs();
        var deleteSiteRequest = new isarray_request();
        var uniqueID = tab.getActiveTab().attr("id").replace("tab-", "");
        
        if(deleteSiteRequest.isRequestReady(e)) {
            var options = {
                e: e,
                uniqueID: uniqueID
            }
            var tab = new isarray_tabs();
            messages.confirm("Are you sure you want to delete the document "+tab.getTitle(e), deleteDocument, options);
        }
    })
});

function uploadVisaDocument(options) {
    var form = new isarray_forms();
    form.setValidator("#visaDocumentationForm");
    var request = new isarray_request();
    
    if($("#visaDocumentationForm").valid()) {
        request.init({
            type:"create",
            subType:"upload-document",
            url:"/immigration/index",
            elementID:"#visaDocumentationForm",
            action:"visaDocumentation",
            id:$(options.e.currentTarget).attr("data-value"),
            uniqueID: options.uniqueID,
            appendData: [
                {
                    "key": "visaDocumentationTypeID",
                    "value":$(options.e.currentTarget).attr("data-value")
                }
            ],
            loading:$(".form-btn"),
            success: function(data) {
                if(data.status == true) {
                    $(".modal-header button.close").trigger("click");
                    messages.applicationMessage(data.message, "success");
                    getPanelDetails("/immigration/visa-documentation", "#visaDocumentation", data.uniqueID);
                    getPanelDetails("/immigration/visa-progress", "#visaProgress", data.uniqueID);
                    $(".visa-status", $($("#tab-"+data.uniqueID+" a").attr("href"))).text(data.visaStatus);
                } else {
                    messages.element("#formMessages", data.message, "danger");
                }
            }
        });
    }
}




function addDocumentReminder(options) {
    var form = new isarray_forms();
    form.setValidator("#addDocumentReminderForm");
    var request = new isarray_request();
    
    if($("#addDocumentReminderForm").valid()) {
        request.init({
            type:"create",
            url:"/immigration/index",
            elementID:"#addDocumentReminderForm",
            action:"documentReminder",
            id:$(options.e.currentTarget).attr("data-value"),
            uniqueID: options.uniqueID,
            loading:$(".form-btn"),
            success: function(data) {
                if(data.status == true) {
                    $(".modal-header button.close").trigger("click");
                    messages.applicationMessage(data.message, "success");
                    getPanelDetails("/immigration/visa-documentation", "#visaDocumentation", data.uniqueID);
                } else {
                    messages.element("#formMessages", data.message, "danger");
                }
            }
        });
    }
}




function validateDocument(options) {
    var form = new isarray_forms();
    form.setValidator("#validateVisaDocumentationForm");
    var updateSiteRequest = new isarray_request();
    
    if($("#validateVisaDocumentationForm").valid()) {
        updateSiteRequest.init({
            url:"/immigration/index",
            type:"update",
            subType:"validate-document",
            action:"visaDocumentation",
            id:$(options.e.currentTarget).attr("data-value"),
            uniqueID: options.uniqueID,
            elementID:"#validateVisaDocumentationForm",
            loading:$(".form-btn"),
            success: function(data) {
                if(data.status == true) {
                    $(".modal-header button.close").trigger("click");
                    messages.applicationMessage(data.message, "success");
                    
                    var tab = new isarray_tabs();
                    tab.setTabForReloading("Visas");
                    tab.setTabForReloading("Current Visas");

                    $(".visa-status", $($("#tab-"+data.uniqueID+" a").attr("href"))).text(data.visaStatus);
                    
                    getPanelDetails("/immigration/visa-documentation", "#visaDocumentation", data.uniqueID);
                    getPanelDetails("/immigration/visa-progress", "#visaProgress", data.uniqueID);
                } else {
                    messages.element("#formMessages", data.message, "danger");
                }
                
            }
        });
    }
}

function deleteDocument(options) {
    var deleteSiteRequest = new isarray_request();
    
    deleteSiteRequest.init({
        url:"/immigration/index",
        type:"delete",
        subType:"delete-visa-document",
        action: "visaDocumentation",
        uniqueID: options.uniqueID,
        id: $(options.e.currentTarget).attr("data-value"),
        loading:true,
        success: function(data) {
            if(data.status == true) {
                var tab = new isarray_tabs();
                getPanelDetails("/immigration/visa-documentation", "#visaDocumentation", data.uniqueID);
                getPanelDetails("/immigration/visa-progress", "#visaProgress", data.uniqueID);
                messages.applicationMessage(data.message, "success");
            } else {
                messages.applicationMessage(data.message, "danger");
            }
        }
    });
}

</script>

<style>
    
    tr.not-required td {
        text-decoration: line-through;
        font-style: italic;
        color:grey;
    }
</style>
<? 
    $visa_docs = $this->docs->getVisaDocs($this->visa->ID);
    if(count($visa_docs) > 0) { ?>
<table class="table table-striped table-hover table-sorter">
    <thead>
        <tr>
            <th class='table-icon'></th>
            <th class='table-icon'></th>
            <th class='table-icon'></th>
            <th class='table-icon'></th>
            <th class='table-icon'></th>
            <th>Name</th>
            <th>Expiry</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?
            foreach($visa_docs as $visa_doc) {
                $visa_doc_type = new model_visaDocumentationType($visa_doc->visaDocumentationTypeID);
                
            if(!isset($phase) || $visa_doc_type->phase != $phase) { 
                switch($visa_doc_type->phase) {
                    case 1: $name = "Urgent documents/information";
                        break;
                    case 2: $name = "Documents required from the applicant";
                        break;
                    case 3: $name = "Documents required from the employer/company";
                        break;
                    case 4: $name = "Documents compiled by Xpatweb";
                        break;  
                }                
            ?>
        <tr>
            <td style="background:#8A94A7; font-weight:bold; color:white;" colspan="7"><? echo $name ?></td>
        </tr>
            <? 
                $phase = $visa_doc_type->phase; 

            } ?>
         <tr class="<?php if($visa_doc->notRequired == 1) { echo 'not-required'; } ?>" data-value="<? echo $this->user->roleID ?>">
            <td class="table-icon">
                <? 
                    $icon = new library_decorators_tableIcon();
                     //Not uploaded
                   
                    switch($visa_doc->status) {
                        case NULL:
                        case 0: //Added - Awaiting Upload
                            
                            if($visa_doc->notRequired == 0) {
                                
                                $icon->addClass(array("upload-documentation", "download"));
                                $icon->value = $visa_doc->ID;
                                $icon->icon = "export";
                                $icon->isDisabled = $this->crud->update;
                                $tooltip = "Upload your document here";
                            } else {
                                $icon->addClass(array("reinstate-document", 'download'));
                                $icon->value = $visa_doc->ID;
                                $icon->icon = "refresh";
                                $tooltip = "Document needed";  
                            }
                            
                            break;
                        case 1: //Uploaded
 
                                $icon->addClass(array("success"));
                                $icon->value = $visa_doc->ID;
                                $icon->icon = "ok";
                                $tooltip = "View uploaded document";
        
                            break;
                    }

                    $icon->isDisabled = $this->crud->update;
                    $icon->tooltip = array(
                            "title"=>$tooltip,
                            "place"=>"bottom"
                        );

                    echo $icon->getElement(); 
                
                ?>
            </td>
            <td class="table-icon">
                <? 
                    $icon = new library_decorators_tableIcon();
                    $icon->addClass(array("read-documentation", "view"));
                    $icon->value = $visa_doc->ID;;
 
                    switch($visa_doc->status) {
                        case NULL:
                        case 0:
                            $icon->isDisabled = 0;
                            break;
                        case 1:
                        case 2:
                        case 3:
                            $icon->isDisabled = $this->crud->read;
                            break;
                        default:
                            $icon->isDisabled = $this->crud->read;
                            break;
                    }

                    $icon->tooltip = array(
                        "title"=>"View this document",
                        "place"=>"bottom"
                    );
                    $icon->icon = "search";
                    
                    if($visa_doc->notRequired == 0) {
                        echo $icon->getElement(); 
                    }
                ?>
            </td>
            <td class="table-icon">
                <? 
                    $icon = new library_decorators_tableIcon();
                    $icon->addClass(array("delete-document", "remove"));

                    switch($visa_doc->status) {
                        case NULL:
                        case 0:
                            $icon->isDisabled = 0;
                            break;
                        case 1:
                        case 3:
                            
                            $icon->isDisabled = $this->crud->delete;
                            $icon->value = $visa_doc->ID;
                           
                            break;
                        case 2:
                            if($this->user->roleID < 3) {
                                $icon->isDisabled = $this->crud->delete;
                            } else {
                                $this->disabled = 0;
                            }
                            $icon->value = $visa_doc->ID;
                            break;
                        default:
                            $icon->isDisabled = $this->crud->delete;
                            break;
                    }

                    $icon->tooltip = array(
                        "title"=>"Delete this document",
                        "place"=>"bottom"
                    );
                    $icon->icon = "remove";
                    
                    if($visa_doc->notRequired == 0) {
                        echo $icon->getElement(); 
                    }
                ?>
            </td>
            <td class="table-icon">
                <? 
                    $icon = new library_decorators_tableIcon();
                    $icon->addClass(array("document-info", "view"));
                    $icon->value = $visa_doc->ID;
 
                    switch($visa_doc->status) {
                        case NULL:
                        case 0:
                        case 1:
                        case 2:
                        case 3:
                            $icon->addClass(array("success"));
                            $icon->isDisabled = $this->crud->read;
                            break;
                    }

                    $icon->tooltip = array(
                        "title"=>"View document information",
                        "place"=>"bottom"
                    );
                    $icon->icon = "info-sign";
                    
                    if($visa_doc->notRequired == 0) {
                        echo $icon->getElement(); 
                    }
                ?>
            </td>
            <td class="table-icon">
                <? 
                    $icon = new library_decorators_tableIcon();
                    $icon->addClass(array("add-document-reminder", "view"));
                    $icon->value = $visa_doc->ID;                    
                    $tooltip = "Add document reminder";
                    $icon->tooltip = array(
                            "title"=>$tooltip,
                            "place"=>"create"
                        );
                    $icon->icon = "calendar";
                    if($this->reminders->countDocumentationWithReminders($visa_doc->ID) > 0)
                    {
                        $icon->isDisabled;
                        echo $icon->getElement(); 
                    }else{
                        $icon->isDisabled = $this->crud->update;
                        echo $icon->getElement(); 
                    }
                
                ?>
            </td>
            <td class="tab-title"><? 
                echo $visa_doc_type->name; 
            ?></td>
            <td><? 
                if($visa_doc_type->expiry == 1 && isset($visa_doc->dateExpiry)) {
                    echo $visa_doc->dateExpiry->format("d-m-Y");
                } else {
                    echo "N/A";
                }
            ?></td>
            <td class="document-status" style="width:140px"><?
                switch($visa_doc->status) {
                    case Null:
                    case 0: echo '<span style="color:grey">Awaiting upload</span>';
                        break;
                    case 1: echo '<span style="color:darkgreen;">Accepted</span>';
                        break;
                }
            ?></td>
        </tr>
        <?  }?>
    </tbody>
</table>
<? } else { ?>
<div class="well well-sm">No documentation types were found for <i>"<? echo $this->model->ID ?>"</i></div>
<?}?>