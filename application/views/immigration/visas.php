<? if(count($this->rows) > 0) { ?>
<script>
$(document).ready(function() {
    $(".table").tablesorter({ 
        headers: { 
            // assign the secound column (we start counting zero) 
            0: {sorter: false}, 
            1: {sorter: false},
            2: {sorter: false}
        }
    });

    var tab = new isarray_tabs();

    $(".navbar-fixed-bottom").off("click", ".export-current-visas");
    $(".navbar-fixed-bottom").on("click", ".export-current-visas", function(e) {
        downloadReport(tab.getActiveTab().attr("id").replace("tab-"), "export-current-visas");
    });
    
    $(".navbar-fixed-bottom").off("click", ".export-completed-visas");
    $(".navbar-fixed-bottom").on("click", ".export-completed-visas", function(e) {
        downloadReport(tab.getActiveTab().attr("id").replace("tab-"), "export-completed-visas");
    });

    $("#main").off("add-item-success");
    $("#main").off("update-item-success");
    $("#main").off("delete-item-success");
    
    var visa_tab = new isarray_tabs();
    
    $("#main").on("add-item-success", function(e, data) {
        if(data.event == "add-visa") {
            visa_tab.setTabForReloading("Visas");
        }
    });
    
    $("#main").on("update-item-success", function(e, data) {
        if(data.event == "update-visa") {
            visa_tab.setTabForReloading("Visas");
            $(".visa-status", $($("#tab-"+data.uniqueID+" a").attr("href"))).text(data.visaStatus);
        }
    });
    
    $("#main").on("delete-item-success", function(e, data) {
        if(data.event == "delete-visa") {
            visa_tab.setTabForReloading("Visas");
        }
    });
});

function downloadReport(uniqueID, type) {
    var request = new isarray_request();
    request.init({
        url:"/immigration/index",
        type:"read",
        subType:type,
        action:"visa",
        uniqueID: uniqueID,
        loading:true,
        success: function(data) {
            if(data.status == true) {
                messages.confirm("Click here to download this report", function() {
                    window.open(data.href, "_blank");
                });
            } else {
                messages.applicationMessage(data.message, "danger")
            }
        }
    });
}    
</script>
<table rules="all" class="database table table-striped table-hover tablesorter">
    <thead>
        <tr>
            <th class='table-icon'></th>
            <th class='table-icon'></th>
            <th class='table-icon'></th>
            <? if($this->user->roleID != 3) { //If the user is not a customer ?>     
            <th>ID</th>
            <th>Company</th>
            <? } ?>
            <th>Name</th>
            <th>Surname</th>
            <th>Passport</th>
            <th>Type</th>
            <th>Status</th>
            <? if ($this->user->roleID != 3) { ?>
            <th>Created By</th>
            <? } ?>
            <th>Appointment</th>
            <th>Expiry</th>
            <th>Documentation</th>
        </tr>
    </thead>
    <tbody>
        <? 
            foreach($this->rows as $v) {
                $row = new model_visa($v["visaID"]);
                $expat = new model_expatriate($row->expatriateID);
                $user = new model_user($expat->userID);
                $createdBy = new model_user($row->createdby);
                $company = new model_company($user->companyID);
                $visaType = new model_visaType($row->visaTypeID);
        ?>
            <tr>
                <td class="table-icon">
                     <? 
                        $icon = new library_decorators_tableIcon();
                        $icon->isDisabled = $this->crud->read;
                        $icon->addClass(array("view", "view-icon"));
                        $icon->addAttributes(array(
                            "url"=>"/immigration/index",
                            "action"=>"visa",
                            "id"=>"viewVisa"
                        ));
                        $icon->value = $row->ID;
                        $icon->tooltip = array(
                            "title"=>"View visa in more detail",
                            "place"=>"bottom"
                        );
                        $icon->icon = "new-window";
                        echo $icon->getElement();
                    ?>
                </td>
                <td class="table-icon">
                    <? 
                        $icon = new library_decorators_tableIcon();
                        $icon->isDisabled = $this->crud->update;
                        $icon->addClass(array("edit", "edit-icon"));
                        $icon->value = $row->ID;
                        $icon->addAttributes(array(
                            "url"=>"/immigration/index",
                            "formType"=>"edit",
                            "action"=>"visa",
                            "event"=>"update-visa"
                        ));
                        $icon->tooltip = array(
                            "title"=>"Edit visa information",
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
                        $icon->addClass(array("remove", "remove-icon"));
                        $icon->addAttributes(array(
                            "url"=>"/immigration/index",
                            "action"=>"visa",
                            "event"=>"delete-visa"
                        ));
                        $icon->value = $row->ID;
                        $icon->tooltip = array(
                            "title"=>"Delete visa",
                            "place"=>"bottom"
                        );
                        $icon->icon = "remove";
                        echo $icon->getElement();
                    ?>
                </td>
                <? if($this->user->roleID != 3) { ?>
                <td class="tab-title"><? echo $row->getVisaID(); ?></td>
                <td><? echo $company->name ?></td>
                <? } ?>
                <td><? echo $user->name ?></td>
                <td><? echo $user->surname ?></td>
                <td class="tab-title"><? echo $expat->passportNumber ?></td>
                <td><? echo $visaType->name ?></td>
                <td><?
                    echo $row->getStatusName($row->status);
                ?></td>
                <? if($this->user->roleID != 3) { ?>
                <td><? echo  $createdBy->name; ?></td>
                <? } ?>
                <td><?
                    if(isset($row->dateAppointment)){
                        echo $row->dateAppointment->format("d-m-Y");
                    } else {
                        echo "N/A";
                    }
                ?></td>
                <td><?
                    if(isset($row->dateExpiry) && $row->status == 9) {
                        echo $row->dateExpiry->format("d-m-Y");
                    } else {
                        echo "N/A";
                    }
                ?></td>
                <td class="table-icon">
                    <? 
                        $icon = new library_decorators_tableIcon();
                        //Not uploaded
                    
                        $icon->icon = "export";
                        $tooltip = "Download your checklist here";
                        $icon->tooltip = array(
                            "title"=>$tooltip,
                            "place"=>"bottom"
                        );
                        echo $icon->getElement(); 
                    
                    ?>
                </td>
            </tr>
             <? } ?>
    </tbody>
</table>
<? } else { ?> 
<div class="well well-sm" style="margin:20px;">No visas were found</div>
<? } ?> 

