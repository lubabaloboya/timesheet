<?php

class model_menu extends navigation {
    public $color = "#0F445F";
    
    function __construct($menu) {
        parent::__construct($menu);
    }
    
    function setName($val) {
        $label = isset($val["label"]) ? $val["label"] : $val["name"];
        return ucwords(str_replace("-", " ", $label));
    }
    
    function renderMenu() {
        $str = "";
        foreach($this->prepareMenu() as $v) {
            if(isset($v["access"]) && $v["access"] == true) {
                if($v["submenu"] == false) {
                    $str .= '<li><a href="' . $v["url"] . '">' . $this->setName($v["name"]) . '</a></li>';
                } else {
                    $str .= '<li><a href="' . $v["url"] . '">' . $this->setName($v["name"]) . '</a>';
                    $str .= '<ul>';
                    foreach($v["submenu"] as $val) {
                        
                        if(isset($val["access"]) && $val["access"] == true) {                            
                            if(@$val["submenu"] == false) {
                                $str .= '<li><a  href="' . $val["url"] . '">' . $this->setName($val["name"]) . '</a></li>';
                            } else {
                                $str .= '<li><a style="background: '.$this->color.'" href="' . $val["url"] . '">' . $this->setName($val["name"]) . '</a>';
                                $str .= '<ul>';
                                foreach($val["submenu"] as $va) {
                                    if($va["access"] == true) {
                                        $str .= '<li><a style="background: '.$this->color.'" href="' . $va["url"] . '">' . $this->setName($va["name"]) . '</a></li>';
                                    }
                                }       
                                $str .= '</ul>';
                                $str .= '</li>';
                            }
                        }
                    }  
                    $str .= '</ul>';
                    $str .= '</li>';
                }
            }
        }
        
        return $str;
    }
    
    function setAttributes(array $array) {
        if(isset($array["attr"])) {
            foreach($array["attr"] as $k=>$v) {
                if(!isset($str)) {
                    $str = $k.'="'. $v .'"';
                } else {
                    $str .= " " . $k.'="'. $v .'"';
                }
            }
            return $str;
        }
        return "";
    }
    
    function renderSingleNav($val) {
        return '<div class="accordion-group tab-creator" '.$this->setAttributes($val["submenu"][0]).'><div class="accordion-heading"><a class="accordion-toggle collapse collapsed" href="' . $val["url"] . '">' . $this->setName($val["submenu"][0]) . '</a></div></div>';
    }
    
    function renderMultipleSubNavs($v) {
        $str = '<div class="accordion-group"><div class="accordion-heading"><a class="accordion-toggle collapse" data-toggle="collapse" data-target="#'.str_replace("/\s/g", "_", $v["name"]).'">' . $this->setName($v) . '</a></div><div id="'.str_replace("/\s/g", "_", $v["name"]).'" class="accordion-body collapse collapsed"><ul class="accordion-inner">';
        
        foreach($v["submenu"] as $val) {
            if(isset($val["access"]) && $val["access"] == true) { 
                $id = isset($val["id"]) ? 'id="'.$val["id"].'"' : "null";
                $str .= '<li class="tab-creator" '.$this->setAttributes($val).'><a style="background: '.$this->color.'" href="' . $val["url"] . '">' . $this->setName($val) . '</a>'.$this->getBadgeForMenuItem(@$val["badge"]).'</li>';
            }
        }  
        $str .= '</ul></div></div>';
        return $str;
    }
    
    function renderSideBar() {
        $str = "";
        foreach($this->prepareMenu() as $v) {
            if(isset($v["access"]) && $v["access"] == true) {
                if(count($v["submenu"]) == 1) {
                    $str .= $this->renderSingleNav($v);
                } else {
                    $str .= $this->renderMultipleSubNavs($v);
                }
            }
        }
        
        return $str;
    }
    
    function getBadgeForMenuItem($badge) {
        if(isset($badge) && $badge == true) {
            return '<div class="isarray-badge hidden"></div>';
        }
    }
}
?>
