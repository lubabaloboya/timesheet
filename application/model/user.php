<?php

class model_user extends model {
    
    public $ID;
    public $name;
    public $surname;
    public $username;
    public $email;
    public $receiveEmail;
    public $status;
    public $companyID;
    public $roleID;
    public $userDateCreated;
    public $userLastLogin;

    protected $_controller = "admin";
    protected $_action = "users";
    protected $_table = "users";
    protected $_ref = "userID";

    function __construct($id = null) {
        parent::__construct($id);
        $this->setUp();
    }
    
    function setUp() {
        $this->ID               = $this->result["userID"];
        $this->name             = $this->result["name"];
        $this->surname          = $this->result["userSurname"];
        $this->username         = $this->result["username"];
        $this->email            = $this->result["userEmail"];
        $this->companyID        = $this->result["userCompanyID"];
        $this->receiveEmail     = $this->result["userReceiveEmail"];
        $this->status           = $this->result["userStatus"];
        $this->roleID           = $this->result["userRoleID"];
        $this->dateCreated      = $this->dateSetup($this->result["userDateCreated"]);
        $this->dateLastLogin    = $this->dateSetup($this->result["userLastLogin"]);
    }

    function getCurrentUser() {
        if(isset($_SESSION["auth"])) {
            $sql = "SELECT * FROM users WHERE username=? AND userPassword=?";
            $array = array(
                $_SESSION["auth"]["username"],
                $_SESSION["auth"]["password"]
            );
            
            if($this->_db->rows($sql, "ss", $array) == 1) {
                $this->result = $this->_db->fetch_all_stmt($sql, "ss", $array, true);
                $this->setUp();
            }
            
            return true;
        } else {
            return null;
        }
    }

    private function editForm($request) {
        $form = new form(APPLICATION_PATH . "/forms/editUserForm.js");
        
        $values = $this->_db->fetch_all_stmt("SELECT * FROM users LEFT JOIN expatriates ON userID = expatriateUserID WHERE userID=? ", "i", array($request->id), true);
        
        $form->setValues($values);

        $form->injectValues();
        
        $form->addList(array(
            "userCompanyID" => $this->_db->fetch_numeric("SELECT companyID, companyName FROM companies ORDER BY companyName ASC")
        ));

        if ($values["userRoleID"] == 1) {
            $form->addList(array(
                "userRoleID" =>$this->_db->fetch_numeric("SELECT roleID, roleName FROM roles")
            ));
        } else {
            $form->addList(array(
                "userRoleID" =>$this->_db->fetch_numeric("SELECT roleID, roleName FROM roles WHERE roleID > 1")
            ));
        }
        
        
        $countries = $this->_db->fetch_numeric("SELECT countryID, countryName FROM countries "); 
            $form->addList(array(
             "expatriateHostCountryID"=>$countries,
             "expatriateHomeCountryID"=>$countries  
            ));
            
            $list = new model_dropDownList();
            $form->addList($list->getFormDropDownLists("expatriate", array("expatriateHomeCountryID"=>$countries)));
        
        if($values["userRoleID"] == 4){
            $form->removeGroupClass("hide", "expatriateHomeCountryID");
            $form->removeGroupClass("hide", "expatriateHostCountryID");
            $form->removeGroupClass("hide", "expatriatePassportNumber");
            $form->removeGroupClass("hide", "expatriateJobTitle");
            $form->removeGroupClass("hide", "expatriateJobDescription");
            $form->removeGroupClass("hide", "expatriatePassportExpiryDate");
        }
        if($values["userRoleID"] != 4){
            $form->removeValidation("expatriateHomeCountryID", "required");
            $form->removeValidation("expatriateHostCountryID", "required");
            $form->removeValidation("expatriatePassportNumber", "required");
        }
        
        return $this->updateForm($form->getForm(), $request);
    }
    
    private function addForm($request) {
        $array["path"] = "/forms/addUserForm.js";
        $array["values"] = null;

        $array["list"] = array(
            "userRoleID" =>$this->_db->fetch_numeric("SELECT roleID, roleName FROM roles WHERE roleID > 1"),
            "userCompanyID" =>$this->_db->fetch_numeric("SELECT companyID, companyName FROM companies ORDER BY companyName ASC")
        );
        
        $array = $this->formRender($array);
        return $this->updateForm($array, $request);
    }

