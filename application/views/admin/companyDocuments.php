<script>
$(document).ready(function() {
    var company_doc = new companyDocumentation();
    
    $(".panel").off("click", ".add-company-document");
    $(".panel").on("click", ".add-company-document", function(e) {

        company_doc.addForm(e);
    });
    
    $(".panel").off("click", ".update-company-document");
    $(".panel").on("click", ".update-company-document", function(e) {

        company_doc.editForm(e);
    });    
    
    $(".panel").off("click", ".view-company-document");
    $(".panel").on("click", ".view-company-document", function(e) {
        
        company_doc.view(e);
    });
    
    $(".panel").off("click", ".delete-company-document");
    $(".panel").on("click", ".delete-company-document", function(e) {
        messages.confirm("Are you sure you want to delete this document", function() {
            company_doc.delete(e);
        });
    });
});

function companyDocumentation() {
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
                url:"/admin/index",
                uniqueID: self.uniqueID,
                action: "companyDocumentation",
                loading: true,
                success: function(data) {
                    var form = new isarray_forms({
                        title:"Add Document",
                        formID:"addCompanyDocumentForm",
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
                action: "companyDocumentation",
                id:$(e.currentTarget).attr("data-value"),
                loading: true,
                success: function(data) {
                    var form = new isarray_forms({
                        title:"Edit Document",
                        formID:"editCompanyDocumentForm",
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
    
    self.add = function(uniqueID) {
        var form = new isarray_forms();
        form.setValidator("#addCompanyDocumentForm");
        var request = new isarray_request();

        if($("#addCompanyDocumentForm").valid()) {
            request.init({
                type:"create",
                url:"/immigration/index",
                elementID:"#addCompanyDocumentForm",
                action:"companyDocumentation",
                id:$("#tab-"+uniqueID).attr("data-value"),
                uniqueID: uniqueID,
                loading:$(".form-btn"),
                success: function(data) {
                    if(data.status == true) {        
                        $(".modal-header button.close").trigger("click");
                        messages.applicationMessage(data.message, "success");               
                        getPanelDetails("/admin/company-documents", "#companyDocuments", data.uniqueID);
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
                action:"companyDocumentation",
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
        form.setValidator("#editCompanyDocumentForm");
        var request = new isarray_request();

        if($("#editCompanyDocumentForm").valid()) {
            request.init({
                url:"/immigration/index",
                type:"update",
                action:"companyDocumentation",
                id:$(options.e.currentTarget).attr("data-value"),
                uniqueID: options.uniqueID,
                elementID:"#editCompanyDocumentForm",
                loading:$(".form-btn"),
                success: function(data) {
                    if(data.status == true) {
                        $(".modal-header button.close").trigger("click");
                        messages.applicationMessage(data.message, "success");
                        getPanelDetails("/admin/company-documents", "#companyDocuments", data.uniqueID);
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
            action: "companyDocumentation",
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
    if(count($this->docs) > 0) { ?>
<table class="table table-striped table-hover table-sorter">
    <thead>
        <tr>
            <th class='table-icon'></th>
            <th class='table-icon'></th>
            <th class='table-icon'></th>
            <th>Name</th>
            <th>Uploaded</th>
        </tr>
    </thead>
    <tbody>
        <?
            foreach($this->docs as $doc) { ?>
        <tr>
            <td class="table-icon">
                <? 
                    $icon = new library_decorators_tableIcon();
                    $icon->isDisabled = $this->crud->read;
                    $icon->addClass(array("view-company-document", "view"));
                    $icon->value = $doc->ID;
                    $icon->tooltip = array(
                        "title"=>"View a copy of this document",
                        "place"=>"bottom"
                    );
                    $icon->icon = "search";
                    echo $icon->getElement(); 
                ?>
            </td>
            <td class="table-icon">
                <? 
                    $icon = new library_decorators_tableIcon();
                    $icon->isDisabled = $this->crud->update;
                    $icon->addClass(array("update-company-document", "edit"));
                    $icon->value = $doc->ID;
                    $icon->tooltip = array(
                        "title"=>"Edit this companies' documentation",
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
                    $icon->addClass(array("delete-company-document", "remove"));
                    $icon->value = $doc->ID;
                    $icon->tooltip = array(
                        "title"=>"Remove this documentation from this company",
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
<div class="well well-sm">No documents were found</div>
<?}?>