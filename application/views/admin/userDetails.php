<li class="list-group-item">
    <span class='list-item-header'>Name:</span> <? echo ucwords($this->model->name); ?>
</li>
<li class="list-group-item">
    <span class='list-item-header'>Surname:</span> <? echo ucwords($this->model->surname); ?>
</li>
<li class="list-group-item">
    <span class='list-item-header'>Username:</span> <? echo $this->model->username; ?>
</li>
<li class="list-group-item">
    <span class='list-item-header'>Email:</span> <? echo $this->model->email; ?>
</li>
<li class="list-group-item">
    <span class='list-item-header'>Status:</span>
        <?
            if($this->model->status === 1) {
                echo '<span style="color:#4b8e0b; font-weight:bold;">Active</span>';
            } else {
                echo '<span style="color:firebrick; font-weight:bold;">Blocked</span>';
            }
        ?>
</li>
<li class="list-group-item">
    <span class='list-item-header'>Role:</span>
    <?
        $role = new model_role($this->model->roleID);
        echo ucwords($role->name);
    ?>
</li>
<? if(isset($this->model->memberTypeID)) { ?>
<li class="list-group-item">
    <span class='list-item-header'>Membership Type:</span>
    <?
        $type = new model_memberType($this->model->memberTypeID);
        echo ucwords($type->name);
    ?>
</li>
<?}?>
<li class="list-group-item">
    <span class='list-item-header'>Last Login:</span>
    <?
        if(isset($this->model->lastLogin)) {
            echo $this->model->lastLogin->format("d-m-Y H:i");
        } else {
            echo "User has not yet logged in";
        }
    ?>
</li>
<li class="list-group-item">
    <span class='list-item-header'>Date Modified:</span>
    <?
        if(isset($this->model->modifiedDate)) {
            echo $this->model->modifiedDate->format("d-m-Y H:i");
        } else {
            echo "Item has not yet been modified";
        }
    ?>
</li>
<li class="list-group-item">
    <span class='list-item-header'>Modified By:</span>       
    <?
        if(isset($this->model->modifiedBy)) {
            $user = new model_user($this->model->modifiedBy);
            echo $user->username;
        } else {
            echo "Item has not yet been modified";
        }
    ?>
</li>