    private function updateProfileDetailsForm($request) {
        $user = new model_user();
        $user->getCurrentUser();
        $array["path"] = "/forms/editProfileDetails.js";
        $array["values"] = $this->_db->fetch_all_stmt("SELECT * FROM users WHERE userID=?", "i", array($user->ID), true);
        $array["list"] = array(
            "userCompanyID" =>$this->_db->fetch_numeric("SELECT companyID, companyName FROM companies")
        );
        
        unset($array["values"]["userPassword"]);
        $array = $this->formRender($array);
        
        return $this->updateForm($array, $request);
    }
    
    protected function registerUserForm($request) {
        $array["path"] = "/forms/registerUserForm.js";
        $array = $this->formRender($array);  
        return $this->updateForm($array, $request);
    }
            
    function createForm(request $request) {
        if($request->subType == "edit") {
            return $this->editForm($request);
        } else if($request->subType == "default"){
            return $this->addForm($request);
        } else if($request->subType == "register-user") {
            return $this->registerUserForm($request);
        } else if($request->subType == "profile-details") {
            return $this->updateProfileDetailsForm($request);
        }
    }
    
    function createPassword() {
        $crypt = new passwordCrypt();
        $password = functions::randomStringGenerator(8);
        return $crypt->create($password);
    }
    
    function createUser(request $request) {
        $obj = $request->getDataObj();
        $filterArray = array(
            "required"=>array(
                array(
                    "Name"=>$obj->name,
                    "Surname"=>$obj->userSurname,
                    "Username"=>$obj->username,
                    "Email"=>$obj->userEmail,
                    "Company"=>$obj->userCompanyID,
                    "Role"=>$obj->userRoleID,
                )
            ),
            "entryExists"=>array(
                "SELECT * FROM users WHERE username=?", 
                "s", 
                array($obj->username)
            )
        );

        $filter = new filter($filterArray);
       

        if(empty($filter->errors)) {
            $data = $request->getDataArray();
            $crypt = new passwordCrypt();
            $password = functions::randomStringGenerator(8);
            $data["userPassword"] = $crypt->create($password);

            if($this->_db->insert($data, "users") === true) {
              $id = $this->_db->insert_id;
              $array = array(
                  "status" => true,
                  "message" => "Your request was successful"
              );

              //Only send registration email to admin and champion users
              if($obj->userRoleID != 4) {
                if(!$this->welcomeEmail($id, $password)) {
                  $array["message"] = array("Your request was successful", "Notice: Credentials email was not sent");
                } else {
                  $array["message"] = array("Your request was successful", "Email Sent");
                }
              } else if($obj->userRoleID == 4) {
                $this->_db->insert(array("expatriateUserID"=>$this->_db->insert_id), "expatriates");
              }

              $array["viewID"] = $id;
              $array["title"] = $obj->username;
              $array["url"] = "/admin/index";
              $array["typeID"] = "viewUser";
              $array["action"] = "user";
            } else {
                $array = array(
                    "status" => false,
                    "message" =>$this->_db->error
                );
            }
        } else {
            $array = array(
                "status" => false,
                "message" => $filter->errors
            );
        }
        
        $request->addResponseItems($array);
        return $request->response();
    }
 
    function welcomeEmail($userID, $password) {
        $ini = parse_ini_file(ADMIN_PATH . "/config/config.ini", true);

        $user = new model_user($userID);

        $mail = new $ini["mail"]["mailer"];
        $mail->FromName = $ini["mail"]["adminName"];
        $mail->From = $ini["mail"]["admin"];
        $mail->Encoding = "base64";
        $mail->addAddress($user->email, $user->name . " " . $user->surname);
        $mail->Subject = $ini["company"]["name"]." - Online Registration";
        $mail->IsHTML(true);
        $mail->AltBody="If you are not able to view this message, please contact us. You will find our contact details at " . $ini["company"]["url"];

        ob_start();
        
        //Set the template variables
        $username = $user->username;
        require APPLICATION_PATH . "/templates/welcomeEmail.php";
        
        $mail->Body = ob_get_contents();
        
        ob_end_clean();
        
        if($mail->Send()) {
            return true;
        } else {
            return false;
        }
    }
 
    
    function create(\request $request) {
        if($request->subType == "default") {
            return $this->createUser($request);
        }
    }

    function read(request $request) {
        if($request->subType == "default") {
            $array["page"] = "admin/userView";
            return $array;
        }
    }
    
