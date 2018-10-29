<?
    $user = new model_user($this->model->userID);
    $host = new model_country($this->model->hostCountryID);
    $home = new model_country($this->model->homeCountryID);
?>
<li class="list-group-item">
    <span class='list-item-header'>Name:</span> <? echo ucwords($user->name); ?>
</li>
<li class="list-group-item">
    <span class='list-item-header'>Surname:</span> <? echo ucwords($user->surname); ?>
</li>
<li class="list-group-item">
    <span class='list-item-header'>Home Country:</span> <? echo $home->name; ?>
</li>
<li class="list-group-item">
    <span class='list-item-header'>Host Country:</span> <? echo $host->name; ?>
</li>
<li class="list-group-item">
    <span class='list-item-header'>Job Title:</span> <? 
    $list = new model_dropDownList("expatriateJobTitle");
    echo $list->getListItemName($this->model->jobTitle); 
?></li>

<li class="list-group-item">
    <span class='list-item-header'>Passport Expiry Date:</span> <? echo $this->model->passportExpiryDate; ?>
</li>

<li class="list-group-item">
    <span class='list-item-header'>Job Description:</span> <? echo $this->model->jobDescription; ?>
</li>
