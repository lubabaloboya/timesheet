<?php

class model_profile extends model_user {
    
    protected $_controller = "admin";
    protected $_action = "profile";
    protected $_table = "users";
    protected $_ref = "userID";

    function __construct($id = null) {
        parent::__construct($id);
        $this->setUp();
    }
}