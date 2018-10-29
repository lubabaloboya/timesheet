<?php

class model_visaCompanyDocumentation extends model_companyDocumentation {
    
    public $ID;
    public $companyID;
    public $name;
    public $extension;
    public $dateUploaded;
    
    protected $_table = "company_documentation";
    protected $_ref = "companyDocumentationID";
    protected $_controller = "immigration";
    protected $_action = "visa-company-documentation";

    function __construct($id = null) {
      parent::__construct($id);
  }
}
    