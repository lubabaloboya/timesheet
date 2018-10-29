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
});
</script>
<table rules="all" class="database table table-striped table-hover tablesorter">
    <thead>
        <tr>
            <th class='table-icon'></th>
            <th class='table-icon'></th>
            <th class='table-icon'></th>
            <th>Name</th>
        </tr>
    </thead>
    <tbody>
        <? 
            foreach($this->rows as $v) {
                $row = new model_visaType($v["visaTypeID"]);
        ?>
            <tr>
                <td class="table-icon">
                    <? 
                        $icon = new library_decorators_tableIcon();
                        $icon->isDisabled = $this->crud->read;
                        $icon->addClass(array("view", "view-icon"));
                        $icon->addAttributes(array(
                            "url"=>"/immigration/index",
                            "action"=>"visaType",
                            "id"=>"viewVisaType"
                        ));
                        $icon->value = $row->ID;
                        $icon->tooltip = array(
                            "title"=>"View this visa type in more detail",
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
                            "action"=>"visaType"
                        ));
                        $icon->tooltip = array(
                            "title"=>"Edit visa type",
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
                            "action"=>"visaType"
                        ));
                        $icon->value = $row->ID;
                        $icon->tooltip = array(
                            "title"=>"Delete visa type",
                            "place"=>"bottom"
                        );
                        $icon->icon = "remove";
                        echo $icon->getElement();
                    ?>
                </td>
                <td class="tab-title"><? echo $row->name ?></td>
            </tr>
        <?
            }
        ?>
    </tbody>
</table>
<? } else { ?> 
<div class="well well-sm" style="margin:20px;">No visa types were found</div>
<? } ?> 
