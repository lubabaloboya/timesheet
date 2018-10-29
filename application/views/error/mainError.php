<script>
    $(document).ready(function() {
        tabsHandler2.activateTab($(".nav-tabs li.active"));
    });
</script>

<div id="dashboard" class="table-holder">
    <ul class="nav nav-tabs">
        <li class="active table-tab loaded" data-button="false" data-title="dashboard" data-url="/admin/dashboard" data-limit="false" data-page="false" data-search="false">
            <a href="#tabs-1" data-toggle="tab">Dashboard</a> 
        </li>
    </ul>
    <div class="tab-content">
        <div id="tabs-1" class="tab-pane active">
            <div class="alert alert-danger" style="margin:20px">
            <?
                echo $this->message;
            ?>
            </div>
        </div>
    </div>
</div>
