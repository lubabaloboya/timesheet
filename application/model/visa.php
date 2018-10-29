<?php

class model_visa extends model {
    
    public $ID;
    public $expatriateID;
    public $visaTypeID;
    public $status;
    public $extension;
    public $dateExpiry;
    public $dateCreated;
    public $dateSubmitted;
    public $dateDeclined;
    public $dateAppointment;
    public $dateOnhold;
    public $dateCompleted;
    public $createdby;
    
    public $statuses;
    
    protected $_table = "visas";
    protected $_ref = "visaID";
    protected $_controller = "immigration";
    protected $_action = "visas";
            
    function __construct($id = null) {
        parent::__construct($id);
        $this->setUp();
    }
    
    function setUp() {
        $this->ID               = $this->result["visaID"];
        $this->expatriateID     = $this->result["visaExpatriateID"];
        $this->visaTypeID       = $this->result["visaVisaTypeID"];
        $this->countryID        = $this->result["visaCountryID"];
        $this->status           = $this->result["visaStatus"];
        $this->extension        = $this->result["visaExtension"];
        $this->createdby        = $this->result["visaCreatedBy"];
        $this->dateExpiry       = $this->dateSetup($this->result["visaDateExpiry"]);
        $this->dateCreated      = $this->dateSetup($this->result["visaDateCreated"]);
        $this->dateSubmitted    = $this->dateSetup($this->result["visaDateSubmitted"]);
        $this->dateDeclined     = $this->dateSetup($this->result["visaDateDeclined"]);
        $this->dateAppointment  = $this->dateSetup($this->result["visaDateAppointment"]);
        $this->dateOnhold       = $this->dateSetup($this->result["visaDateOnhold"]);
        $this->dateCompleted    = $this->dateSetup($this->result["visaDateCompleted"]);
        
        $this->statuses = [
            array(1, "Created"),
            array(2, "Awaiting Documents"),
            array(3, "Awaiting Documents"),
            array(4, "Awaiting Documents"),
            array(5, "Compiling"),
            array(6, "Compiled"),
            array(7, "Handed over"),
            array(8, "Submitted"),
            array(9, "Completed"),
            array(10, "Declined"),
            array(11, "Cancelled"),
            array(12, "Awaiting Documents"),
        ];
    }
    
    protected function addForm($request) {
        $array["path"] = "/forms/addVisaForm.js";
        $countries = $this->_db->fetch_numeric("SELECT countryID, countryName FROM countries ORDER BY countryName ASC");
        $array["list"] = array(
            "visaExpatriateID"=>$this->_db->fetch_numeric("SELECT expatriateID, CONCAT(userSurname, ', ', name) AS expatriateName FROM expatriates INNER JOIN users ON userID=expatriateUserID WHERE userStatus = 1 ORDER BY userSurname"),
            "visaVisaTypeID"=>$this->_db->fetch_numeric("SELECT visaTypeID, visaTypeName FROM visa_types"),
            "visaCountryID"=>$countries,
        );
        
        $array = $this->formRender($array);     
        return $this->updateForm($array, $request);
    }
    
    protected function editForm($request) {
        $form = new form(APPLICATION_PATH . "/forms/editVisaForm.js");
        
        $values = $this->_db->fetch_all_stmt("SELECT * FROM visas LEFT JOIN expatriates ON visaExpatriateID=expatriateID LEFT JOIN users ON userID=expatriateUserID WHERE visaID=?", "i", array($request->id), true);
        
        $form->setValues($values);

        $form->injectValues();
        $countries = $this->_db->fetch_numeric("SELECT countryID, countryName FROM countries ORDER BY countryName ASC");
        $form->addList(array(
            "visaExpatriateID"=>$this->_db->fetch_numeric("SELECT expatriateID, CONCAT(name , ' ', userSurname) AS expatriateName FROM expatriates INNER JOIN users ON userID=expatriateUserID WHERE userStatus = 1"),
            "visaVisaTypeID"=>$this->_db->fetch_numeric("SELECT visaTypeID, visaTypeName FROM visa_types"),
            "visaStatus"=>array_splice($this->statuses, 4),
            "visaCreatedBy"=>$this->_db->fetch_numeric("SELECT userID, name FROM users WHERE userRoleID IN (1, 2)"),
            "visaCountryID"=>$countries,
        ));
        
        if($values["visaStatus"] == 11){
            $form->removeGroupClass("hide", "visaDateOnhold");
            $form->addValidation("required", "visaDateOnhold", TRUE);
        }
        if($values["visaStatus"]  != 11){
            $form->removeValidation("visaDateOnhold", "required");
        }

        return $this->updateForm($form->getForm(), $request);
    }

