<?php

class model_importVisas extends model {
    
    public $errors = [];
    
    public $csv = [];
    
    public $fields = array(
        "name",
        "userSurname",
        "username",
        "userEmail",
        "HomeCountry",
        "HostCountry",
        "expatriatePassportNumber",
        "expatriatePassportExpiryDate",
        "expatriateJobTitle",
        "expatriateJobDescription",
        "type",
        "visaDateExpiry",
        "adminUsername"
    );

    function __construct($file, $delimiter) {
        parent::__construct();
        $this->moveFile($file);
        $this->csv = $this->parseCSV($delimiter);
    }
    
    function expatNotExists($passport_number, $row) {
        $result = $this->_db->rows("SELECT expatID FROM expatriates WHERE expatriatePassportNumber=?", "s", array($passport_number));
        if($result === 1) {
            $this->errors[] = "An expat with this passport number \"" . $passport_number . "\" already exists" . ' at row ' . $row;   
        }
    }
    
    function countryExists($country_name, $row) {
        $result = $this->_db->rows("SELECT countryID FROM countries WHERE countryName=?", "s", array($country_name));
        if($result === 0) {
            $this->errors[] = 'We were unable to locate the country ' . $country_name . ' at row ' . $row;
        }
    }
    
    function jobTitleExists($job_title, $row) {
        $list = new model_dropDownList();
        $arr = $list->getSpecificList("expatriateJobTitle");
        foreach($arr as $val) {
            if($val[1] == $job_title ) {
                return true;
            }
        }
        
        $this->errors[] = "We cant find the job title " . $job_title . ' at row ' . $row;  
    }
    
    function visaTypeExists($type, $row) {
        $result = $this->_db->rows("SELECT visaTypeID FROM visa_types WHERE visaTypeName=?", "s", array($type));
        if($result === 0) {
            $this->errors[] = 'We were unable to locate the visa type "' . $type . '" at row ' . $row;  
        }
    }
    
    function adminExists($username, $row) {
        $result = $this->_db->rows("SELECT userID FROM users WHERE username=?", "s", array($username));
        if($result === 0) {
            $this->errors[] = 'We were unable to locate the admin ' . $username . ' at row ' . $row; 
        }
    }
    
    
    function checkDate($date, $row) {

        if((DateTime::createFromFormat('Y-m-d', $date) === false)) {
            $this->errors[] = 'Date format must be yyyy-mm-dd, we found ' . $date . ' at row ' . $row; 
        }
    }
    
    function getCountry($country_name) {
        $sql = "SELECT countryID FROM countries WHERE countryName=?";
        $result = $this->_db->fetch_all_stmt($sql, "s", array($country_name), true);
        return $result["countryID"];
    }
    
    function getJobTitle($job_title) {
        $list = new model_dropDownList();
        $arr = $list->getSpecificList("expatriateJobTitle");

        foreach($arr as $val) {
            if($val[1] == $job_title ) {
                $id = $val[0];
            }
        }
        
        return $id;
    }
    
    function getAdmin($username) {
        $result = $this->_db->fetch_all_stmt("SELECT userID FROM users WHERE username=?", "s", array($username), true);
        return $result["userID"];
    }
    
    function getVisaTypeID($visa_type) {
        $sql = "SELECT visaTypeID FROM visa_types WHERE visaTypeName=?";
        $result = $this->_db->fetch_all_stmt($sql, "s", array($visa_type), true);
        return $result["visaTypeID"];
    }
    
    function insertUser($company_id, $data) {
        $u = new model_user();
        $user = $this->extractor($data, 'user');
        $user["name"] = $data["name"];
        $user["userCompanyID"] = $company_id;
        $user["userPassword"] = $u->createPassword();
        $user["userStatus"] = 1;
        $user["userRoleID"] = 4; // Found in the roles table
        
        if($this->_db->insert($user, "users") === true) {
            $user_id = $this->_db->insert_id;
            // Insert welcome email here if needed
            
            return $user_id;
        }
        
    }
    
