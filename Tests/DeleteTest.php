<?php
namespace DreamblazeNet\GenSql\Tests;
use PHPUnit_Framework_TestCase;
use \DreamblazeNet\GenSql\Delete;
/**
 * Created by JetBrains PhpStorm.
 * User: mriedmann
 * Date: 09.10.12
 * Time: 20:00
 * To change this template use File | Settings | File Templates.
 */
class DeleteTest extends PHPUnit_Framework_TestCase
{
    public function testDelete(){
        $query = new Delete('testTable', array('id', 'name'));
        $query->where(array('id' => 1));
        $actual = $query->give_sql_and_values();
        $expected = array('DELETE FROM testTable WHERE testTable.id = :id', array(':id' => 1));
        $this->assertEquals($expected, $actual);
    }
}