    protected function uploadActualVisaForm(request $request) {   
        $array["path"] = "/forms/uploadActualVisaForm.js";
        $array = $this->formRender($array);           
        return $this->updateForm($array, $request);
    }
     
    function createForm(request $request) {
        if($request->subType == "default"){
            return $this->addForm($request);
        } else if($request->subType == "edit") {
            return $this->editForm($request);
        } else if($request->subType == "upload-actual-visa") {
            return $this->uploadActualVisaForm($request);
        }
    }
    
    function add($data) {
        
        if(!isset($data["visaCreatedBy"])) {
            $user = new model_user();
            $user->getCurrentUser();
            $data["visaCreatedBy"] = $user->ID;
        }
        
        if($this->_db->insert($data, "visas") === true) {
            $id = $this->_db->insert_id;

            $visa_docs = new model_visaDocumentation();
            $visa_docs->addVisaDocumentation(new model_visa($id));

            return $id;
        } else {
            return false;
        }
        
    }
    
    protected function createVisa(request $request) {
        $obj = $request->getDataObj();
        $filter = new filter(array(
            "required"=>array(
                array (
                    "Expatriate"=>$obj->visaExpatriateID,
                    "Visa Type"=>$obj->visaVisaTypeID
                )
            ),
            "number"=>array(
                array (
                    "Expatriate"=>$obj->visaExpatriateID,
                    "Visa Type"=>$obj->visaVisaTypeID
                )
            ),
            "maxLength"=>array(
                array(
                    "Expatriate"=>array($obj->visaExpatriateID, 5),
                    "Visa Type"=>array($obj->visaVisaTypeID, 5)
                )
            )
        ));
        
        if(empty($filter->errors)) {
            $data = $request->getDataArray();
            if($this->add($data)) {
                $array = array(
                    "status"=>true,
                    "message"=>"Your request was successful"
                );
            } else {
                $array = array(
                    "status"=>false,
                    "message"=>"Your request was unsuccessful"
                );
            }
            
        } else {
            $array = array(
                "status"=>false,
                "message"=>$filter->errors
            );
        }
        $request->addResponseItems($array);
        return $request->response();
    }
    
    
    
    function create(\request $request) {
        if($request->subType == "default") {
            return $this->createVisa($request);
        }
    }
    
    
    function readVisa(request $request) {
        $uploader = new uploader();
        $obj = $request->getDataObj();
        $newdoc = new model_visa($request->id);
        
        $path = ADMIN_PATH . "/documentation/" . $request->id ."/visa" . $newdoc->extension; 
        
        if(file_exists($path)) {
            if($filename = $uploader->moveFile($path)) {
                $array = array(
                    "status"=>true,
                    "href"=>"/tmp/".$filename
                );  
            } else {
                $array = array(
                    "status"=>false,
                    "message"=>"Were unable to move the file for viewing"
                );  
            }
        } else {
            $array = array(
                "status"=>false,
                "message"=>"File does not exists"
            );   
        }
        $request->addResponseItems($array);
        return $request->response();
    }
    
    function downloadReport(request $request, $type) {
        
        if( $type == "current" ) {
            $visas = $this->_db->fetch_all_stmt("SELECT * FROM visas INNER JOIN visa_types ON visaTypeID = visaVisaTypeID INNER JOIN expatriates ON visaExpatriateID=expatriateID INNER JOIN users as u1 ON expatriateUserID=u1.userID INNER JOIN companies ON companyID=userCompanyID WHERE visaStatus < 9 OR visaStatus > 11 ORDER BY visaStatus, visaID");
        } else if( $type == "completed" ){
            $visas = $this->_db->fetch_all_stmt("SELECT * FROM visas INNER JOIN visa_types ON visaTypeID = visaVisaTypeID INNER JOIN expatriates ON visaExpatriateID=expatriateID INNER JOIN users as u1 ON expatriateUserID=u1.userID INNER JOIN companies ON companyID=userCompanyID WHERE visaStatus = 9 ORDER BY visaStatus, visaID");
        }
 
        $uploader = new uploader();
        $this->exportVisasReport($visas);
        
        $path = ADMIN_PATH. "/reporting/report.csv";
        if(file_exists($path)) {
            if($filename = $uploader->moveFile($path)) {
                $array = array(
                    "status"=>true,
                    "href"=>"/tmp/".$filename
                );
            } else {
                $array = array(
                    "status"=>false,
                    "message"=>"We are unable to move the file"
                );
            }
        } else {
            $array = array(
                "status"=>false,
                "message"=>"This file does not exist, please try again"
            );
        }
        
        $request->addResponseItems($array);
        return $request->response();
    }
    
