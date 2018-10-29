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
    }
    
    .dashboard-total {
        text-align: center;
    }
    
    .dashboard-total-heading {
        font-size: 16px;
        color: #adadad;
        padding:10px 0;
        margin:10px 10px;
        border-bottom:solid 1px;
    }
    
    .dashboard-total-value {
        padding: 20px;
        font-size: 25px;
    }
</style>

<div id="Dashboard" class="container-fluid inner-view">
    
    <div class="row">

        <div class="col-md-6">
            <div class="col-sm-12 col-md-12" style="height:435px;">
                <h1>EXPATRIATE BREAKDOWN</h1>
                <div id="expatriateBreakdown" class="graph panel-slider"></div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="col-sm-12 col-md-12" style="height:435px;">
                <h1>EXPATRIATE BREAKDOWN</h1>
                <div class="panel-slider" id="expatriateTableBreakdown">
                </div>
            </div>
        </div>
        
    </div>
    
    <div class="row" style="margin-top: 40px;">
        
        <div class="col-md-12">

            <div class="col-sm-6 col-md-2 dashboard-total">
                <h6 class="dashboard-total-heading">Total Expats</h6>
                <div class="dashboard-total-value"><?
                    echo $this->dashboard->getTotalExpats();
                ?></div>
            </div>
            
            <div class="col-sm-6 col-md-2 dashboard-total">
                <h6 class="dashboard-total-heading">Total Customers</h6>
                <div class="dashboard-total-value"><?
                    echo $this->dashboard->getTotalCustomers();
                ?></div>
            </div>
            
            <div class="col-sm-6 col-md-2 dashboard-total">
                <h6 class="dashboard-total-heading">Total Open Visas</h6>
                <div class="dashboard-total-value"><?
                    echo $this->dashboard->getTotalOpenVisas();
                ?></div>
            </div>
            
            <div class="col-sm-6 col-md-2 dashboard-total">
                <h6 class="dashboard-total-heading">Total Completed Visas</h6>
                <div class="dashboard-total-value"><?
                    echo $this->dashboard->getTotalCompletedVisas();
                ?></div>
            </div>
            
            <div class="col-sm-6 col-md-2 dashboard-total">
                <h6 class="dashboard-total-heading">Issued Visas</h6>
                <div class="dashboard-total-value"><?
                    echo $this->dashboard->getTotalIssuedVisas();
                ?></div>
            </div>
            
            <div class="col-sm-6 col-md-2 dashboard-total">
                <h6 class="dashboard-total-heading">Total Denied Visas</h6>
                <div class="dashboard-total-value"><?
                    echo $this->dashboard->getTotalDeniedVisas();
                ?></div>
            </div>
            
        </div>
        
    </div>
    
    <div class="row">

        <div class="col-md-6">
            <div class="col-sm-12 col-md-12" style="height:435px;">
                <h1>MONTHLY VISA BREAKDOWN</h1>
                <div id="monthlyVisaBreakdown" class="graph panel-slider"></div>
            </div>
        </div>
        
        <div class="col-md-6">
          <div class="col-sm-12 col-md-12" style="height:435px;">
              <h1>ACTIVE VISAS SUMMARY</h1>
              <div id="activeVisaSummary" class="graph panel-slider"></div>
          </div>
        </div>

    </div>
</div>
