<?php


class model_country extends model {
    
    public $ID;
    public $name;
    public $status;

    protected $_table = "countries";
    protected $_controller = "admin";
    protected $_ref = "countryID";
    protected $_action = "country";
    
    function __construct($id = null) {
        parent::__construct($id);
        $this->setUp();
    }
    
    function setUp() {  
        $this->ID       = $this->result["countryID"];
        $this->name     = $this->result["countryName"];
        $this->status   = $this->result["countryStatus"];
    }

    function getCountries() {

        $sql =  $this->_db->fetch_all_stmt("SELECT countryID, countryName FROM `countries` WHERE countryStatus = 1");

        $directory = PUBLIC_PATH . "/images/countries";
        $images = scandir($directory);

        foreach ($sql as $key => $value) {
            $sql[$key]["image"] = [];
            foreach($images as $image) {
                if($image !== '.' && $image !== '..' ) {
                    if ($image == $value["countryID"]) {
                        $sql[$key]["image"] = $image;  
                    }
                }
            }
        }

        return $sql;
    }

    function getCountryVisaType($countryID) {
        $result = $this->_db->fetch_all_stmt("SELECT * FROM visa_types WHERE visaCountryID=?", "i", array($countryID));
        $array = array();
        
        if(is_array($result)) {
            foreach($result as $types) {
                $array[] = $types;
            }
            return $array;
        } else {
            return false;
        }
    }

    function read(request $request) {
        if($request->subType == "default") {
            $array["page"] = "admin/countryDetails";
            return $array;
        }
    }
}