 function exportVisasReport($visas) {
        
        $csv = array(
            array(
                "Id",
                "Customer",
                "Expat Name",
                "Expat Surname",
                "Passport No",
                "Visa Type",
                "Date Added",
                "Date Submitted",
                "Date Appointment",
                "Status",
                "Admin"
            )
        );

        if(count($visas) > 0) {
            $k = 1;

            foreach($visas as $v) {
                
                $visa = new model_visa($v["visaID"]);
                $date_added = new DateTime($v["visaDateCreated"]);
                $date_submitted = new DateTime($v["visaDateSubmitted"]);
                $date_appointment = new DateTime($v["visaDateAppointment"]);
                $user = new model_user($v["visaCreatedBy"]);

                $csv[$k][0]     = $visa->getVisaID();
                $csv[$k][1]     = $v["companyName"];
                $csv[$k][2]     = $v["name"];
                $csv[$k][3]     = $v["userSurname"];
                $csv[$k][4]     = $v["expatriatePassportNumber"];
                $csv[$k][5]     = $v["visaTypeName"];
                $csv[$k][6]     = $date_added->format("d-m-Y");
                $csv[$k][7]     = $date_submitted->format("d-m-Y");
                $csv[$k][8]     = $date_appointment->format("d-m-Y");
                $csv[$k][9]     = $visa->getStatusName($v["visaStatus"]);
                $csv[$k][10]    = $user->name . " " . $user->surname;
                
                $k++;
            }
            
        }
        
        $fp = fopen(ADMIN_PATH . '/reporting/report.csv', 'w');

        foreach ($csv as $fields) {
            fputcsv($fp, $fields, ",");
        }

        fclose($fp);
    }
    
    
    function read(request $request) {
        switch($request->subType) {
            case 'default': $array["page"] = 'immigration/visaView';
                return $array;
            case 'view-actual-visa':  return $this->readVisa($request);
            case 'export-current-visas': return $this->downloadReport($request, "current");
            case 'export-completed-visas': return $this->downloadReport($request, "completed");
        }
    }
    
    function updateVisa(request $request) {
        $obj = $request->getDataObj();
        $filterArray = array(
            "minLength"=>array(
                array(
                    "Expatriate"=>array(@$obj->visaExpatriateID, 1),
                    "Visa Type"=>array(@$obj->visaVisaTypeID, 1),
                    "Status"=>array(@$obj->visaStatus, 1)
                )
            ),
            "number"=>array(
                array(
                    "Expatriate"=>@$obj->visaExpatriateID,
                    "Visa Type"=>@$obj->visaVisaTypeID,
                    "Status"=>@$obj->visaStatus
                )
            )
        );
        
        if(isset($obj->visaStatus)) {
            if($obj->visaStatus == $this->getStatusID("Submitted")) {
                $filterArray["required"][0]["Date Submitted"] = $obj->visaDateSubmitted;
            } else if($obj->visaStatus == $this->getStatusID("Declined")) {
                $filterArray["required"][0]["Date Declined"] = $obj->visaDateDeclined;
            }
        }
        
        $filter = new filter($filterArray);
        
        if(empty($filter->errors)) {
            $data = $request->getDataArray();
            
            if(isset($obj->visaStatus)) {
                if($obj->visaStatus == $this->getStatusID("Submitted") && isset($obj->visaDateSubmitted)) {
                    $date = new DateTime($data["visaDateSubmitted"]);
                    $data["visaDateSubmitted"] = $date->format("Y-m-d");
                } else if($obj->visaStatus == $this->getStatusID("Declined") && isset($obj->visaDateDeclined)) {
                    $date = new DateTime($data["visaDateDeclined"]);
                    $data["visaDateDeclined"] = $date->format("Y-m-d");
                }
            }
            
            if(isset($data["visaDateAppointment"])) {
                $appointment_date = new DateTime($data["visaDateAppointment"]);
                $data["visaDateAppointment"] = $appointment_date->format("Y-m-d");
            }
            
            if(isset($data["visaDateOnhold"])) {
                $onHold_date = new DateTime($data["visaDateOnhold"]);
                $data["visaDateOnhold"] = $onHold_date->format("Y-m-d");
            }

            if($this->_db->update($data, "visas", "visaID", $request->id) === true) {
                $visa = new model_visa($request->id);
                
                $array = array(
                    "status"=>true,
                    "visaStatus"=>$visa->getStatusName($visa->status),
                    "message"=>"Your request was successful"
                );
               
            } else {
                $array = array(
                    "status"=>false,
                    "message"=>"Your request was unsuccessful"
                );
            }
        } else {
            $array = array(
                "status"=>false,
                "message"=>$filter->errors
            );
        }
        $request->addResponseItems($array);
        return $request->response();
    }
    
