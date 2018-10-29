<?
    $expat = new model_expatriate($this->model->expatriateID);
    $user = new model_user($expat->userID);
    $visaType = new model_visaType($this->model->visaTypeID);
?>

<li class="list-group-item">
    <span class='list-item-header'>Name:</span> <? echo $this->model->getVisaID() ?>
</li>
<li class="list-group-item">
    <span class='list-item-header'>Name:</span> <? echo ucwords($user->name . " " . $user->surname); ?>
</li>
<li class="list-group-item">
    <span class='list-item-header'>Visa Type:</span> <? echo ucwords($visaType->name); ?>
</li>
<li class="list-group-item">
    <span class='list-item-header'>Status:</span> <span class="visa-status"><? echo $this->model->getStatusName($this->model->status); ?></span>
</li>
<li class="list-group-item">
    <span class='list-item-header'>Date Submitted:</span> <? 
    if(isset($this->model->dateSubmitted)) {
        echo $this->model->dateSubmitted->format("d-m-Y");
    } else {
        echo "N/A";
    }
    
?></li>
<li class="list-group-item">
    <span class='list-item-header'>Date Declined:</span> <? 
    if(isset($this->model->dateDeclined)) {
        echo $this->model->dateDeclined->format("d-m-Y");
    } else {
        echo "N/A";
    }
    
?></li>
<li class="list-group-item">
    <span class='list-item-header'>Date Appointment:</span> <?
    if(isset($this->model->dateAppointment)){
        echo $this->model->dateAppointment->format("d-m-Y");
    } else{
        echo "N/A";
    }
    ?>
</li>
<li class="list-group-item">
    <span class='list-item-header'>Date On Hold:</span> <?
    if(isset($this->model->dateOnhold)){
        echo $this->model->dateOnhold->format("d-m-Y");
    } else{
        echo "N/A";
    }
    ?>
</li>
<li class="list-group-item">
    <span class='list-item-header'>Expiry:</span> <?
        if(isset($this->model->dateExpiry)) {
            echo $this->model->dateExpiry->format("d-m-Y");
        } else {
            echo "TBA";
        } 
?></li>


<? if($this->user->roleID <= 2) { // Only admin and champion should see this field ?>

<li class="list-group-item">
    <? if ($this->model->isVisaUploaded() === false && $this->model->getStatusName($this->model->status) == "Submitted") { ?>
     <span class='list-item-header'>Visa Doc:</span> <span class="upload-actual-visa" style="color:blue; cursor: pointer">Upload</span>
    <? } else if ($this->model->isVisaUploaded() === false && $this->model->getStatusName($this->model->status) == "Completed") { ?>
      <span class='list-item-header'>Visa Doc:</span> <span class="upload-actual-visa" style="color:blue; cursor: pointer">Upload</span> 
      <? } else if ($this->model->isVisaUploaded() === false && $this->model->getStatusName($this->model->status) != "Submitted") { ?>
      <span class='list-item-header'>Visa Doc:</span> Awaiting Submission
    <? } else { ?>
   <span class='list-item-header'>Visa Doc:</span> <span class="view-actual-visa" style="color:blue; cursor: pointer">View Visa</span>
    <? } ?>
</li>

<? } ?>