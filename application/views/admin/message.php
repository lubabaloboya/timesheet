<div class="modal fade" id="messageModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="glyphicon glyphicon-remove"></span></button>
              <h4 class="modal-title" id="myModalLabel">Message</h4>
            </div>
            <div class="modal-body">
                <p>From: </p>
                <div class="well well-sm">
                    <?
                        if(isset($this->message["messageOwnerID"])) {
                            $user = new model_user($this->message["messageOwnerID"]);
                            echo $user->name . " " . $user->surname;
                        } else {
                            echo "Application Message";
                        }
                    ?>
                </div>
                <p>Title: </p>
                <div class="well well-sm">
                    <?  echo $this->message["messageTitle"] ?>
                </div>
                <p>Message: </p>
                <div class="well well-sm">
                    <? echo $this->message["messageBody"] ?>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