    function statusUpdate($status) {
        if(isset($status) && isset($this->ID)) {
            return $this->_db->update(array("visaStatus"=>$status), "visas", "visaID", $this->ID);
        } else {
            return false;
        }
    }
    
    function uploadVisaDocument(request $request) {
        $filter = new filter(array(
            "validFile"=>array($_FILES["document"], array("pdf","docx","doc","xlsx","jpeg","jpg","png"), 5)
        ));
        
        if(empty($filter->errors)) {
            
            $uploader = new uploader($_FILES["document"]);
            $data = $request->getDataArray();

            $data["visaStatus"] = $this->getStatusID("Completed");
            $data["visaDateCompleted"] = date("Y-m-d H:i");
            $data["visaExtension"] = $uploader->getExtension();
            
            if(isset($data["visaDateExpiry"])) {
                $date = new DateTime($data["visaDateExpiry"]);
                $data["visaDateExpiry"] = $date->format("Y-m-d");
            }

            if($this->_db->update($data, "visas", "visaID", $request->id) === true){

                $visa = new model_visa($request->id);
                
                $uploader = new uploader($_FILES["document"]);
                $uploader->basePath = ADMIN_PATH . "/documentation/" . $visa->ID;
                $uploader->newFilename = "visa" . $uploader->getExtension();
                
                $array["status"] = true;
                $array["visaStatus"] = $this->getStatusName($visa->status);
                
                if($uploader->transferFile()) {
                    $array["message"][] = "Document successfully uploaded";
                } else {
                    $array["status"] = false;
                    $array["message"][] = $uploader->error;
                }
            } else {
                $array = array(
                    "status"=>false,
                    "message"=>"Your request was unsuccessful"
                );
            }
        } else {
            $array = array(
                "status"=>false,
                "message"=>$filter->errors
            );
        }
        
        $request->addResponseItems($array);
        return $request->response();
    }
            
    function update(request $request) {
        switch($request->subType) {
            case "default": return $this->updateVisa($request);
            case "upload-actual-visa": return $this->uploadVisaDocument($request);
        }
    }

    function declineEmail(model_user $user, $message) {
        $ini = parse_ini_file(ADMIN_PATH . "/config/config.ini", true);

        $mail = new $ini["mail"]["mailer"]();
        $mail->FromName = $ini["mail"]["adminName"];
        $mail->From = $ini["mail"]["admin"];
        $mail->addAddress($user->email, $user->name . " " . $user->surname);
        $mail->Subject = "Document uploaded - Declined";
        $mail->IsHTML(true);
        $mail->AltBody="If you are not able to view this message, please contact us.";

        ob_start();
        
        //Get email template
        require APPLICATION_PATH . "/views/immigration/templates/decliningEmail.php";
        
        $mail->Body = ob_get_contents();
        
        ob_end_clean();
        
        if($mail->Send()) {
            return "The email has been successfully sent";
        } else {
            return "The email was not sent";
        }
    }
    
    function getStatusName($id) {
        foreach($this->statuses as $v) {
            if($v[0] == $id) {
                return $v[1];
            }
        }
        return false;
    }
    
    function getStatusID($name) {
        foreach($this->statuses as $v) {
            if($v[1] == $name) {
                return $v[0];
            }
        }
        return false;
    }
    
