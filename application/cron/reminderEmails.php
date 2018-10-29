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

$document_reminders = new model_documentReminder();
$users = new model_user();

$first_dates = $document_reminders->getFirstDateReminders();
$second_dates = $document_reminders->getSecondDateReminders();

$juniors = $users->getUsersByRoleName(5);


if (count($first_dates) > 0) { 
    foreach ($juniors as $junior) { 
        $document_reminders->sendReminderEmail($junior, $first_dates, model_documentReminder::FIRST_DATE);
    }
}

if (count($second_dates) > 0) { 
    foreach ($second_dates as $item) {
        $user = $db->fetch_all_stmt("SELECT * FROM users WHERE userID=?", 'i', [$item["documentReminderCreatedBy"]], true);
        $document_reminders->sendReminderEmail($user, [$item], model_documentReminder::SECOND_DATE);
    }
}

if (count($second_dates) > 0) { 
    foreach ($juniors as $junior) { 
        $document_reminders->sendReminderEmail($junior, $second_dates, model_documentReminder::SECOND_DATE);
    }
}