<?php

class model_configuration extends model {

    protected $_table = "configurations";
    protected $_ref = "configurationName";
    
    function __construct($id = null) {
        parent::__construct($id);
        $this->setUp();
    }

    function __get($name) {
        $result = $this->_db->fetch_all_stmt("SELECT configurationValue FROM ".$this->_table." WHERE ".$this->_ref."=?", "s", array($name), true);
        
        if(isset($result["configurationValue"])) {
            return $result["configurationValue"];
        } else {
            throw new exception("This database field does not exists");
        }
    }
}