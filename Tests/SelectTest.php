<?php
namespace Dreamblaze\GenSql\Tests;
use PHPUnit_Framework_TestCase;
use \Dreamblaze\GenSql\Select;
/**
 * Created by JetBrains PhpStorm.
 * User: mriedmann
 * Date: 09.10.12
 * Time: 20:00
 * To change this template use File | Settings | File Templates.
 */
class SelectTest extends PHPUnit_Framework_TestCase
{
    public function testConstructionWithFields(){
        $query = $this->createQuery(array('id', 'name', 'date', 'valid'));
        $actual = $query->give_sql_and_values();
        $expected = array('SELECT testTable.id, testTable.name, testTable.date, testTable.valid FROM testTable',array());
        $this->assertEquals($expected, $actual);
    }

    public function testConstructionWithWildcard(){
        $query = $this->createQuery();
        $actual = $query->give_sql_and_values();
        $expected = array('SELECT testTable.* FROM testTable',array());
        $this->assertEquals($expected, $actual);
    }

    public function testWhere(){
        $query = $this->createQuery(array('id', 'name', 'date', 'valid'));
        $query->where(array('id' => 1));
        $actual = $query->give_sql_and_values();
        $expected = array(
            'SELECT testTable.id, testTable.name, testTable.date, testTable.valid ' .
            'FROM testTable ' .
            "WHERE testTable.id = :id",
            array(':id' => 1));
        $this->assertEquals($expected, $actual);
    }

    public function testLimit(){
        $query = $this->createQuery(array('id', 'name', 'date', 'valid'));
        $query->limit(23);
        $actual = $query->give_sql_and_values();
        $expected = array(
            'SELECT testTable.id, testTable.name, testTable.date, testTable.valid ' .
                'FROM testTable ' .
                "LIMIT 23",
            array());
        $this->assertEquals($expected, $actual);
    }

    public function testResetLimit(){
        $query = $this->createQuery(array('id', 'name', 'date', 'valid'));
        $query->limit(0);
        $actual = $query->give_sql_and_values();
        $expected = array(
            'SELECT testTable.id, testTable.name, testTable.date, testTable.valid ' .
                'FROM testTable',
            array());
        $this->assertEquals($expected, $actual);
    }

    public function testLimitWithOffset(){
        $query = $this->createQuery(array('id', 'name', 'date', 'valid'));
        $query->limit(23);
        $query->offset(42);
        $actual = $query->give_sql_and_values();
        $expected = array(
            'SELECT testTable.id, testTable.name, testTable.date, testTable.valid ' .
                'FROM testTable ' .
                "LIMIT 42,23",
            array());
        $this->assertEquals($expected, $actual);
    }

    public function testJoin(){
        $query = $this->createQuery();
        $query->join('joinTable', array('id' => 'test_id'));
        $actual = $query->give_sql_and_values();
        $expected = array(
            'SELECT testTable.*, joinTable.* ' .
            'FROM testTable ' .
            'INNER JOIN joinTable ON (testTable.id = joinTable.test_id)',
            array());
        $this->assertEquals($expected, $actual);
    }

    public function testOrder(){
        $query = $this->createQuery();
        $query->order('date');
        $actual = $query->give_sql_and_values();
        $expected = array(
            'SELECT testTable.* ' .
            'FROM testTable ' .
            'ORDER BY date',
            array());
        $this->assertEquals($expected, $actual);
    }

    public function testOrderWithDirection(){
        $query = $this->createQuery();
        $query->order('date DESC');
        $actual = $query->give_sql_and_values();
        $expected = array(
            'SELECT testTable.* ' .
                'FROM testTable ' .
                'ORDER BY date DESC',
            array());
        $this->assertEquals($expected, $actual);
    }

    public function testOrderWithMultibleFields(){
        $query = $this->createQuery();
        $query->order(array('date', 'id'));
        $actual = $query->give_sql_and_values();
        $expected = array(
            'SELECT testTable.* ' .
                'FROM testTable ' .
                'ORDER BY date,id',
            array());
        $this->assertEquals($expected, $actual);
    }

    public function testCounting(){
        $query = $this->createQuery();
        $query->counting();
        $actual = $query->give_sql_and_values();
        $expected = array(
            'SELECT count(*) as c ' .
            'FROM testTable',
            array());
        $this->assertEquals($expected, $actual);
    }

    public function testDistinct(){
        $query = $this->createQuery();
        $query->distinct();
        $actual = $query->give_sql_and_values();
        $expected = array(
            'SELECT DISTINCT testTable.* ' .
            'FROM testTable',
            array());
        $this->assertEquals($expected, $actual);
    }

    public function testGroupBy(){
        $query = $this->createQuery();
        $query->group_by('group_id');
        $actual = $query->give_sql_and_values();
        $expected = array(
            'SELECT testTable.* ' .
                'FROM testTable ' .
            'GROUP BY group_id',
            array());
        $this->assertEquals($expected, $actual);
    }

    private function createQuery($fields=null){
        return new Select('testTable', $fields);
    }
}
