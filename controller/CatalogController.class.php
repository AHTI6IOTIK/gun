<?php

/**
* 
*/
class CatalogController extends Controller
{
  public $view = 'catalog';
  
  public function __construct(){
    parent::__construct();
    $this->title .= " | Каталог";
  }

  public function index($data){
    var_dump($data);
  }
}

?>