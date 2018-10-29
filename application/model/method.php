<?php

class model_method extends model {

    public $ID;
    public $name;
    public $acronym;
    
    protected $_table = "methods";
    protected $_ref = "methodID";
    
    function __construct($id = null) {
        parent::__construct($id);
        $this->setUp();
    }
    
    public function setUp() {
        $this->ID       = $this->result["methodID"];
        $this->name     = $this->result["methodName"];
        $this->acronym  = $this->result["methodAcronym"];
    }

}
