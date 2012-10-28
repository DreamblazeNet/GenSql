<?php

namespace DreamblazeNet\GenSql;

abstract class Base implements Query {

    protected $type = 'none'; /* none, one or many */
    protected $fields = array();
    protected $table;
    protected $conds = array();
    
    protected $sql;
    protected $sql_values;
    
    public $result;
    public $row_count;

    protected $query_parts = array(
        'head',
        'fields',
        'subhead',
        'join',
        'values',
        'where',
        'groupby',
        'order',
        'limit',
        'offset'
    );
    
    public function __construct($table, $fields = null) {
        if(is_null($fields))
            $fields = array('*');

        if(!is_array($fields))
            throw new InvalidFieldsException("Fields-Parameter must be an array! Given:" . var_export($fields, true));

        array_walk($fields, function($field){
            if(is_array($field) && (!isset($field['name']) || !is_string($field['name']))){
                throw new InvalidFieldsException("Advanced Fields must have a name! Given:" . var_export($field, true));
            } elseif(is_null($field) || is_array($field) || is_string($field)) {
                return;
            } else {
                throw new InvalidFieldsException("Only null, arrays and strings are allowed as field-definitions! Given:" . var_export($field, true));
            }
        });

        $this->table = $table;
        $this->fields = array_filter($fields);
    }
    
    public function give_sql_and_values(){
        $result = array(
            $this->give_sql(),
            $this->give_sql_values()
        );
        return $result;
    }

    public function give_sql(){
        return $this->build_sql();
    }

    public function give_sql_values(){
        return $this->build_sql_values();
    }
    
    protected function build_sql(){
        $part_results = $this->collect_method_results($this->query_parts, '_part');
        $part_results = array_filter($part_results);
        $sql = implode(' ', $part_results);
        $this->sql = trim($sql);
        return $this->sql;
    }
    
    protected function build_sql_values() {
        $this->sql_values = $this->collect_method_results($this->query_parts, '_values');
        return $this->sql_values;
    }

    protected abstract function head_part();

    protected function fields_part(){
        return $this->fields_to_sql();
    }
    
    //--------------------------------------------
    //-- WHERE
    
    public function where($conds) {
        $conds = array_filter($conds,function($elem){
            return $elem != ''; 
        });
        if(!empty($conds)){
            if (!isset($conds[0])) {
                $conds = $this->parse_conds($conds);
            }
            $this->conds[] = $conds;
        }
        return $this;
    }
    
    private function parse_conds($conds){
        $merged_params = array();
        foreach ($conds as $field => $value) {
            if(strpos($field,'.') === false){
                $ufield = $this->table . '.' . $field;
            } else {
                $ufield = $field;
                $field = str_replace('.','_',$field);
            }
            if(strpos($value,'%') !== false){
                $merged_params[] = "$ufield LIKE :$field";
            } else {
                $merged_params[] = "$ufield = :$field";
            }
        }
        $sql_conds = array(join(' AND ', $merged_params));
        $vals = $conds;
        $conds = array_merge($sql_conds, $vals);

        return $conds;
    }

    protected function where_values(){
        $conds = $this->conds;
        $values = array();
        if (!empty($conds)) {
            if(count($this->conds) > 1){
                foreach($this->conds as $cond){
                    unset($cond[0]);
                    if (!empty($cond)) {
                        $values += $cond;
                    }
                }
            } else {
                unset($conds[0][0]);
                if (!empty($conds[0])) {
                    $values = $conds[0];
                }
            }
        }
        $values = array_filter($values,function($elem){
            return $elem != ''; 
        });
        $result = array();
        foreach($values as $key=>$val){
            if(strpos($key,'.') ==! false){
                $key = str_replace('.','_',$key);
            }
            $result[':'.$key] = $val;
        }
        return $result;
    }

    protected function where_part(){
        if(count($this->conds) > 1){
            $parts = array();
            foreach($this->conds as $cond){
                $parts[] = $cond[0];
            }
            $conds = join(' AND ', array_filter($parts));
        } elseif(count($this->conds) == 1) {
            $conds = $this->conds[0][0];
        }
        if(!empty($conds)){
            return "WHERE {$conds}";
        }
        return "";
    }
    
    //--
    //--------------------------------------------
    
    private function collect_method_results($method_names, $suffix){
        $part_results = array();
        foreach($method_names as $part){
            $method_name = $part . $suffix;
            if(method_exists($this, $method_name)){
                $result = $this->$method_name();
                if(is_array($result)){
                    $part_results = array_merge($part_results, $result);
                } else {
                    $part_results[] = $result;
                }
            }
        }
        return $part_results;
    }
    
    protected function fields_to_sql($with_tablenames=true) {
        $fields = $this->fields;
        $table = $this->table;

        array_walk($fields, function(&$field){
            if(is_array($field)){
                $field = $field['name'];
            }
        });

        if($with_tablenames){
            array_walk($fields, function(&$field) use($table) {
                    if(strpos($field, '(') === false && strpos($field,'.') === false)
                        $field = $table . '.' . $field;
                });
        }
        return implode(', ', $fields);
    }
}