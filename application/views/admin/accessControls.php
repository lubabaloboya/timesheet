<? if(count($this->rows) > 0) { ?>
<script>
    $(document).ready(function() {
        $(".table").tablesorter({ 
        // pass the headers argument and assing a object 
        headers: { 
            0: {sorter: false},
            1: {sorter: false}
        } 
    }); 
});
    
</script>
<table rules="all" class="database table table-striped tablesorter table-hover">
    <thead>
        <tr>
            <th class="table-icon"></th>
            <th class="table-icon"></th>
            <th>Category</th>
            <th>Page</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <? 
            foreach($this->rows as $v) {
                $row = new model_accessControl($v["accessControlID"]);
                $user = new model_user();
                $user->getCurrentUser();
                
                if($user->roleID !== 1) {
                    $read = 0;
                    $update = 0;
                } else {
                    $read = $this->crud->read;
                    $update = $this->crud->update;
                }
        ?>
            <tr>
                <td class="table-icon">
                    <? 
                        $icon = new library_decorators_tableIcon();
                        $icon->isDisabled = $this->crud->update;
                        $icon->addClass(array("view", "view-icon"));
                        $icon->addAttributes(array(
                            "url"=>"/admin/index",
                            "action"=>"accessControl"
                        ));
                        $icon->value = $row->ID;
                        $icon->tooltip = array(
                            "title"=>"View all the available access types and make changes to them here",
                            "place"=>"bottom"
                        );
                        $icon->icon = "new-window";
                        echo $icon->getElement(); 
                    ?>
                </td>
                <td class="table-icon">
                    <? 
                        $icon = new library_decorators_tableIcon();
                        $icon->isDisabled = $update;
                        $icon->addClass(array("edit", "edit-icon"));
                        $icon->addAttributes(array(
                            "url"=>"/admin/index",
                            "formType"=>"edit",
                            "action"=>"accessControl",
                            "subType"=>"status"
                        ));
                        $icon->value = $row->ID;
                        $icon->tooltip = array(
                            "title"=>"Edit role name",
                            "place"=>"bottom"
                        );
                        $icon->icon = "pencil";
                        echo $icon->getElement();
                    ?>
                </td>
                <td>
                    <? echo ucwords($row->controller) ?>
                </td>
                <td class="tab-title">
                    <? echo ucwords(str_replace("-", " ", $row->action)) ?>
                </td>
                <td>
                    <?
                        if($row->status == 1) {
                            echo '<span style="color:#4b8e0b; font-weight:bold;" class="glyphicon glyphicon-ok"></span>';
                        } else {
                            echo '<span style="color:firebrick; font-weight:bold;" class="glyphicon glyphicon-remove"></span>';
                        }
                    ?>
                </td>
            </tr>
        <?
            }
        ?>
    </tbody>
</table>
<? } else { ?> 
<div class="well well-sm" style="margin:20px;">No access controlled pages were found</div>
<? } ?> 