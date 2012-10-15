<?php
namespace DreamblazeNet\GenSql;

class Delete extends Base
{
    protected function head_part()
    {
        return "DELETE FROM " . $this->table;
    }

    protected function fields_to_sql()
    {
        return '';
    }


}
