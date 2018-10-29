<?php

class model_documentReminder extends model {
    
    public $ID;
    public $visaDocumentationID;
    public $firstDateReminder;
    public $description;
    public $secondDateReminder;
    public $status;
    public $createdBy;
    public $completedAt;
    
    protected $_controller = "immigration";
    protected $_action = "visa-document-reminder";
    protected $_table = "document_reminders";
    protected $_ref = "documentReminderID";
    
    const FIRST_DATE = 'first';
    const SECOND_DATE = 'second';

    function __construct($id = null) {
        parent::__construct($id);
        $this->setUp();
    }
    
    function setUp() {
        $this->ID            = $this->result["documentReminderID"];
        $this->visaDocumentationID        = $this->result["documentReminderVisaDocumentationID"];
        $this->firstDateReminder  = $this->dateSetup($this->result["documentReminderFirstDate"]); 
        $this->secondDateReminder  = $this->dateSetup($this->result["documentReminderSecondDate"]); 
        $this->status       = $this->result["documentReminderStatus"];
        $this->createdBy       = $this->result["documentReminderCreatedBy"];
        $this->completedAt      = $this->dateSetup($this->result["documentReminderCompletedAt"]);
    }
    
    
    protected function addForm(request $request) {
        $array["path"] = "/forms/addDocumentReminderForm.js";
        $array["values"] = null;
        $array = $this->formRender($array);
        return $this->updateForm($array, $request);
    }


    function createForm(request $request) {
        switch($request->subType) {
            case "default": return $this->addForm($request);
            case "create": return $this->create($request);
            case "edit": return $this->editForm($request);
        }
    }
      
    
    function create(\request $request) {
        if($request->subType == "default") {
            return $this->createDocumentReminder($request);
        }
    }


