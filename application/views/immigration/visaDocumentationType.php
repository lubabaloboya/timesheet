<script>
$(document).ready(function() {
    $(".table").tablesorter({ 
        headers: { 
            0: {sorter: false},
            1: {sorter: false}
        }
    });
    
         
    //Add Site to multiple areas
    $(".panel").off("click", ".add-visa-documentation-type");
    $(".panel").on("click", ".add-visa-documentation-type", function(e) {
        var addSiteRequest = new isarray_request();
        if(addSiteRequest.isRequestReady(e)) {
            var tab = new isarray_tabs();
            var uniqueID = tab.getActiveTab().attr("id").replace("tab-", "");
            addSiteRequest.init({
                type:"form",
                subType:"default",
                url:"/immigration/index",
                uniqueID: uniqueID,
                action: "visaDocumentationType",
                loading: true,
                success: function(data) {
                    var form = new isarray_forms({
                        title:"Add Document Type",
                        formID:"addDocumentTypeForm",
                        modal:true,
                        draggable:true,
                        data:data
                    });
                    form.renderModalForm(addDocumentType, uniqueID);
                }
            });
        }
    });
    
    //Update Site
    $(".update-documentation-type").click(function(e) {
        var tab = new isarray_tabs();
        var uniqueID = tab.getActiveTab().attr("id").replace("tab-", "");
        var request = new isarray_request();
        
        if(request.isRequestReady(e)) {
            request.init({
                type:"form",
                subType:"edit",
                url:"/immigration/index",
                action: "visaDocumentationType",
                uniqueID: uniqueID,
                loading: true,
                id:$(e.currentTarget).attr("data-value"),
                success: function(data) {
                    var form = new isarray_forms({
                        title:"Edit Company",
                        formID:"editVisaDocumentationType",
                        modal:true,
                        draggable:true,
                        data:data
                    });
                    var options = {
                        e: e,
                        uniqueID: uniqueID
                    }
                    form.renderModalForm(updateSite, options);
                }
            });
        }
    });
    
    //Delete site
    $(".delete-documentation-type").click(function(e) {
        var deleteSiteRequest = new isarray_request();
        
        if(deleteSiteRequest.isRequestReady(e)) {
            var tab = new isarray_tabs();
            messages.confirm("Are you sure you want to delete the document "+tab.getTitle(e), deleteSite, e);
        }
    })
});

function addDocumentType(uniqueID) {
    var form = new isarray_forms();
    form.setValidator("#addDocumentTypeForm");
    var addSiteRequest = new isarray_request();
    
    if($("#addDocumentTypeForm").valid()) {
        addSiteRequest.init({
            type:"create",
            url:"/immigration/index",
            elementID:"#addDocumentTypeForm",
            action:"visaDocumentationType",
            id:$("#tab-"+uniqueID).attr("data-value"),
            uniqueID: uniqueID,
            loading:$(".form-btn"),
            success: function(data) {
                if(data.status == true) {        
                    $(".modal-header button.close").trigger("click");
                    messages.applicationMessage(data.message, "success");               
                    getPanelDetails("/immigration/visa-documentation-types", "#visaDocumentationTypes",data.uniqueID);
                } else {
                    messages.element("#formMessages", data.message, "danger");
                }
            }
        });
    }
}

function updateSite(options) {
    var form = new isarray_forms();
    form.setValidator("#editVisaDocumentationType");
    var updateSiteRequest = new isarray_request();
    
    if($("#editVisaDocumentationType").valid()) {
        updateSiteRequest.init({
            url:"/immigration/index",
            type:"update",
            action:"visaDocumentationType",
            id:$(options.e.currentTarget).attr("data-value"),
            uniqueID: options.uniqueID,
            elementID:"#editVisaDocumentationType",
            loading:$(".form-btn"),
            success: function(data) {
                if(data.status == true) {
                    $(".modal-header button.close").trigger("click");
                    messages.applicationMessage(data.message, "success");
                    getPanelDetails("/immigration/visa-documentation-types", "#visaDocumentationTypes", data.uniqueID);
                } else {
                    messages.element("#formMessages", data.message, "danger");
                }
            }
        });
    }
}

function deleteSite(e) {
    var deleteSiteRequest = new isarray_request();
    
    deleteSiteRequest.init({
        url:"/immigration/index",
        type:"delete",
        action: "visaDocumentationType",
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
</script>

<? 
    $docs = $this->model->getDocumentation();
    if(count($docs) > 0) { ?>
<table class="table table-striped table-hover table-sorter">
    <thead>
        <tr>
            <th class='table-icon'></th>
            <th class='table-icon'></th>
            <th>Name</th>
            <th>Description</th>
            <th>Expiry</th>
            <th>Phase</th>
        </tr>
    </thead>
    <tbody>
        <?
            foreach($docs as $doc) { ?>
        <tr>
            <td class="table-icon">
                <? 
                    $icon = new library_decorators_tableIcon();
                    $icon->isDisabled = $this->crud->update;
                    $icon->addClass(array("update-documentation-type", "edit"));
                    $icon->addAttributes(array("parent-value"=>$this->model->ID));
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
                    $icon->addClass(array("delete-documentation-type", "remove"));
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
                echo $doc->description; 
            ?></td>
            <td><? 
                switch($doc->expiry) {
                    case 1: echo "Yes";
                        break;
                    case 0: echo "No";
                        break;
                }
            ?></td>
            <td><?
                switch($doc->phase) {
                    case 1:  echo "Urgent documents/information";
                        break;
                    case 2:  echo "Documents required from the applicant";
                        break;
                    case 3:  echo "Documents required from the employer/company";
                        break;
                    case 4:  echo "Documents compiled by Xpatweb";
                        break;  
                }  
            ?></td>
        </tr>
        <?  }?>
    </tbody>
</table>
<? } else { ?>
<div class="well well-sm">No documentation types were found for <i>"<? echo $this->model->name ?>"</i></div>
<?}?>