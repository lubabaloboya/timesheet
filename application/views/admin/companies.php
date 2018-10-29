<? if(count($this->rows) > 0) { ?>
<script>
$(document).ready(function() {
    $(".table").tablesorter({ 
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
            <th class='table-icon'></th>
            <th class='table-icon'></th>
            <th class='table-icon'></th>
            <th>Name</th>
            <th>Contact</th>
        </tr>
    </thead>
    <tbody>
        <? 
            foreach($this->rows as $v) {
                $row = new model_company($v["companyID"]);
        ?>
            <tr>
                <td class="table-icon" >
                    <? 
                        $icon = new library_decorators_tableIcon();
                        $icon->isDisabled = $this->crud->read;
                        $icon->addClass(array("view", "view-icon"));
                        $icon->addAttributes(array(
                            "url"=>"/admin/index",
                            "action"=>"company",
                            "id"=>"viewCompany"
                        ));
                        $icon->value = $row->ID;
                        $icon->tooltip = array(
                            "title"=>"View this companies' details",
                            "place"=>"bottom"
                        );
                        $icon->icon = "new-window";
                        echo $icon->getElement();
                    ?>
                </td>
                <td class="table-icon" >
                    <? 
                        $icon = new library_decorators_tableIcon();
                        $icon->isDisabled = $this->crud->update;
                        $icon->addClass(array("edit", "edit-icon"));
                        $icon->addAttributes(array(
                            "url"=>"/admin/index",
                            "formType"=>"edit",
                            "action"=>"company"
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
                <td class="table-icon">
                    <? 
                        $icon = new library_decorators_tableIcon();
                        $icon->isDisabled = $this->crud->delete;
                        $icon->addClass(array("remove", "remove-icon"));
                        $icon->addAttributes(array(
                            "url"=>"/admin/index",
                            "action"=>"company"
                        ));
                        $icon->value = $row->ID;
                        $icon->tooltip = array(
                            "title"=>"Remove this role from the database",
                            "place"=>"bottom"
                        );
                        $icon->icon = "remove";
                        echo $icon->getElement();
                    ?>
                </td>
                <td class="tab-title">
                    <? echo ucwords($row->name) ?>
                </td>
                <td>
                    <? echo ucwords($row->contactNumber) ?>
                </td>
            </tr>
        <?
            }
        ?>
    </tbody>
</table>
<? } else { ?> 
<div class="well well-sm" style="margin:20px;">No companies were found</div>
<? } ?> 
