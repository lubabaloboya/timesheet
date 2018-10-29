<? if(count($this->rows) > 0) { ?>
<script>
    $(document).ready(function() {

        $(".table").tablesorter({ 
                // pass the headers argument and assing a object 
            headers: { 
                // assign the secound column (we start counting zero) 
                0: {sorter: false}, 
                1: {sorter: false}, 
                2: {sorter: false}, 
                3: {sorter: false} 
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
            var value = $("#userRoleID").val();
            if(value == 4){
                $("#editUserForm .form-group").removeClass("hide");
            }else{
                $("#editUserForm .expatriate").addClass("hide");
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
            <th class='table-icon'></th>
            <th>Name</th>
            <th>Surname</th>
            <th>Username</th>
            <th>Role</th>
            <th>Last Login</th>
        </tr>
    </thead>
    <tbody>
        <? 
            foreach($this->rows as $v) {
                $row = new model_user($v["userID"]);
                $role = new model_role($row->roleID);
        ?>
            <tr>
                <td class="table-icon">
                    <?
                        $db = new database();
                        $this->request->updateRequestLog();
                        if($db->rows("SELECT * FROM request_log WHERE userID=?", "i", array($row->ID), true) == 1) {
                            echo '<span style="color:green" class="glyphicon glyphicon-user tool-tip" data-toggle="tooltip" data-placement="bottom" title="User: '.$row->username.' is online"/>';
                        } else {
                            echo '<span class="glyphicon glyphicon-user tool-tip" data-toggle="tooltip" data-placement="bottom" title="User: '.$row->username.' is offline"/>';
                        } 
                    ?>
                </td>
                <td class="table-icon">
                    <? 
                        $icon = new library_decorators_tableIcon();
                        $icon->isDisabled = $this->crud->read;
                        $icon->addClass(array("view", "view-icon"));
                        $icon->addAttributes(array(
                            "url"=>"/admin/index",
                            "action"=>"user",
                            "id"=>"viewUser"
                        ));
                        $icon->value = $row->ID;
                        $icon->tooltip = array(
                            "title"=>"View user in more detail",
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
                            "url"=>"/admin/index",
                            "formType"=>"edit",
                            "action"=>"user"
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
                            "action"=>"user"
                        ));
                        $icon->value = $row->ID;
                        $icon->tooltip = array(
                            "title"=>"Delete user",
                            "place"=>"bottom"
                        );
                        $icon->icon = "remove";
                        echo $icon->getElement();
                    ?>
                </td>
                <td class="tab-title"><? echo $row->name ?></td>
                <td><? echo $row->surname ?></td>
                <td><? echo $row->username ?></td>
                <td><? echo $role->name ?></td>
                <td><? 
                    if(isset($row->lastLogin)) {
                        echo $row->lastLogin->format("d-m-Y H:i");
                    } else {
                        echo "N/A";
                    }
                ?></td>
            </tr>
        <?
            }
        ?>
    </tbody>
</table>
<? } else { ?> 
<div class="well well-sm" style="margin:20px;">No users were found</div>
<? } ?> 