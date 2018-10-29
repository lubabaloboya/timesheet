<?php

interface interface_navigation {
    

    function __construct($menuType);
    public function renderMenu(model_user $user);
}
?>
