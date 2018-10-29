<div class="general-info container" style="margin-top:20px;">
    <div class="alert alert-danger" role="alert">
            <span class="glyphicon glyphicon-exclamation-sign"></span>
            Error: <? echo @$this->code?>
    </div>
    <p>
        <? 
            if(isset($this->message)) {
                echo $this->message;
            } 
        ?>
    </p>
</div>
