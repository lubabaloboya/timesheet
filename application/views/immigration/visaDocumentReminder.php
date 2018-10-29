<? 
    $reminders = $this->reminders->getDocumentationWithReminders();
    if(count($reminders) > 0) { ?>
        <table rules="all" class="database table table-striped table-hover tablesorter">
    <thead>
        <tr>
            <th class='table-icon'></th>
            <th class='table-icon'></th>
            <th>Document Name</th>
            <th>Visa Name</th>
            <th>Reminder Second Date</th>
            <th>Reminder Second Date</th>
            <th>Consultant</th>
        </tr>
    </thead>
    <tbody>
        <?
            foreach($reminders as $reminder) {
        ?>
            <tr>
               
                <td class="table-icon">
                
                    <? 
                        $icon = new library_decorators_tableIcon();
                        $icon->addClass(array("edit", "edit-icon"));
                        $icon->value = $reminder['documentReminderID']; 

                        $tooltip = "Add document reminder";

                        $icon->isDisabled = $this->crud->update;
                        $icon->addAttributes(array(
                            "url"=>"/immigration/index",
                            "formType"=>"edit",
                            "action"=>"documentReminder"
                        ));
                        $icon->tooltip = array(
                                "title"=>$tooltip,
                                "place"=>"create"
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
                            "action"=>"documentReminder"
                        ));
                        $icon->value = $reminder['documentReminderID'];
                        $icon->tooltip = array(
                            "title"=>"Remove this reminder",
                            "place"=>"bottom"
                        );
                        $icon->icon = "remove";
                        echo $icon->getElement();
                    ?>
                </td>
                <td><? echo $reminder['visaTypeName'];?></td>
                <td><? echo $reminder['visaDocumentationTypeName'];?></td>
                <td><? echo $reminder['documentReminderFirstDate'];?></td>
                <td><? echo $reminder['documentReminderSecondDate'];?></td>
                <td>
                    <? 
                        echo $this->userObj->getFullNameByUserId($reminder['documentReminderCreatedBy']);
                    ?>
                </td>
            </tr>

        <?  }?>
    </tbody>
</table>
<? } else { ?>
<div class="well well-sm" style="margin:20px;">No document reminders were found</div>
<?}?>