    function updateUser($request) {
        $obj = $request->getDataObj(); 
        $filter = new filter(array(
            "minLength"=>array(
                array(
                    "Name"=>array(@$obj->name, 1),
                    "Surname"=>array(@$obj->userSurname, 1),
                    "Username"=>array(@$obj->username, 1),
                    "Email"=>array(@$obj->userEmail, 1),
                    "Company"=>array(@$obj->userCompanyID, 1),
                    "Role"=>array(@$obj->userRoleID, 1)
                )
            )
        ));

        if(empty($filter->errors)) {
            
            $user = $this->extractor($request->getDataArray(), "user");
            
            if(isset($request->getDataObj()->name)){
                $results = array_merge(array("name"=>$request->getDataObj()->name), $user);
            } else {
                $results = $user;
            }
            
            $xpat = $this->extractor($request->getDataArray(), "expatriate");
            
            if(!empty($results)){
               $user_query = $this->_db->update($results, "users", "userID", $request->id) === true;
            }  else {
                $user_query = true;
            }
            
            if(isset($xpat["expatriatePassportExpiryDate"])) {
                $date = new DateTime($xpat["expatriatePassportExpiryDate"]);
                $xpat["expatriatePassportExpiryDate"] = $date->format("Y-m-d");
            }
            
            if(!empty($xpat)){
                $xpat_query = $this->_db->update($xpat, "expatriates", "expatriateUserID", $request->id) === true;
            }else{
                $xpat_query = true;
            }
            
            if($user_query && $xpat_query){
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
    protected function updateProfileDetails(request $request) {
        $obj = $request->getDataObj();
        $filterArray = array(
            "minLength"=>array(
                array(
                    "Name"=>array(@$obj->name, 1),
                    "Surname"=>array(@$obj->userSurname, 1),
                    "Username"=>array(@$obj->username, 1),
                    "Email"=>array(@$obj->userEmail, 1)
                )
            )
        );
        
        //If we send a password validate it
        if(isset($obj->userPassword)) {
            $filterArray["validatePassword"][0]["Password"] = array(8, $obj->userPassword);
        }

        $user = new model_user();
        $user->getCurrentUser();
        $filter = new filter($filterArray);
        if(empty($filter->errors)) {

            //At this point we check if a password has been received if so create a new password hash
            $data = $request->getDataArray();
            if(isset($obj->userPassword)) {
                $crypt = new passwordCrypt();
                $data["userPassword"] = $crypt->create($obj->userPassword);
                unset($data["confirmPassword"]);
            }

            //If someone tries to get clever and add a role id here we will disregard it
            if(isset($data["userRoleID"])) {
                unset($data["userRoleID"]);
            }

            //Update the details and update the session as to keep user logged in
            if($this->_db->update($data, "users", "userID", $user->ID) === true) {
                if(isset($obj->username)) {
                    $_SESSION["auth"]["username"] = $data["username"];
                }

                if(isset($obj->userPassword)) {
                    $_SESSION["auth"]["password"] = $data["userPassword"];
                }

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
    
    function update(request $request) {
        if($request->subType == "default") {
            return $this->updateUser($request);
        } else if($request->subType == "profile-details") {
            return $this->updateProfileDetails($request);
        }
    }
    
    function deleteUser(request $request) {
        $user = new model_user();
        $user->getCurrentUser();

        if($user->ID !== $request->id) {
            $sql = "DELETE FROM users WHERE userID=?";

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
        } else {
            $array = array(
                "status" => false,
                "message" => "You are not authorised to complete this task"
            );
        }

        //Return json object as response
        $request->addResponseItems($array);
        return $request->response();
    } 

    function delete(\request $request) {
       if($request->subType == "default") {
           return $this->deleteUser($request);
       }
    }
    
    function abbreviateName() {
        $fullname = $this->name . " " . $this->surname;
        $array = explode(" ", $fullname);
        $str = "";
        
        foreach($array as $v) {
            $str .= $v[0];
        }
        
        return $str;
    }

    public function getFullNameByUserId($userId) { 
        $query = "SELECT name, userSurname FROM users WHERE userID=?";
        $result = $this->_db->fetch_all_stmt($query, "i", array($userId), true);

        $fullName = $result['name'] . ' ' . $result['userSurname'];
        return $fullName;
    } 

    public function getUsersByRoleName($roleID) {
        $query = "SELECT name, userSurname, userEmail, roleName FROM users JOIN roles ON userRoleID = roleID WHERE roleID=?";
        return $this->_db->fetch_all_stmt($query, "i", array($roleID));
    } 

}
?>