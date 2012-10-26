<?php
namespace DreamblazeNet\GenSql;
/**
 * Created by JetBrains PhpStorm.
 * User: mriedmann
 * Date: 26.10.12
 * Time: 02:26
 * To change this template use File | Settings | File Templates.
 */
interface Query
{
    public function give_sql();
    public function give_sql_values();
    public function give_sql_and_values();
}
