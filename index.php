<?php

require 'JsonHandler.php';

use JsonHandler\JsonHandler;

class A
{
	private $id;
	private $name;
	public $birthday;
	public $description;
	public $_A;

	public function getId(){
		return $this->id;
	}

	public function setId($id){
		$this->id = $id;
	}

	public function getName(){
		return $this->name;
	}

	public function setName($name){
		$this->name = $name;
	}

	
}
$a1 = new A();
$a1 -> setId('1');
$a1 -> setName('Paul');
$a1 -> birthday = '03/01/1991';
$a1 -> description = 'Test description';



$a2 = new A();
$a2 -> setId('2');
$a2 -> setName('Aaron Paul');
$a2 -> birthday = '03/01/1901';
$a2 -> description = 'Teste description 2';

$a1 -> _A = $a2;


//Adding the objects into array
$ListObjects[] = $a1;
$ListObjects[] = $a2;

//Creating JSON from list.
$jsonHandler = new JsonHandler();

$fields = array('id', 'name', '_A');

$jsonHandler -> CreateFromListObject($ListObjects, $fields, 'result', array(), true);
$jsonHandler -> PrintJson();

?>