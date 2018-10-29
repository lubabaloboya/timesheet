<?php
// This feature to be used in the future. 
defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "application");
defined('ADMIN_PATH') || define("ADMIN_PATH", realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "admin"));

function autoload($name) {

    $arr = str_getcsv($name, '_');
    if(count($arr) > 1) {
        $str = "/";
        foreach($arr as $k=>$v) {
            if($k != count($arr) - 1) {
                $str .= $v . "/";
            } else {
                $str .= $v . ".php";
            }
        }
        require_once  APPLICATION_PATH . $str;
    } else {
        require_once  APPLICATION_PATH . '/library/'. lcfirst($name) .  ".php";
    }
}

spl_autoload_register('autoload');

$db = new database();

$sql_clients = $db->fetch_all_stmt("SELECT name, userSurname, userEmail, DATEDIFF(visaDateExpiry,NOW()) as `days`, visaDateExpiry,visaTypeName,companyEmail,companyName, userCompanyID, expatriatePassportNumber, visaCreatedBy FROM users INNER JOIN expatriates ON userID=expatriateUserID INNER JOIN companies ON userCompanyID=companyID INNER JOIN visas ON expatriateID=visaExpatriateID INNER JOIN visa_types ON visaTypeID=visaVisaTypeID WHERE userRoleID = 4 AND DATEDIFF(visaDateExpiry,NOW()) <= 90");

$client_email = $db->fetch_all_stmt("SELECT companyID, companyEmail, companyName FROM companies");

foreach ($sql_clients as $k => $client) {

    $expats = array(
        "name"=>$client["name"],
        "surname"=>$client["userSurname"], 
        "email"=>$client["userEmail"],
        "days"=>$client["days"],
        "ExpiryDate"=>$client["visaDateExpiry"],
        "VisaType"=>$client["visaTypeName"],
        "passportNumber"=>$client["expatriatePassportNumber"],
        "visaCreatedBy"=>$client["visaCreatedBy"]
    );
    
    foreach($client_email as $key=> $int){
        
        if($int["companyID"] === $client["userCompanyID"]) {
            $client_email[$key]["expats"][] = $expats;
        } 
    }   
}


$users =  $db->fetch_all_stmt("SELECT DISTINCT  visaCreatedBy FROM users INNER JOIN expatriates ON userID=expatriateUserID INNER JOIN visas on visaExpatriateID=expatriateID WHERE visaDateOnHold IS NOT NULL");
$expatriates = $db->fetch_all_stmt("SELECT name,userSurname, username, visaCreatedBy, visaDateOnhold FROM users INNER JOIN expatriates ON userID=expatriateUserID INNER JOIN visas on visaExpatriateID=expatriateID WHERE visaDateOnHold IS NOT NULL");
    
if(isset($expatriates) && is_array($expatriates)){
    foreach($users as $key => $user){
        $users[$key]['expatriates'] = array();    
        foreach ($expatriates as $expat) {
            if($user['visaCreatedBy'] === $expat['visaCreatedBy']) {
                $users[$key]['expatriates'] [] = $expat;
            }
        }
    }
}

$visa = new model_visa();

$visa->notificationEmail($client_email, "client");
$visa->notificationEmail($client_email, "expatriate");
$visa->notificationEmail($client_email, "consultant");
$visa->notificationEmail($users, "onhlod-date");