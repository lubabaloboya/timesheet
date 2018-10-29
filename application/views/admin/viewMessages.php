<? 
    $message = new model_messages();
    $messages = $message->getAllMessages($this->user->ID);
    if(count($messages) > 0) {?>
<script>
$(document).ready(function() {
    $(".table").tablesorter({ 
        headers: { 
            0: {sorter: false},
            1: {sorter: false},
            2: {sorter: false}
        }
    });
        
    //Delete Message
    $(".delete-inbox-message").click(function(e) {
        if(!$(e.currentTarget).hasClass("disabled")) {
            messages.confirm("Are you sure you want to delete "+tabsHandler2.getTitle(e), deleteInboxMessage, e);
        }
    });

});
    
function deleteInboxMessage(e) {
    var request = new isarray_request();
    request.init({
        elementID: "#messagesTable",
        url:"/admin/update-messenger",
        type:"delete",
        action: "messages",
        id: $(e.currentTarget).attr("data-value"),
        loading:true,
        success: function(data) {
            if(data.status == true) {
                updateInbox();
            } else {
                messages.applicationMessage(data.message, "danger");
            }
        }
    });
}
</script>
<table rules="all" class="database table table-striped tablesorter table-hover">
    <thead>
        <tr>
            <th class='table-icon'></th>
            <th class='table-icon'></th>
            <th>Date</th>
            <th>From</th>
            <th>Title</th>
        </tr>
    </thead>
    <tbody>
        <? 
            foreach($messages as $v) {
                $row = new model_messages($v["messageID"]);
                $status = $row->getStatus($this->user->ID, $row->ID);
                if($status <= 2) {
                    $cls = "success";
                } else if($status == 3) {
                    $cls = "";
                }
        ?>
            <tr class="<? echo $cls ?>">
                <td class="table-icon" >
                    <? 
                        $icon = new library_decorators_tableIcon();
                        $icon->isDisabled = 1;
                        $icon->addClass(array("view", "read-message"));
                        $icon->addAttributes(array(
                            "url"=>"/admin/index",
                            "action"=>"messages"
                        ));
                        $icon->value = $row->ID;
                        $icon->tooltip = array(
                            "title"=>"View message",
                            "place"=>"bottom"
                        );
                        $icon->icon = "search";
                        echo $icon->getElement();
                    ?>
                </td>
                <td class="table-icon">
                    <? 
                        $icon = new library_decorators_tableIcon();
                        $icon->isDisabled = 1;
                        $icon->addClass(array("remove", "remove-icon"));
                        $icon->addAttributes(array(
                            "url"=>"/admin/index",
                            "action"=>"messages"
                        ));
                        $icon->value = $row->ID;
                        $icon->tooltip = array(
                            "title"=>"Remove this message from the database",
                            "place"=>"bottom"
                        );
                        $icon->icon = "remove";
                        echo $icon->getElement();
                    ?>
                </td>
                <td><? 
                    echo $row->created->format("d-m-Y H:i");
                ?></td>
                <td><? 
                    if(isset($row->ownerID)) {
                        $user = new model_user($row->ownerID);
                        echo ucwords($user->name . " " . $user->surname);
                    } else {
                        echo "Application Message";
                    }
                ?></td>
                <td class="tab-title">
                    <? echo $row->title ?>
                </td>
            </tr>
        <?
            }
        ?>
    </tbody>
</table>
<? } else { ?> 
<div class="well well-sm" style="margin:20px;">No messages were found</div>
<? } ?> 