    protected function createDocumentReminder(request $request) {

        $obj = $request->getDataObj();
        $filter = new filter(array(
            "required"=>array(
                array(
                    "Reminder : First Date"=>$obj->documentReminderFirstDate,
                    "Reminder : Second Date"=>$obj->documentReminderSecondDate
                )
            ),
        ));
        
        if(empty($filter->errors)) {
            $data = $request->getDataArray();
            $data["documentReminderVisaDocumentationID"] = $request->id;
        
            if(!isset($data["documentReminderCreatedBy"])) {
                $user = new model_user();
                $user->getCurrentUser();
                $data["documentReminderCreatedBy"] = $user->ID;
            }

            if(isset($data["documentReminderFirstDate"])) {
                $date = new DateTime($data["documentReminderFirstDate"]);
                $data["documentReminderFirstDate"] = $date->format("Y-m-d");
            }

            if(isset($data["documentReminderSecondDate"])) {
                $date = new DateTime($data["documentReminderSecondDate"]);
                $data["documentReminderSecondDate"] = $date->format("Y-m-d");
            }

            if($this->_db->insert($data, "document_reminders") === true) {
                $array = array(
                    "status"=>true,
                    "message"=>"Your request was successful"
                );
            } 
            else {
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

    
    protected function editForm(request $request) {
        $array["path"] = "/forms/addDocumentReminderForm.js";
        $array["values"] = $this->_db->fetch_all_stmt("SELECT documentReminderFirstDate,documentReminderSecondDate FROM document_reminders WHERE documentReminderID=?", "i", array($request->id), true);

        $array = $this->formRender($array);
        return $this->updateForm($array, $request);
    }


    function update(\request $request) {
        switch($request->subType) {
            case "default": return $this->updateDocumentReminder($request);
        }
    }
    

    protected function updateDocumentReminder(request $request) {
        $obj = $request->getDataObj();
        $filter = new filter(array(
            "required"=>array(
                array(
                    "Reminder : First Date"=>$obj->documentReminderFirstDate,
                    "Reminder : Second Date"=>$obj->documentReminderSecondDate
                )
            ),
        ));
        
        if(empty($filter->errors)) {
            $data = $request->getDataArray();

            if(isset($data["documentReminderFirstDate"])) {
                $date = new DateTime($data["documentReminderFirstDate"]);
                $data["documentReminderFirstDate"] = $date->format("Y-m-d");
            }

            if(isset($data["documentReminderSecondDate"])) {
                $date = new DateTime($data["documentReminderSecondDate"]);
                $data["documentReminderSecondDate"] = $date->format("Y-m-d");
            }

            if(isset($data["documentReminderCompletedAt"])) {
                $date = new DateTime($data["documentReminderCompletedAt"]);
                $data["documentReminderCompletedAt"] = $date->format("Y-m-d");
            }
            
            if($this->_db->update($data, "document_reminders", "documentReminderID", $request->id) === true) {

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
    

    function getByVisaDocumentationID($visaDocumentationID) {
        return $this->_db->fetch_all_stmt("SELECT * FROM document_reminders WHERE documentReminderVisaDocumentationID=?", "i", array($visaDocumentationID));
    } 

    
    function delete(\request $request) {
        switch($request->subType) {
            case "default": return $this->deleteDocumentReminder($request);
        }
    }
    
    
    protected function deleteDocumentReminder(request $request) {
        if($this->_db->delete("DELETE FROM document_reminders WHERE documentReminderID=?", "i", array($request->id)) === true) 
        {
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


    function sendReminderEmail($user, $reminders, $category) {  

        $ini = parse_ini_file(ADMIN_PATH . "/config/config.ini", true);
        $mail = new mailer();
        $mail->FromName = $ini["mail"]["adminName"];
        $mail->From = $ini["mail"]["admin"];
        $mail->IsHTML(true);
        $mail->AltBody="If you are not able to view this message, please contact us.";
        
        ob_start();

        $mail->addAddress($user['userEmail'], $user['name'] . " " . $user['userSurname']);
        $mail->Subject = "Document date reminder";
        require APPLICATION_PATH . "/cron/emails/reminderNotification.php";

        $mail->Body = ob_get_contents();

        ob_end_clean();

        if($mail->Send()) {
            return "The email has been successfully sent";
        } else {
            return "The email was not sent";
        } 
    }


    function countDocumentationWithReminders($visaDocumentationID) {          
        $sql = "SELECT documentReminderVisaDocumentationID FROM document_reminders WHERE documentReminderVisaDocumentationID = " . $visaDocumentationID . " ";
        return $this->_db->rows($sql);
    } 


    function getDocumentationWithReminders() {
        return $this->_db->fetch_all_stmt("SELECT documentReminderID, documentReminderFirstDate, documentReminderSecondDate, documentReminderCreatedBy, documentReminderStatus, visaDocumentationTypeName, visaTypeName 

            FROM document_reminders
            
            JOIN visa_documentation
            ON documentReminderVisaDocumentationID = visaDocumentationID
            
            JOIN visas
            ON visaDocumentationVisaID = visaID
            
            JOIN visa_types
            ON visaVisaTypeID = visaTypeID

            JOIN visa_documentation_types
            ON visaDocumentationVisaDocumentationTypeID = visaDocumentationTypeID");
    } 

    function getFirstDateReminders()
    {
        $query = $this->_db->fetch_all_stmt("SELECT visaDocumentationTypeName, visaTypeName, userID, name, userSurname, userEmail

        FROM document_reminders 

        JOIN visa_documentation
        ON documentReminderVisaDocumentationID = visaDocumentationID

        JOIN visas
        ON visaDocumentationVisaID = visaID

        JOIN expatriates
        ON visaExpatriateID = expatriateID

        JOIN visa_types
        ON visaVisaTypeID = visaTypeID

        JOIN visa_documentation_types
        ON visaDocumentationVisaDocumentationTypeID = visaDocumentationTypeID

        JOIN  users
        ON expatriateUserID = userID

        WHERE documentReminderFirstDate = CURDATE()");
                        
        return $query;
    } 
    
    function getSecondDateReminders()
    {
        $query = $this->_db->fetch_all_stmt("SELECT visaDocumentationTypeName, visaTypeName, userID , name, userSurname, userEmail, documentReminderCreatedBy

        FROM document_reminders 

        JOIN visa_documentation
        ON documentReminderVisaDocumentationID = visaDocumentationID

        JOIN visas
        ON visaDocumentationVisaID = visaID

        JOIN expatriates
        ON visaExpatriateID = expatriateID

        JOIN visa_types
        ON visaVisaTypeID = visaTypeID

        JOIN visa_documentation_types
        ON visaDocumentationVisaDocumentationTypeID = visaDocumentationTypeID

        JOIN users
        ON documentReminderCreatedBy = userID

        WHERE documentReminderSecondDate = CURDATE()");

        return $query;
    }
   
}