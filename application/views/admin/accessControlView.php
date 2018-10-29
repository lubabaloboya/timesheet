<table id="accessControlView" rules="all" class="database table table-striped table-hover">
    <thead>
        <tr>
            <th>Role</th>
            <th>Access</th>
            <th>Create</th>
            <th>Read</th>
            <th>Update</th>
            <th>Delete</th>
        </tr>
    </thead>
    <tbody>
        <?
            foreach($this->model->getAccessArrays() as $v) {
            $roles = new model_role($v["roleID"]); 
            $access = json_decode($v["accessRights"]);
            $ac = new model_accessControl($v["accessControlID"]);
        ?>

        <tr>
            <td>
                <? echo $roles->name ?>
            </td>
            <td>
                <? 
                    if($access->access == 1) {
                        echo '<input type="checkbox" checked="checked" id="'.str_replace("-", "_",$ac->action).'-access-'.$roles->ID.'" class="access"><label for="'.str_replace("-", "_",$ac->action).'-access-'.$roles->ID.'"></label>';
                    } else {
                        echo '<input type="checkbox" id="'.str_replace("-", "_",$ac->action).'-access-'.$roles->ID.'" class="access"><label for="'.str_replace("-", "_",$ac->action).'-access-'.$roles->ID.'"></label>';
                    }
                ?>
            </td>
            <td>
                <? 
                    if($access->create == 1 && $access->access == 1) {
                        echo '<input type="checkbox" checked="checked" id="'.str_replace("-", "_",$ac->action).'-create-'.$roles->ID.'"><label for="'.str_replace("-", "_",$ac->action).'-create-'.$roles->ID.'"></label>';
                    } else {
                        echo '<input type="checkbox" id="'.str_replace("-", "_",$ac->action).'-create-'.$roles->ID.'"><label for="'.str_replace("-", "_",$ac->action).'-create-'.$roles->ID.'"></label>';
                    }
                ?>
            </td>
            <td>
                <? 
                    if($access->read == 1 && $access->access == 1) {
                        echo '<input type="checkbox" checked="checked" id="'.str_replace("-", "_",$ac->action).'-read-'.$roles->ID.'"><label for="'.str_replace("-", "_",$ac->action).'-read-'.$roles->ID.'"></label>';
                    } else {
                        echo '<input type="checkbox" id="'.str_replace("-", "_",$ac->action).'-read-'.$roles->ID.'"><label for="'.str_replace("-", "_",$ac->action).'-read-'.$roles->ID.'"></label>';
                    }
                ?>
            </td>
            <td>
                <? 
                    if($access->update == 1 && $access->access == 1) {
                        echo '<input type="checkbox" checked="checked" id="'.str_replace("-", "_",$ac->action).'-update-'.$roles->ID.'"><label for="'.str_replace("-", "_",$ac->action).'-update-'.$roles->ID.'"></label>';
                    } else {
                        echo '<input type="checkbox" id="'.str_replace("-", "_",$ac->action).'-update-'.$roles->ID.'"><label for="'.str_replace("-", "_",$ac->action).'-update-'.$roles->ID.'"></label>';
                    }
                ?>
            </td>
            <td>
                <? 
                    if($access->delete == 1 && $access->access == 1) {
                        echo '<input type="checkbox" checked="checked" id="'.str_replace("-", "_",$ac->action).'-delete-'.$roles->ID.'"><label for="'.str_replace("-", "_",$ac->action).'-delete-'.$roles->ID.'"></label>';
                    } else {
                        echo '<input type="checkbox" id="'.str_replace("-", "_",$ac->action).'-delete-'.$roles->ID.'"><label for="'.str_replace("-", "_",$ac->action).'-delete-'.$roles->ID.'"></label>';
                    }
                ?>
            </td>
        </tr>

        <?}
        ?>
    </tbody>
</table>