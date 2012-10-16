<?php
namespace DreamblazeNet\GenSql;

class Insert extends Base {
    private $values = array();
    private $sql_functions = array(
        '#NOW' => 'NOW()'
    );
    
    public function values($values) {
        $new_values = array();
        foreach($values as $key=>$val){
            $func_key = $this->placeholder_to_sql($val);
            if($func_key == false){
                $new_values[':' . $key] = $val;
            } else {
                $new_values[$func_key] = null;
            }
        }
        $this->values = $new_values;
        $this->fields = array_keys($values);
        return $this;
    }

    protected function head_part() {
        return 'INSERT INTO ' . $this->table;
    }

    protected function fields_part(){
        return '(' . $this->fields_to_sql() . ')';
    }

    protected function values_part(){
        return 'VALUES (' . implode(array_keys($this->values), ',') . ')';
    }

    protected function values_values(){
        return array_filter($this->values, function($item){
            return !is_null($item);
        });
    }

    private function placeholder_to_sql($placeholder){
        if(isset($this->sql_functions[$placeholder])){
            return $this->sql_functions[$placeholder];
        } else {
            return false;
        }
    }
}