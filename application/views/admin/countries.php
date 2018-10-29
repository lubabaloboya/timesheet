<style>

  .main .page-header {
      margin-top: 0;
  }
  .placeholders {
    margin-left: 20px;
  }
  .placeholder {
    margin: 10px;
    text-align: center;
    width: 110px;
    height: 100px;
  }
  .placeholder span {
    display: block;
    text-align: center;
    font-size: 12px; 
    text-align: center;
  }
  .placeholder img {
      display: inline-block;
      border-radius: 50%;
      margin-top: 40px;
  }
  
</style>

<? if(count($this->rows) > 0) { ?>
<div class="container-fluid" id="innerView">
  <div class="main">
    <div class="row placeholders"> 
      <? foreach ($this->rows as $row) { ?>
        <div class="col-xs-6 col-sm-3 placeholder">
          <img
            src="/images/countries/<? echo $row["image"]?>"
              <? 
                $icon = new library_decorators_tableIcon();
                $icon->isDisabled = $this->crud->read;
                $icon->addClass(array("view", "view-icon"));
                $icon->addAttributes(array(
                    "url"=>"/admin/index",
                    "action"=>"country",
                    "id"=>"viewCountry",
                ));
                $icon->value = $row["countryID"];
                $icon->tooltip = array(
                    "title"=> $row['countryName'],
                    "place"=>"bottom"
                );
                $icon->icon = "new-window";
                echo $icon->getElement();
              ?> 
          <span class="tab-title"><? echo $row['countryName'] ?></span> 
        </div>
      <? } ?>
    </div>
  </div>
</div>
<? } else { ?> 
<div class="well well-sm" style="margin:20px;">No Countries were found</div>
<? } ?> 