<script>
$(document).ready(function() {
    var company_doc = new companyDocumentation();
    
    $(".panel").off("click", ".view-company-document");
    $(".panel").on("click", ".view-company-document", function(e) {
        
        company_doc.view(e);
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
                action: "visaCompanyDocumentation",
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
    
    
    self.view = function(e) {
        var request = new isarray_request();
        
        if(request.isRequestReady(e)) {
            request.init({
                url:"/immigration/index",
                type:"read",
                action:"visaCompanyDocumentation",
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
}
    
</script>

<? 
    if(count($this->docs) > 0) { ?>
<table class="table table-striped table-hover table-sorter">
    <thead>
        <tr>
            <th class='table-icon'></th>
            <th>Name</th>
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
            <td class="tab-title"><? 
                echo $doc->name; 
            ?></td>
        </tr>
        <?  }?>
    </tbody>
</table>
<? } else { ?>
<div class="well well-sm">No documents were found</div>
<?}?>