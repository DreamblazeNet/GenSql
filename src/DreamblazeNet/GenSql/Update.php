<?php
namespace DreamblazeNet\GenSql;

class Update extends Base {

    private $values = array();
    private $lowercase_fields;
    
    public function set($values) {
        /*
        $fields = unserialize(strtolower(serialize($this->fields)));
        $this->lowercase_fields = $fields;
        $value_keys = array_filter(array_keys($values), function($item) use ($fields){
            return in_array(strtolower($item), $fields);
        });
        $this->values = array_intersect_key($values, array_flip($value_keys));
        */
        $this->values = $values;
        return $this;
    }

    protected function head_part() {
        return "UPDATE {$this->table}";
    }

    protected function fields_part() {
        return '';
    }

    protected function values_part(){
        $fields = array();
        foreach($this->fields as $key=>$value){
            if(is_array($value) && isset($value['name']))
                $fields[$key] = $value['name'];
            else
                $fields[$value] = $value;
        }

        $values = $this->values;
        $sets = array();
        foreach($values as $value=>$content){
            //$field_id = array_search(strtolower($value), $this->lowercase_fields);
            if(isset($fields[$value])){
                $field = $fields[$value];

                $sets[] = $field . ' = :' . $value. ' ';
            }
        }
        $sets_string = implode(',', $sets);
        if(empty($sets_string))
            throw new \Exception("No matching fields for update Values:" . var_export($values, true));
        else
            return 'SET ' . $sets_string . ' ';
    }

    protected function values_values(){
        $values = $this->values;
        $result = array();
        foreach($values as $key=>$val){
            $result[':'.$key] = $val;
        }
        return $result;
    }
}