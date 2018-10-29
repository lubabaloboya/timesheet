<div class="progress">
    <?
        $progress = $this->model->determineProgress();
        if($progress < 30) {
            $cls = "progress-bar-danger";
        } else if($progress >= 30 && $progress < 80) {
            $cls = "progress-bar-warning";
        } else if($progress >= 80) {
            $cls = "progress-bar-success";
        }
    
    ?>
    <div class="progress-bar <? echo $cls ?> progress-bar-striped" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: <? echo $this->model->determineProgress() ?>%">
        <span class="sr-only"><? echo $progress ?>% Complete (success)</span>
    </div>
</div>

<div style="color: green; font-size: 20px; text-align: center;"><? echo $progress ?> % Complete</span>