    function getStatusText() {
        switch($this->status) {
            case 1: return "Created";
            case 2: return "Phase 1 Complete";
            case 3: return "Phase 2 Complete";
            case 4: return "Compiling";
            case 5: return "Compiled";
            case 6: return "Handed Over";
            case 7: return '<span style="color:green">Completed</span>';
            case 8: return '<span style="color:firebrick">Declined</span>';
        }
    }
    
    function getDaysLeft() {
        if(isset($this->dateExpiry)) {
            $date = new DateTime(date("Y-m-d"));
            
            $diff = $date->diff($this->dateExpiry);
            if($diff->invert == 0) {
                return $diff->days - 1;
            } else {
                return "-".$diff->days - 1;
            }
        }
    }
    
    function getVisaDocumentation() {
        return $this->_db->fetch_all_stmt("SELECT * FROM visa_documentation_types WHERE visaDocumentationTypeVisaTypeID=?", "i", array($this->visaTypeID));
    }
    
    function getUploadedDocumentation() {
        $result = $this->_db->fetch_all_stmt("SELECT * FROM visa_documentation WHERE visaDocumentationVisaID=?", "i", array($this->ID));
        return $result;
    }
    
    function getDocuments() {
        $docs = $this->getVisaDocumentation();
        $uploaded = $this->getUploadedDocumentation();
        
        foreach($docs as $k=>$doc) {
            for($i=0; $i<count($docs); $i++) {
                if(isset($uploaded[$i]) && $doc["visaDocumentationTypeID"] == $uploaded[$i]["visaDocumentationVisaDocumentationTypeID"]) {
                    $docs[$k]["visaDocumentationID"] = $uploaded[$i]["visaDocumentationID"];
                    $docs[$k]["visaDocumentationStatus"] = $uploaded[$i]["visaDocumentationStatus"];
                    $docs[$k]["visaDocumentationDateExpiry"] = $uploaded[$i]["visaDocumentationDateExpiry"];
                }
            }
        }
        
        return $docs;
    }
    
    function isVisaUploaded() {
        $result = $this->_db->fetch_all_stmt("SELECT visaExtension FROM visas WHERE visaID=?", "i", array($this->ID));
        if(file_exists(ADMIN_PATH . "/documentation/" .  $this->ID . "/visa" . $result[0]["visaExtension"])) {
            return true;
        } else {
            return false;
        }
    }
    
    function getTotalPhaseDocuments($phase) {
        return $this->_db->rows("SELECT visaDocumentationTypeID FROM visa_documentation_types WHERE visaDocumentationTypeVisaTypeID=? AND visaDocumentationTypePhase=?", "ii", array($this->visaTypeID, $phase));
    }
    
    function getTotalPhaseValidDocuments($phase) {
        return $this->_db->rows("SELECT visaDocumentationID, visaDocumentationVisaID FROM visa_documentation_types LEFT JOIN visa_documentation ON visaDocumentationVisaDocumentationTypeID = visaDocumentationTypeID WHERE visaDocumentationTypePhase = ? AND visaDocumentationVisaID = ? AND (visaDocumentationStatus = 1 || visaDocumentationNotRequired = 1)", "ii", array($phase, $this->ID));
    }
    
    function isPhaseComplete($phase) {
        if($this->getTotalPhaseDocuments($phase) == $this->getTotalPhaseValidDocuments($phase)) {
            return true;
        } else {
            return false;
        }
    }
    
    function getComments() {
        $results = $this->_db->fetch_all_stmt("SELECT visaCommentID FROM visa_comments WHERE visaCommentVisaID=?", "i", array($this->ID));
        $array = array();
        foreach($results as $v) {
            $array[] = new model_visaComment($v["visaCommentID"]);
        }
        return $array;
    }
    
    function deleteVisa(request $request) {
        $sql = "DELETE FROM visas WHERE visaID=?";

        if($this->_db->delete($sql, "i", array($request->id)) === true) {
            $array = array(
                "status" => true,
                "message" => "Your request was successful"
            );
        } else {
            $array = array(
                "status" => false,
                "message" =>$this->deleteMsg($this->_db->dbError)
            );
        }

        //Return json object as response
        $request->addResponseItems($array);
        return $request->response();
    }
    
    
    function delete(\request $request) {
       if($request->subType == "default") {
           return $this->deleteVisa($request);
       }
    }
    
