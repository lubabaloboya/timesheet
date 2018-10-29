<div class="container-fluid" id="innerView">
	<div class="row">
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">

			<div  class="panel panel-primary">
				<div class="panel-heading"><? echo $this->model->name ?></div>
					<ul class="list-group">
						<? 
							$contry = new model_country();
							$visa_type = $contry->getCountryVisaType($this->model->ID);
							if (count($visa_type) > 0) {
								foreach($visa_type as $v) {
								?>
									<li class="list-group-item"><? echo $v["visaTypeName"] ?></li>
								<?	
								}
							} else {
								?>
									<li class="list-group-item"><? echo "No visa type allocated to this company"; ?></li>
								<?
							}
						?>
					</ul>
			</div>
		</div>
	</div>
</div>