    function insertExpat($userID, $data) {
        $expatriate = $this->extractor($data, 'expat');
        $expatriate["expatriateUserID"] = $userID;
        $expatriate["expatriateHomeCountryID"] = $this->getCountry($data["HomeCountry"]);
        $expatriate["expatriateHostCountryID"] = $this->getCountry($data["HostCountry"]);
        $expatriate["expatriateJobTitle"] = $this->getJobTitle($data["expatriateJobTitle"]);
        if($this->_db->insert($expatriate, "expatriates") === true) {
            return $this->_db->insert_id;
        }
    }
    
    function insertVisa($expatID, $data) {
        $visa = $this->extractor($data, 'visa');
        $visa["visaExpatriateID"] = $expatID;
        $visa["visaStatus"] = 9;
        $visa["visaVisaTypeID"] = $this->getVisaTypeID($data["type"]);
        $date = new DateTime($visa["visaDateExpiry"]);
        $visa["visaDateExpiry"] = $date->format("Y-m-d");
        $visa["visaCreatedBy"] = $this->getAdmin($data["adminUsername"]);

        $visa_model = new model_visa();
        if($id = $visa_model->add($visa)) {
            return $id;
        }
    }
    
    function updateDocumentation($visaID) {
        $this->_db->update(array(
            "visaDocumentationNotRequired" => 1
        ), "visa_documentation", "visaDocumentationVisaID", $visaID);
    }
    
    
    function addComment($visaID) {
        $user = new model_user();
        $user->getCurrentUser();
        $this->_db->insert(array(
            "visaCommentUserID" => $user->ID,
            "visaCommentVisaID" => $visaID,
            "visaCommentText" => "This visa was added via the company multiple visa upload"
        ), "visa_comments");
    }
    
    function moveFile($file) {

        $image = new uploader($file);
        $image->basePath = ADMIN_PATH . "/uploads/";
        $image->newFilename = 'visa_upload' . $image->getExtension();
        $image->transferFile();
        
    }
    
    function countColumns($line, $columns, $row) {
        foreach($this->csv as $line) {
            if(count($line) !== $columns) {
                throw new userException("Your colum count is incorrect you might have the wrong delimiter at row " . $row);
            }

        }
    }
    
    function parseCSV($delimiter) {
        
        $file = file_get_contents(ADMIN_PATH . '/uploads/visa_upload.csv');
        $lines = str_getcsv($file, "\n\r");
        
        foreach($lines as $k=>$line) {
            $lines[$k] = str_getcsv($line, $delimiter);
            $array = array();
            
            //Insert as keys into the array the fields name
            foreach($lines[$k] as $key=>$val) {
                $array[$this->fields[$key]] = $val;
            }
            
            $lines[$k] = $array;
        }

        return  array_splice($lines, 1);;
    }
    
    function validateCSV() {
        
        if(count($this->csv) > 0) {
            $row = 1;
            $this->countColumns($this->csv, 13, $row);
            
            foreach($this->csv as $line) {

                $this->adminExists($line["adminUsername"], $row);
                $this->countryExists($line["HomeCountry"], $row);
                $this->countryExists($line["HostCountry"], $row);
                $this->expatNotExists($line["expatriatePassportNumber"], $row);
                $this->jobTitleExists($line["expatriateJobTitle"], $row);
                $this->visaTypeExists($line["type"], $row);
                $this->checkDate($line["visaDateExpiry"], $row);
                
                $row++;
            }

            return empty($this->errors) ? true : false;
        } else {
            throw userException("Visa upload file is empty");
        }
    }
    
    function import($companyID) {

        foreach($this->csv as $line) {
            $userID = $this->insertUser($companyID, $line);
            $expatID = $this->insertExpat($userID, $line);
            $visaID = $this->insertVisa($expatID, $line);
            $this->updateDocumentation($visaID);
            $this->addComment($visaID);
        }

    }
        
}