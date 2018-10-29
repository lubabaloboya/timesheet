<? 
    foreach ($this->dashboard->getActiveVisas() as $key => $value){
        $user = new model_user($value["visaCreatedBy"]);
        $index = $key + 1;
?>
    <div class="panel-group" id="accordion">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h4 class="panel-title">
              <a data-toggle="collapse" data-parent="#accordion" href="#collapse<?echo $index; ?>"> <? echo $user->name . " : " . $value["total"] ?> Open Visas </a>
          </h4>
        </div>
        <div id="collapse<?echo $index; ?>" class="panel-collapse collapse">
          <div class="panel-body">  
             <? foreach ($value["status"] as $v) { 
                $visa = new model_visa();
             ?> 
              <ul>
                  <li>
                      <? if($v["duplicate"] > 1) {
                          echo $visa->getStatusName($v["visaStatus"]) . " " . $v["duplicate"] ; 
                      } else {
                          echo $visa->getStatusName($v["visaStatus"]); 
                      } ?>
                  </li>
              </ul>
            <? } ?>
          </div>
        </div>
      </div>
    </div>
<? } ?>