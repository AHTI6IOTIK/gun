<?php
class AdminController extends Controller
{
  public $view = 'admin';

  protected $controls = [
    'pages' => 'Page',
    'orders' => 'Order',
    'categories' => 'Category',
    'goods' => 'Good'
  ];

  public $title;
  function __construct(){
    parent::__construct();
    $this->title .= ' | Админка';
  }

  public function index($data){
    return ['controls' => $this->controls];
  }

  public function control($data){

    $actionId = $this->getActionId($data);

    if ($actionId['action'] === 'save') {
      $fields = [];
      foreach ($_POST as $key => $value) {
        $field = explode('_', $key, 2);
        
        if ($field[0] == $actionId['id'] ) {
          $fields['name'] = $value;
        }
        if(strpos('status_'.$actionId['id'] , $key) !== false){
            $fields['status'] = $value;
        }
        if(strpos('url_'.$actionId['id'] , $key) !== false){
          $fields['url'] = $value;
        }
      }
    }

    if ($actionId['action'] === 'create') {
      $fields = [];

      foreach ($_POST as $key => $value) {
        if ( substr($key, 0, 5) === 'new__') { 
          $fields[str_replace('new__', 'name', $key)] = $value; 
        }elseif(substr($key, 0, 5) === 'url__'){
          $fields[str_replace('url__', 'url', $key)] = $value; 
        }
      }
    } 

    switch($actionId['action']) {
      case 'create':
        foreach ($fields as $key => $value) {
          $column[] = $key;
          $masks[] =":$key";
        }

        $column_s = implode(',', $column);
        $masks_s = implode(',', $masks);

        $query = " INSERT INTO {$data['id']} ({$column_s}) VALUES ({$masks_s})";

        db::getInstance()->Query($query, $fields);
        break;

      case 'save':
        $query = 'UPDATE ' . $data['id'] . ' SET ';

        foreach ($fields as $field => $value) {
          $query .=  $field . ' = "' . $value . '",';
        }
        $query = substr($query, 0, -1) . ' WHERE id = :id';

        db::getInstance()->Query($query, ['id' => $actionId['id']]);
        break;

      case 'delete':

        if(key_exists( 'status_'.$actionId['id'], $_POST ) && $_POST['status_'.$actionId['id']] == 0 ){
          db::getInstance()->Query('DELETE FROM ' . $data['id'] . ' WHERE id = :id', ['id' => $actionId['id']]);
        }else{
          echo "<h1>Удаление не возможно, не установлен статус: удалить</h1>";
        }
        break; 
    }

    $fields = db::getInstance()->Select('desc ' . $data['id']);

    $_items = db::getInstance()->Select('select * from ' . $data['id']);
    $items = [];

    foreach ($_items as $item) {
      $items[] = new $this->controls[$data['id']]($item);
    } 

    return ['fields' => $fields, 'items' => $items];
  }

  protected function getActionId($data){ 
    foreach ($_POST as $key => $value) {
      if (strpos($key, '__save_') === 0) {
        $id = explode('__save_', $key)[1];
        $action = 'save';
        break;
      }
      if (strpos($key, '__delete_') === 0) {
        $id = explode('__delete_', $key)[1];
        $action = 'delete';
        break;
      }
      if (strpos($key, '__create') === 0) {
        $action = 'create';
        $id = 0;
      }
    }
    if (isset($id) && isset($action)) {
      return ['id' => $id, 'action' => $action];
    }
  }
}