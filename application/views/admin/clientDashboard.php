<style>
    #Dashboard {
        font-size:14px;
    }
    
    #Dashboard h1 {
        color: #adadad;
        font-size:16px;
        padding:10px 0;
        margin:10px 10px;
        border-bottom:solid 1px;
        text-align: center;
    }

    #Dashboard h2 {
        font-size:20px;
        margin-left: 30px;
        font-weight: bold;
    }

    #Dashboard table.table {
      border: solid 1px lightgrey;
    }
    
    #Dashboard .dashboard-total {
        text-align: center;
    }
    
    #Dashboard .dashboard-total-heading {
        font-size: 16px;
        color: #adadad;
        padding:10px 0;
        margin:10px 10px;
        border-bottom:solid 1px;
    }
    
    #Dashboard.dashboard-total-value {
        padding: 20px;
        font-size: 25px;
    }
    #Dashboard .bold {
        font-weight: bold;
        font-size: 14pt;
    }

     #Dashboard .panel-group .panel {
      margin-bottom: 0;
      border-radius: 0px; 
    }

     #Dashboard .panel {
        margin: 10px 0;
    }

     #Dashboard .panel-default {
        border-color: #3c4c6c;
    }

     #Dashboard .panel-default > .panel-heading {
        color: black;
        background-color: white;
        border-color: #ddd;
        border-radius: 0; 
        cursor: pointer;
    }
    #Dashboard .notes {
      margin-left: 30px; 
      font-style: italic;
    }
    #Dashboard .progress {
      height: 20px;
      margin-bottom: 0px; 
      overflow: hidden;
      background-color: #f5f5f5;
      border-radius: 4px;
      -webkit-box-shadow: inset 0 1px 2px rgba(0, 0, 0, .1);
      box-shadow: inset 0 1px 2px rgba(0, 0, 0, .1);
    }

</style>

<div id="Dashboard" class="container-fluid inner-view">
    
  <div class="row">

  <h2>ACTIVE VISAS</h2>
        
    <div class="col-md-12">

      <div class="col-sm-12 col-md-12">

        <?
          $open_visas = $this->dashboard->getOpenVisasByCompany($this->user->companyID);
          $i = 0;
          foreach ($open_visas as $k => $v) {
            if(count($v) > 0) {
        ?>
          <div class="panel-group" id="accordion">
            <div class="panel panel-default">

              <div class="panel-heading" data-toggle="collapse" data-parent="#accordion" href="#collapse<? echo $i; ?>">
                <h4 class="panel-title" style="font-weight: bold; color: #3c4c6c;">
       
                    <span style="float:right; background: #16b916; color: white; padding: 3px 8px; border-radius: 50%; font-size: 12px"><? echo count($v) ?></span>
                    <span><? echo $k ?></span>
               
                </h4>
              </div>

              <div id="collapse<? echo $i; ?>" class="panel-collapse collapse">

                <div class="panel-body">  

                  <table class="table table-striped">

                    <thead>
                      <tr>
                        <th>Expatriate</th>
                        <th>Status</th>
                        <th>Progress</th>
                      </tr>
                    </thead>

                    <tbody>
                    
                    <? 
                      
                      foreach ($v as $value) { 
                        $visa = new model_visa($value["visaID"]); 
                    ?> 
                      <tr>
                        <td><? echo $value["name"]; ?></td> 
                        <td>
                          <?
                            
                            echo $visa->getStatusName($value["visaStatus"]);
                          ?>
                        </td>
                        <td>
                          <div class="progress">

                            <?
                              $progress = $visa->determineProgress();
                              if ($progress < 30) {
                                $cls = "progress-bar-danger";
                              } else if ($progress >= 30 && $progress < 80) {
                                $cls = "progress-bar-warning";
                              } else if ($progress >= 80) {
                                $cls = "progress-bar-success";
                              }
                            ?>

                            <div class="progress-bar <? echo $cls ?>" role="progressbar" style="width: <? echo $progress ?>%;" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100"><? echo $progress ?>%</div>

                          </div>
                        </td>
                      </tr>
                      <? } ?>
                    </tbody>
                   
                  </table>
                </div>
              </div>
            </div>
          </div>
        <? } 
        $i++;
        } ?>
      </div>
    </div>


    <h2 style="margin-top: 10px; margin-bottom:20px;">COMPLETED VISAS</h2>

    <div class="col-md-12">

        <div class="col-sm-12 col-md-12">

            <table rules="all" class="table table-striped" style="margin-bottom: 0">
              <thead>
                <tr>
                <th>Expatriate</th>
                <th>Visa Type</th>
                <th>Expiry Date</th>
                <th>Days Left</th>
                </tr>
            </thead>

              <? 
                $completed_visas = $this->dashboard->getCompletedVisasByCompany($this->id);
                if(count($completed_visas) > 0) {
                foreach ($completed_visas as $k => $v) {
              ?>

                <tbody>
                    <?
                    switch ($v["days"]){
                      case ($v["days"] <= 90 && $v["days"] > 60):
                        $color = "green";
                        break;
                      case ($v["days"] <= 60 && $v["days"] > 30):
                        $color = "orange";
                        break;
                      case ($v["days"] <= 30 && $v["days"] > 0):
                        $color = "red";
                        break;
                      default:
                        $color = "black";
                    }
                    ?>

                    <tr style="color: <?echo $color?>;" >
                        <td><? echo $v["name"] ?></td>
                        <td><? echo $v["visaTypeName"]; ?></td>
                        <td><?
                            if (isset($v["visaDateExpiry"])) {
                              $date = new DateTime($v["visaDateExpiry"]);
                              echo $date->format("d-m-Y");
                            } else {
                              echo "N/A";
                            }
                            ?>
                        </td>
                        <td><? 
                          if(isset($v["days"]) && $v["days"] > 0) {
                            echo $v["days"];
                          } else if(isset($v["days"]) && $v["days"] <= 0) {
                            echo '<span style="color: firebrick">Expired</span>';
                          } else {
                            echo "N/A";
                          }
                         ?></td>
                    </tr>

                </tbody>
                  <?
                }
              } else { ?>
                <tbody>
                  <tr>
                    <td colspan="5">No visas found.</td>
                  </tr>
                </tbody>
             <? } ?>
            </table>

            </div>
        
        </div>
    
    </div>
</div>