    function getVisaID() {
        $type = new model_visaType($this->visaTypeID);
        $expat = new model_expatriate($this->expatriateID);
        $user = new model_user($expat->userID);
        
        return strtoupper($user->abbreviateName() . $type->abbreviation . $this->ID);
    }
    
    function getSpecialDocuments() {
        $result = $this->_db->fetch_all_stmt("SELECT specialDocumentID FROM special_documents WHERE specialDocumentVisaID=?", "i", array($this->ID));
        $array = array();
        
        foreach($result as $doc) {
            $array[] = new model_specialDocument($doc["specialDocumentID"]);
        }
        
        return $array;
    }
    
    function getFinancialDocuments() {
        $result = $this->_db->fetch_all_stmt("SELECT financialDocumentID FROM financial_documents WHERE financialDocumentVisaID=?", "i", array($this->ID));
        $array = array();
        
        foreach($result as $doc) {
            $array[] = new model_financialDocument($doc["financialDocumentID"]);
        }
        
        return $array;
    }

    function determineProgress() {
        $total = 0;
        
        $docs = $this->_db->fetch_all_stmt("SELECT visaDocumentationID, visaDocumentationStatus, visaDocumentationNotRequired FROM visas INNER JOIN visa_documentation ON visaID=visaDocumentationVisaID WHERE visaID=?", "i", array($this->ID));
        
        $special_docs = $this->_db->fetch_all_stmt("SELECT specialDocumentID, specialDocumentStatus FROM visas RIGHT JOIN special_documents ON visaID=specialDocumentVisaID WHERE visaID=?", "i", array($this->ID));
        
        $uploaded_docs = array_merge($docs, $special_docs);

        $total_docs = count($uploaded_docs);

        foreach($uploaded_docs as $v) {
            
            if(isset($v["visaDocumentationID"])) { 
                
                if($v["visaDocumentationStatus"] == 1 || (isset($v["visaDocumentationNotRequired"]) && $v["visaDocumentationNotRequired"] == 1)) {

                    $total++;

                }
                
            }
            
            
            
            if(isset($v["specialDocumentID"])) {
                
                if($v["specialDocumentStatus"] == 1) {
                
                     $total++;

                }
                
            }
            
        }

        if($total > 0) {
            if (round(($total / $total_docs) * 100, 0) > 100) {
                return 100;
            } else {
                return round(($total / $total_docs) * 100, 0);
            }
        } else {
            return 0;
        }
    }
    

    
    function notificationEmail($clients ,$type) {
        
        foreach ($clients as $client) {
            
            $ini = parse_ini_file(ADMIN_PATH . "/config/config.ini", true);
            $mail = new mailer();
            $mail->FromName = $ini["mail"]["adminName"];
            $mail->From = $ini["mail"]["admin"];
            $mail->IsHTML(true);
            $mail->AltBody="If you are not able to view this message, please contact us.";
            
            ob_start();
            
            if ($type === "onhlod-date" ) {

                $user = new model_user($client["visaCreatedBy"]);
                $mail->addAddress($user->email, $user->name . " " . $user->surname);
                $mail->Subject = "Visa onhold date reminder";
                require APPLICATION_PATH . "/cron/emails/onholdDate.php";
            }
            
            foreach ($client["expats"] as $expat) {
                if($type === "client") {

                    $mail->addAddress($client["companyEmail"], $client['companyName']);
                    $mail->Subject = "Visa expiry date reminder";
                    require APPLICATION_PATH . "/cron/emails/emailToClient.php";

                } else if($type === "expatriate") {

                    $mail->addAddress($expat['email'], $expat['name'] . " " . $expat['surname']);
                    $mail->Subject = "Passport expiry date reminder";
                    require APPLICATION_PATH . "/cron/emails/emailToExpatriate.php";
 
                } else if($type === "consultant") { 

                    $user = new model_user($expat["visaCreatedBy"]);
                    $mail->addAddress($user->email, $user->name . " " . $user->surname);
                    $mail->Subject = "Visa expiry date reminder";
                    require APPLICATION_PATH . "/cron/emails/emailToConsultant.php";
                } 
            }
                        
            $mail->Body = ob_get_contents();

            ob_end_clean();

            if($mail->Send()) {
                return "The email has been successfully sent";
            } else {
                return "The email was not sent";
            }  
        }
    }
}
    
