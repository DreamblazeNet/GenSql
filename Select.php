<?php
namespace DreamblazeNet\GenSql;

class Select extends Base {
    private $limit;
    private $offset;
    private $joinfields = array();
    private $join;
    private $order;
    private $group_by;
    private $count = false;
    private $distinct = false;
    
    public function group_by($field){
        $this->group_by = $field;
        return $this;
    }
    
    public function distinct($distinct=true){
        $this->distinct = $distinct;
        return $this;
    }
    
    public function counting($counting=true){
        $this->count = $counting;
        return $this;
    }
    
    public function limit($limit) {
        if(is_numeric($limit) && $limit > 0){
            $this->limit = $limit;
        } else {
            $this->limit = null;
        }
        return $this;
    }
    
    public function offset($offset){
        $this->offset = (string)intval($offset);
        return $this;
    }

    public function join($ftable, Array $conditions, $fields=null, $type='INNER') {
        if(is_null($fields))
            $fields = array(array('name' => '*'));

        $this->joinfields += $fields;
        $this->join = array(
            'table' => $ftable,
            'type' => $type,
            'conditions' => $conditions);
        $fields = array_map(
            function($field) use ($ftable) {
                return $ftable . "." . $field['name'];
            }, $fields);
        $this->fields = array_merge($this->fields, $fields);
        return $this;
    }

    public function order($fields) {
        if (!is_array($fields)) {
            $order = array($fields);
        } else {
            $order = $fields;
        }
        $this->order = array_filter($order);
        return $this;
    }

    protected function head_part(){
        $head = 'SELECT';
        if($this->distinct)
                $head .= ' DISTINCT';
        return $head;
    }

    protected function fields_part(){
        if($this->count)
                return "count(*) as c";
        
        $sqlfields = $this->fields_to_sql();
        return $sqlfields;
    }

    protected function subhead_part(){
        return 'FROM ' . $this->table;
    }

    protected function order_part(){
        $order_part = '';
        if (!empty($this->order)) {
            $order_part = 'ORDER BY ';
            $a = array();
            
            foreach ($this->order as $order) {
                $pos = strpos($order, ' ');
                if ($pos === false) {
                    $field = $order;
                    $direction = '';
                } else {
                    $field = substr($order, 0, $pos);
                    $direction = substr($order, $pos);
                }
                $a[] = "$field$direction";
            }
            $order_part .= implode(',', $a);
        }
        return $order_part;
    }

    protected function limit_part(){
        $limit_part = '';
        if (isset($this->limit)) {
            if (isset($this->offset)) {
               $limit_part = "LIMIT {$this->offset},{$this->limit}";
            } else {
               $limit_part = "LIMIT {$this->limit}"; 
            }
        }
        return $limit_part;
    }

    protected function join_part(){
        $join_part = '';
        if (isset($this->join)) {
            $on_parts = array();
            foreach($this->join['conditions'] as $cond1=>$cond2) {
                $on_parts[] = $this->table . '.' . $cond1 . ' = ' . $this->join['table'] . '.' . $cond2;
            }
            $join_on_part = implode(' AND ', $on_parts);
            $join_part = "{$this->join['type']} JOIN {$this->join['table']} ON ({$join_on_part})";
        }
        return $join_part;
    }

    protected function groupby_part(){
        $groupby_part = '';
        if (isset($this->group_by)) {
            $groupby_part = "GROUP BY {$this->group_by}";
        }
        return $groupby_part;
    }
}