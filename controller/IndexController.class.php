<?php

class IndexController extends Controller
{
    public $view = 'index';
    public $title;

    function __construct()
    {
        parent::__construct();
        $this->title .= ' | Главная';
    }

    public function getPagesMenu(){
      $_items = db::getInstance()->Select('select * from pages where status = '.Status::Active);
 
      return ['items' => $_items];
    }


}