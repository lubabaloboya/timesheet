<script>
$(document).ready(function() {
    $(".table").tablesorter({ 
        headers: { 
            0: {sorter: false},
            1: {sorter: false}
        }
    });
    
         
    //Add Site to multiple areas
    $(".panel").off("click", ".add-site");
    $(".panel").on("click", ".add-site", function(e) {
        var addSiteRequest = new isarray_request();
        
        if(addSiteRequest.isRequestReady(e)) {
            var tab = new isarray_tabs();
            var uniqueID = tab.getActiveTab().attr("id").replace("tab-", "");

            addSiteRequest.init({
                type:"form",
                subType:"default",
                url:"/admin/index",
                uniqueID: uniqueID,
                action: "site",
                loading: true,
                success: function(data) {
                    var form = new isarray_forms({
                        title:"Add Site",
                        formID:"addSiteForm",
                        modal:true,
                        draggable:true,
                        data:data
                    });
                    form.renderModalForm(addSite, uniqueID);
                }
            });
        }
    });
    
    //Update Site
    $(".update-site").click(function(e) {
        var tab = new isarray_tabs();
        var uniqueID = tab.getActiveTab().attr("id").replace("tab-", "");
        var updateSiteRequest = new isarray_request();
        
        if(updateSiteRequest.isRequestReady(e)) {
            updateSiteRequest.init({
                type:"form",
                subType:"edit",
                url:"/admin/index",
                action: "site",
                uniqueID: uniqueID,
                loading: true,
                id:$(e.currentTarget).attr("data-value"),
                success: function(data) {
                    var form = new isarray_forms({
                        title:"Edit Company",
                        formID:"editSiteForm",
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
    
    //    //Delete site
    $(".delete-site").click(function(e) {
        var deleteSiteRequest = new isarray_request();
        
        if(deleteSiteRequest.isRequestReady(e)) {
            var tab = new isarray_tabs();
            messages.confirm("Are you sure you want to delete the site "+tab.getTitle(e), deleteSite, e);
        }
    })
});

function addSite(uniqueID) {
    var form = new isarray_forms();
    form.setValidator("#addSiteForm");
    var addSiteRequest = new isarray_request();
    
    if($("#addSiteForm").valid()) {
        addSiteRequest.init({
            type:"create",
            url:"/admin/index",
            elementID:"#addSiteForm",
            action:"site",
            id:$("#tab-"+uniqueID).attr("data-value"),
            uniqueID: uniqueID,
            loading:$(".form-btn"),
            success: function(data) {
                if(data.status == true) {        
                    $(".modal-header button.close").trigger("click");
                    messages.applicationMessage(data.message, "success");               
                    getPanelDetails("/admin/sites", "#sites", data.uniqueID);
                } else {
                    messages.element("#formMessages", data.message, "danger");
                }
            }
        });
    }
}

function updateSite(options) {
    var form = new isarray_forms();
    form.setValidator("#editSiteForm");
    var updateSiteRequest = new isarray_request();
    
    if($("#editSiteForm").valid()) {
        updateSiteRequest.init({
            url:"/admin/index",
            type:"update",
            action:"site",
            id:$(options.e.currentTarget).attr("data-value"),
            uniqueID: options.uniqueID,
            elementID:"#editSiteForm",
            loading:$(".form-btn"),
            success: function(data) {
                if(data.status == true) {
                    $(".modal-header button.close").trigger("click");
                    messages.applicationMessage(data.message, "success");
                    getPanelDetails("/admin/sites", "#sites", data.uniqueID);
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
        url:"/admin/index",
        type:"delete",
        action: "site",
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
    if(count($docs) > 0) {?>
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
            foreach($docs as $object) { ?>
        <tr>
            <td class="table-icon">
                <? 
                    $icon = new library_decorators_tableIcon();
                    $icon->isDisabled = $this->crud->update;
                    $icon->addClass(array("update-documentation-type", "edit"));
                    $icon->addAttributes(array("parent-value"=>$this->model->ID));
                    $icon->value = $object->ID;
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
                    $icon->value = $object->ID;
                    $icon->tooltip = array(
                        "title"=>"Remove this documentation type from this visa type",
                        "place"=>"bottom"
                    );
                    $icon->icon = "remove";
                    echo $icon->getElement(); 
                ?>
            </td>
            <td class="tab-title"><? echo $object->name; ?></td>
            <?
                $cell = new library_decorators_tableCell();
                $cell->strLenTotal = 20;
                $cell->text = $object->description;
                echo $cell->getElement();
            ?>
            <td><?
                switch($object->expiry) {
                    case 1: echo "Yes";
                        break;
                    case 0: echo "No";
                        break;
                }
            ?></td>
            <td><? echo $object->phase; ?></td>
        </tr>
        <?  }?>
    </tbody>
</table>
<? } else { ?>
<div class="well well-sm">No documentation types were found for <? echo $this->model->name ?></div>
<?}?>