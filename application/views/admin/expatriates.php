<? if(count($this->rows) > 0) { ?>
<script>
    $(document).ready(function() {
        $(".table").tablesorter({ 
                // pass the headers argument and assing a object 
            headers: { 
                // assign the secound column (we start counting zero) 
                0: {sorter: false}, 
                1: {sorter: false}, 
                2: {sorter: false}
            } 
        }); 
        
        $("body").off("change", "#userRoleID");
        $("body").on("change", "#userRoleID", function() {
            if($(this).val() == 3) {
                $("#userMemberTypeID").removeAttr("disabled");
            } else {
                $("#userMemberTypeID").val("");
                $("#userMemberTypeID").attr("disabled", "disabled");
            }
        });
     });
    
</script>
<table rules="all" class="database table table-striped table-hover tablesorter">
    <thead>
        <tr>
            <th class='table-icon'></th>
            <th class='table-icon'></th>
            <th class='table-icon'></th>
            <th>Name</th>
            <th>Company</th>
            <th>Passport</th>
            <th>Home</th>
            <th>Job</th>
        </tr>
    </thead>
    <tbody>
        <? 
            foreach($this->rows as $v) {

                $user = new model_user($v["userID"]);
                
                $xpat = new model_expatriate();
                $xpat->getExpatriate($user->ID);
               
        ?>
            <tr>
                <td class="table-icon">
                    <? 
                        $icon = new library_decorators_tableIcon();
                        $icon->isDisabled = $this->crud->read;
                        $icon->addClass(array("view", "view-icon"));
                        $icon->addAttributes(array(
                            "url"=>"/admin/index",
                            "action"=>"expatriate",
                            "id"=>"viewExpatriate"
                        ));
                        $icon->value = $xpat->ID;
                        $icon->tooltip = array(
                            "title"=>"View expatriate in more detail",
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
                        $icon->value = $xpat->ID;
                        $icon->addAttributes(array(
                            "url"=>"/admin/index",
                            "formType"=>"edit",
                            "action"=>"expatriate"
                        ));
                        $icon->tooltip = array(
                            "title"=>"Edit user",
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
                            "url"=>"/admin/index",
                            "action"=>"expatriate"
                        ));
                        $icon->value = $user->ID;
                        $icon->tooltip = array(
                            "title"=>"Delete expatriate",
                            "place"=>"bottom"
                        );
                        $icon->icon = "remove";
                        echo $icon->getElement();
                    ?>
                </td>
                <td class="tab-title"><? echo $user->name . " " , $user->surname ?></td>
                <td><?
                    $company = new model_company($user->companyID);
                    echo $company->name;
                ?></td>
                <td><?
                    echo $xpat->passportNumber;
                ?></td>
                <td><?
                    $country = new model_country($xpat->homeCountryID);
                    echo $country->name;
                ?></td>
                <td><?
                    $list = new model_dropDownList("expatriateJobTitle");
                    echo $list->getListItemName($xpat->jobTitle);
                ?></td>
            </tr>
        <?
            }
        ?>
    </tbody>
</table>
<? } else { ?> 
<div class="well well-sm" style="margin:20px;">No expatriates were found</div>
<? } ?> 
