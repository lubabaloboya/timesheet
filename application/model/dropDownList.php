<?php

class model_dropDownList {
    
    protected $_ID;
    protected $_name;
    protected $_model;
    protected $_values;
    
    public $key;
    public $val;

    function __construct($name = null) {
        $this->_db = new database();
        
        if(isset($name)) {
            $result = $this->_db->fetch_all_stmt("SELECT * FROM drop_down_lists WHERE dropDownListName=?", "s", array($name), true);
            $this->_ID          = $result["dropDownListID"];
            $this->_name        = $result["dropDownListName"];
            $this->_model       = $result["dropDownListModel"];
            $this->_values      = isset($result["dropDownListValues"]) ? json_decode($result["dropDownListValues"], true) : array();
        }
        
        
    }
    
    function addItem() {
        $key = count($this->_values)+1;
        array_push($this->_values, array((int) $key, $this->val, 1));
        if($this->_db->update(array("dropDownListValues"=>json_encode($this->_values)), "drop_down_lists", "dropDownListID", $this->_ID) === true) {
            return json_encode(array(
                "status"=>true,
                "key"=>$key
            ));
        } else {
            return json_encode(array(
                "status"=>false,
            ));
        }
    }
    
    function deleteItem() {
            foreach($this->_values as $k=>$v) {
                if($this->key == $v[0]) {
                    $this->_values[$k][2] = 0;
                }
            }
            return $this->_db->update(array("dropDownListValues"=>json_encode($this->_values)), "drop_down_lists", "dropDownListID", $this->_ID);
            
    }
    
    function updateItem() {
        
        
        foreach($this->_values as $k=>$v) {
            if($this->key == $v[0]) {
                $this->_values[$k][1] = $this->val;
            }
        }
        return $this->_db->update(array("dropDownListValues"=>json_encode($this->_values)), "drop_down_lists", "dropDownListID", $this->_ID);
    }

    function getListItemName($key) {
        foreach($this->_values as $v) {
            if((int) $key == (int) $v[0]) {
                return $v[1];
            }
        }
    }
    
    protected function setDropDownList($array) {
        $newarray = array();
        
        $i = 0;
        
        if(is_array($array)) {
            foreach($array as $k=>$v) {
                if($v[2] == 1) {
                    $newarray[$i] = $v;
                    $i++;
                }
            }
        }
        return $newarray;
    }
    
    function getFormDropDownLists($model, $list) {
        $db = new database();
        
        $result = $db->fetch_all_stmt("SELECT dropDownListName, dropDownListValues FROM drop_down_lists WHERE dropDownListModel=?", "s", array($model));
        
        foreach($result as $k=>$v) {
            $list[$v["dropDownListName"]] = $this->setDropDownList(json_decode($v["dropDownListValues"], true));
        }

        return $list;
    }

    static function getSpecificList($listName) {
        $db = new database();
        $result = $db->fetch_all_stmt("SELECT dropDownListValues FROM drop_down_lists WHERE dropDownListName=?", "s", array($listName), true);
        
        if($array = json_decode($result["dropDownListValues"])) {
            return $array;
        } else {
            throw new appException("JSON decoding failed");
        }
    }
}