<?php
namespace DreamblazeNet\GenSql\Tests;
use PHPUnit_Framework_TestCase;
use \DreamblazeNet\GenSql\Update;

/**
 * Created by JetBrains PhpStorm.
 * User: Private
 * Date: 15.10.12
 * Time: 22:50
 * To change this template use File | Settings | File Templates.
 */
class UpdateTest extends PHPUnit_Framework_TestCase
{
    public function testUpdate(){
        $query = new Update('testTable', array('id', 'name', 'value'));
        $query->set(array('name' => 'testName', 'value' => 'testValue'));
        $actual = $query->give_sql_and_values();
        $expected = array(
            'UPDATE testTable SET name = :name ,value = :value',
            array(':name' => 'testName', ':value' => 'testValue')
        );
        $this->assertEquals($expected, $actual);
    }
}
