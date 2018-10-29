<? 
    $comments = $this->model->getComments();
    if(count($comments) > 0) { ?>
<table class="table table-striped table-hover table-sorter">
    <thead>
        <tr>
            <th class='table-icon'></th>
            <th class='table-icon'></th>
            <th>User</th>
            <th>Comment</th>
            <th>Created</th>
        </tr>
    </thead>
    <tbody>
        <?
            foreach($comments as $comment) { ?>

         <tr>
            <td class="table-icon"><?
                $icon = new library_decorators_tableIcon();
                $icon->addClass(array("edit-visa-comment", "edit"));
                $icon->icon = "pencil";
                $icon->isDisabled = $this->crud->update;
                $icon->tooltip = "Update this comments details";
                $icon->value = $comment->ID;
                echo $icon->getElement();
            ?></td>
            <td class="table-icon"><?
                $icon = new library_decorators_tableIcon();
                $icon->addClass(array("delete-visa-comment", "remove"));
                $icon->icon = "remove";
                $icon->isDisabled = $this->crud->delete;
                $icon->tooltip = "Remove this comment from the database";
                $icon->value = $comment->ID;
                echo $icon->getElement();
            ?></td>
            <td class="tab-title"><? 
                $user = new model_user($comment->userID);
                echo ucwords($user->name . " ". $user->surname); 
            ?></td>
            <?
                $cell = new library_decorators_tableCell();
                $cell->strLenTotal = 50;
                $cell->text = $comment->text;
                echo $cell->getElement();
            ?>
            <td><? 
                echo $comment->dateCreated->format("d-m-Y");
            ?></td>
        </tr>
        <?  }?>
    </tbody>
</table>
<? } else { ?>
<div class="well well-sm">No comments types were found</div>
<?}?>