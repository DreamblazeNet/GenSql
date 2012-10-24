<?php
namespace DreamblazeNet\GenSql\Tests;
use PHPUnit_Framework_TestCase;
use DreamblazeNet\GenSql\Select;
/**
 * Created by JetBrains PhpStorm.
 * User: Private
 * Date: 16.10.12
 * Time: 21:01
 * To change this template use File | Settings | File Templates.
 */
class BaseTest extends PHPUnit_Framework_TestCase
{
    public function testConstructionWithFields(){
        $query = new Select('testTable', array('id', 'name', 'date', 'valid'));
        $actual = $query->give_sql_and_values();
        $expected = array('SELECT testTable.id, testTable.name, testTable.date, testTable.valid FROM testTable',array());
        $this->assertEquals($expected, $actual);
    }

    public function testConstructionWithWildcard(){
        $query = new Select('testTable');
        $actual = $query->give_sql_and_values();
        $expected = array('SELECT testTable.* FROM testTable',array());
        $this->assertEquals($expected, $actual);
    }

    public function testConstructionWithAdvancedFields(){
        $query = new Select('testTable', array(
            'objId' => array('name' => 'id', 'type' => 'primary_key'),
            'objValue' => array('name' => 'value', 'type' => 'string')
        ));
        $actual = $query->give_sql_and_values();
        $expected = array('SELECT testTable.id, testTable.value FROM testTable',array());
        $this->assertEquals($expected, $actual);
    }

    public function testLikewiseWhere(){
        $query = new Select('testTable', array('id', 'name', 'date'));
        $query->where(array('name' => 'hans%'));
        $actual = $query->give_sql_and_values();
        $expected = array(
            'SELECT testTable.id, testTable.name, testTable.date FROM testTable WHERE testTable.name LIKE :name',
            array(':name' => 'hans%')
        );
        $this->assertEquals($expected, $actual);
    }

    public function testWhereWithTable(){
        $query = new Select('testTable', array('id', 'name', 'date'));
        $query->where(array('testTable2.link' => '100'));
        $actual = $query->give_sql_and_values();
        $expected = array(
            'SELECT testTable.id, testTable.name, testTable.date FROM testTable WHERE testTable2.link = :testTable2_link',
            array(':testTable2_link' => '100')
        );
        $this->assertEquals($expected, $actual);
    }

    public function testMultibleWhere(){
        $query = new Select('testTable', array('id', 'name', 'date'));
        $query->where(array('id' => '100'));
        $query->where(array('name' => 'hans'));
        $actual = $query->give_sql_and_values();
        $expected = array(
            'SELECT testTable.id, testTable.name, testTable.date FROM testTable WHERE testTable.id = :id AND testTable.name = :name',
            array(':id' => '100', ':name' => 'hans')
        );
        $this->assertEquals($expected, $actual);
    }
}
