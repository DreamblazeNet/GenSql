<?php
namespace DreamblazeNet\GenSql\Tests;
use PHPUnit_Framework_TestCase;
use \DreamblazeNet\GenSql\Insert;

require_once(__DIR__ . DIRECTORY_SEPARATOR. '../Common.php');
/**
 * Created by JetBrains PhpStorm.
 * User: Private
 * Date: 15.10.12
 * Time: 22:50
 * To change this template use File | Settings | File Templates.
 */
class InsertTest extends PHPUnit_Framework_TestCase
{
    public function testInsert(){
        $query = new Insert('testTable', array('id', 'name', 'value'));
        $query->values(array('id' => 1, 'name' => 'testName', 'value' => 'testValue'));
        $actual = $query->give_sql_and_values();
        $expected = array(
            'INSERT INTO testTable (testTable.id, testTable.name, testTable.value) VALUES (:id,:name,:value)',
            array(':id' => 1, ':name' => 'testName', ':value' => 'testValue')
        );
        $this->assertEquals($expected, $actual);
    }

    public function testInsertWithWildcardFields(){
        $query = new Insert('testTable');
        $query->values(array('id' => 1, 'name' => 'testName', 'value' => 'testValue'));
        $actual = $query->give_sql_and_values();
        $expected = array(
            'INSERT INTO testTable (testTable.id, testTable.name, testTable.value) VALUES (:id,:name,:value)',
            array(':id' => 1, ':name' => 'testName', ':value' => 'testValue')
        );
        $this->assertEquals($expected, $actual);
    }

    public function testInsertWithPlaceholder(){
        $query = new Insert('testTable', array('id', 'name', 'date'));
        $query->values(array('id' => 1, 'name' => 'testName', 'date' => '#NOW'));
        $actual = $query->give_sql_and_values();
        $expected = array(
            'INSERT INTO testTable (testTable.id, testTable.name, testTable.date) VALUES (:id,:name,NOW())',
            array(':id' => 1, ':name' => 'testName')
        );
        $this->assertEquals($expected, $actual);
    }
}
