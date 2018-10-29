<?php

class controllers_index extends library_controllers_indexController {
    

    function __construct() {
        parent::__construct();
        
    }
    
    public function indexAjax() {
        $str = "model_" . $this->_request->action;
        $this->_model = new $str();

        switch($this->_request->type) {
            case "form": echo $this->_model->createForm($this->_request);
                break;
            case "create": echo $this->_model->create($this->_request);
                break;
            case "read": echo $this->_model->read($this->_request);
                break;
        }
    }

    public function registerAction()
    {
        $this->view->setView(array(
            "page"=>"index/register",
            "template"=>"index",
            "header"=>array(
                "metaData"=>array(
                    "author"=>"isArray",
                    "charset"=>"UTF-8"
                ),
                "title"=>"Register",
                "js"=>array("index.js"),
                "theme"=>"frontend"
            )
        ));
        $this->view->render();
    }
    
    public function accountActivationAction() {
        if(isset($_GET["user"]) && isset($_GET["token"])) {
            $user = new model_user($_GET["user"]);
            if(isset($user->ID)) {
                if($_GET["token"] == $user->code) {
                    if($user->status === 0) {
                        if($this->_db->update(array("userStatus"=>1), "users", "userID", $user->ID) === true) {
                            $this->_db->insert(array("invoiceMemberID"=>$user->getMember()->ID, "invoiceType"=>1), "invoices");
                            $this->view->setView(array(
                                "page"=>"index/accountActivation",
                                "template"=>"index",
                                "header"=>array(
                                    "metaData"=>array(
                                        "author"=>"isArray",
                                        "charset"=>"UTF-8"
                                    ),
                                    "title"=>"Register",
                                    "js"=>array("index.js"),
                                    "theme"=>"frontend"
                                )
                            ));
                            $this->view->render();
                        } else {
                            throw new userException("There has been an error, please contact your sytem adminstator",101001);
                        }
                    } else {
                        throw new userException("This account has already been confirmed", 101002);
                    }
                } else {
                    throw new userException("This code is invalid", 101003);
                }
            } else {
                throw new userException("There has been an error, please contact your sytem adminstator", 101004);
            }
        } else {
            throw new userException("There has been an error, please contact your sytem adminstator", 101005);
        }
    }
}