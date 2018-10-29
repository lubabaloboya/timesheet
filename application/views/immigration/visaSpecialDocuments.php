<script>
$(document).ready(function() {
    var doc = new specialDocument();
    
    $(".panel").off("click", ".add-special-document");
    $(".panel").on("click", ".add-special-document", function(e) {

        doc.addForm(e);
    });
    
    $(".panel").off("click", ".update-special-document");
    $(".panel").on("click", ".update-special-document", function(e) {

        doc.editForm(e);
    });
    
    $(".panel").off("click", ".upload-special-documentation");
    $(".panel").on("click", ".upload-special-documentation", function(e) {

        doc.uploadForm(e);
    });
    
    $(".panel").off("click", ".view-special-document");
    $(".panel").on("click", ".view-special-document", function(e) {
        
        doc.view(e);
    });
    
    $(".panel").off("click", ".delete-special-document");
    $(".panel").on("click", ".delete-special-document", function(e) {
        messages.confirm("Are you sure you want to delete this document", function() {
            doc.delete(e);
        });
    });
});

function specialDocument() {
    var self = this;
    var tab = new isarray_tabs();
    
    self.currentTab = tab.getActiveTab();
    self.uniqueID = self.currentTab.attr("id").replace("tab-", "");
    
    self.addForm = function(e) {
        var request = new isarray_request();
        if(request.isRequestReady(e)) {
            var tab = new isarray_tabs();
            var uniqueID = tab.getActiveTab().attr("id").replace("tab-", "");
            request.init({
                type:"form",
                subType:"default",
                url:"/immigration/index",
                uniqueID: self.uniqueID,
                action: "specialDocument",
                loading: true,
                success: function(data) {
                    var form = new isarray_forms({
                        title:"Add Document",
                        formID:"addSpecialDocumentForm",
                        modal:true,
                        draggable:true,
                        data:data
                    });
                    form.renderModalForm(self.add, data.uniqueID);
                }
            });
        }
    }
    
    self.editForm = function(e) {
        var request = new isarray_request();
        if(request.isRequestReady(e)) {
            var tab = new isarray_tabs();

            request.init({
                type:"form",
                subType:"edit",
                url:"/immigration/index",
                uniqueID: self.uniqueID,
                action: "specialDocument",
                id:$(e.currentTarget).attr("data-value"),
                loading: true,
                success: function(data) {
                    var form = new isarray_forms({
                        title:"Edit Document",
                        formID:"editSpecialDocumentForm",
                        modal:true,
                        draggable:true,
                        data:data
                    });
                    var options = {
                        e: e,
                        uniqueID: data.uniqueID
                    }
                    form.renderModalForm(self.update, options);
                }
            });
        }
    }
    
    self.uploadForm = function(e) {
        var request = new isarray_request();
        if(request.isRequestReady(e)) {
            var tab = new isarray_tabs();

            request.init({
                type:"form",
                subType:"upload-file",
                url:"/immigration/index",
                uniqueID: self.uniqueID,
                action: "specialDocument",
                loading: true,
                success: function(data) {
                    var form = new isarray_forms({
                        title:"Upload Document",
                        formID:"uploadSpecialDocumentForm",
                        modal:true,
                        draggable:true,
                        data:data
                    });
                    var options = {
                        e: e,
                        uniqueID: data.uniqueID
                    }
                    form.renderModalForm(self.upload, options);
                }
            });
        }
    }
    
    self.add = function(uniqueID) {
        var form = new isarray_forms();
        form.setValidator("#addSpecialDocumentForm");
        var request = new isarray_request();

        if($("#addSpecialDocumentForm").valid()) {
            request.init({
                type:"create",
                url:"/immigration/index",
                elementID:"#addSpecialDocumentForm",
                action:"specialDocument",
                id:$("#tab-"+uniqueID).attr("data-value"),
                uniqueID: uniqueID,
                loading:$(".form-btn"),
                success: function(data) {
                    if(data.status == true) {        
                        $(".modal-header button.close").trigger("click");
                        messages.applicationMessage(data.message, "success"); 
                        
                        getPanelDetails("/immigration/visa-special-documents","#visaSpecialDocuments",data.uniqueID);
                        getPanelDetails("/immigration/visa-progress", "#visaProgress", data.uniqueID);
                    } else {
                        messages.element("#formMessages", data.message, "danger");
                    }
                }
            });
        }
    }
    
    self.upload = function(options) {
        var form = new isarray_forms();
        form.setValidator("#uploadSpecialDocumentForm");
        var request = new isarray_request();

        if($("#uploadSpecialDocumentForm").valid()) {
            request.init({
                type:"update",
                subType:"upload-file",
                url:"/immigration/index",
                elementID:"#uploadSpecialDocumentForm",
                action:"specialDocument",
                id:$(options.e.currentTarget).attr("data-value"),
                uniqueID: options.uniqueID,
                loading:$(".form-btn"),
                success: function(data) {
                    if(data.status == true) {        
                        $(".modal-header button.close").trigger("click");
                        messages.applicationMessage(data.message, "success");               
                        getPanelDetails("/immigration/visa-special-documents", "#visaSpecialDocuments", data.uniqueID);
                        getPanelDetails("/immigration/visa-progress", "#visaProgress", data.uniqueID);
                    } else {
                        messages.element("#formMessages", data.message, "danger");
                    }
                }
            });
        }
    }
    
    self.view = function(e) {
        var request = new isarray_request();
        
        if(request.isRequestReady(e)) {
            request.init({
                url:"/immigration/index",
                type:"read",
                subType:"view-document",
                action:"specialDocument",
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
    }
    
    self.update = function(options) {
        var form = new isarray_forms();
        form.setValidator("#editSpecialDocumentForm");
        var request = new isarray_request();

        if($("#editSpecialDocumentForm").valid()) {
            request.init({
                url:"/immigration/index",
                type:"update",
                action:"specialDocument",
                id:$(options.e.currentTarget).attr("data-value"),
                uniqueID: options.uniqueID,
                elementID:"#editSpecialDocumentForm",
                loading:$(".form-btn"),
                success: function(data) {
                    if(data.status == true) {
                        $(".modal-header button.close").trigger("click");
                        messages.applicationMessage(data.message, "success");
                        getPanelDetails("/immigration/visa-special-documents", "#visaSpecialDocuments", data.uniqueID);
                    } else {
                        messages.element("#formMessages", data.message, "danger");
                    }
                }
            });
        }
    }

    self.delete = function(e) {
        var request = new isarray_request();

        request.init({
            url:"/immigration/index",
            type:"delete",
            action: "specialDocument",
            id: $(e.currentTarget).attr("data-value"),
            loading:true,
            success: function(data) {
                if(data.status == true) {
                    var tab = new isarray_tabs();
                    tab.removeRow(e);
                    messages.applicationMessage(data.message, "success");
                } else {
                    messages.applicationMessage(data.message, "danger");
                }
            }
        });
    }
}


</script>

<? 
    $docs = $this->model->getSpecialDocuments();
    if(count($docs) > 0) { ?>
<table class="table table-striped table-hover table-sorter">
    <thead>
        <tr>
            <th class='table-icon'></th>
            <th class='table-icon'></th>
            <th class='table-icon'></th>
            <th>Name</th>
            <th>Status</th>
            <th>Expiry</th>
            <th>Uploaded</th>
        </tr>
    </thead>
    <tbody>
        <?
            foreach($docs as $doc) { ?>
        <tr>
            <td class="table-icon">
                <? 
                    $icon = new library_decorators_tableIcon();
                    switch($doc->status) {
                        case NULL:
                        case 0: //Added - Awaiting upload
                            $icon->addClass(array("upload-special-documentation", "download"));
                            $icon->value = $doc->ID;
                            $icon->icon = "export";
                            $icon->isDisabled = $this->crud->update;
                            $tooltip = "Upload your document here";
                            break;   
                        case 1: //Uploaded
                            $icon->addClass(array("view-special-document", "view"));
                            $icon->value =$doc->ID;;
                            $icon->icon = "search";
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
                    $icon->isDisabled = $this->crud->update;
                    $icon->addClass(array("update-special-document", "edit"));
                    $icon->value = $doc->ID;
                    $icon->tooltip = array(
                        "title"=>"Edit this visa type documentation",
                        "place"=>"bottom"
                    );
                    $icon->icon = "pencil";
                    echo $icon->getElement(); 
                ?>
            </td>
            <td class="table-icon">
                <? 
                    $icon = new library_decorators_tableIcon();
                    $icon->isDisabled = $this->crud->delete;
                    $icon->addClass(array("delete-special-document", "remove"));
                    $icon->value = $doc->ID;
                    $icon->tooltip = array(
                        "title"=>"Remove this documentation type from this visa type",
                        "place"=>"bottom"
                    );
                    $icon->icon = "remove";
                    echo $icon->getElement(); 
                ?>
            </td>
            <td class="tab-title"><? 
                echo $doc->name; 
            ?></td>
            <td><? 
                switch($doc->status) {
                    case 0: echo "Added";
                        break;
                    case 1: echo "Uploaded";
                        break;
                } 
            ?></td>
            <td><? 
                if(isset($doc->dateExpiry)) {
                    echo $doc->dateExpiry->format("d-m-Y");
                } else {
                    echo "N/A";
                }
            ?></td>
            <td><? 
                if(isset($doc->dateUploaded)) {
                    echo $doc->dateUploaded->format("d-m-Y");
                } else {
                    echo "TBA";
                }
            ?></td>
        </tr>
        <?  }?>
    </tbody>
</table>
<? } else { ?>
<div class="well well-sm">No special documents were found</div>
<?}?>