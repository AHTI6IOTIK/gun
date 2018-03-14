<?php
class db
{
    private static $_instance = null;

    private $db; // Ресурс работы с БД

    /*
     * Получаем объект для работы с БД
     */
    public static function getInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new db();
        }
        return self::$_instance;
    }

    /*
     * Запрещаем копировать объект
     */
    private function __construct() {}
    private function __sleep() {}
    private function __wakeup() {}
    private function __clone() {}

    /*
     * Выполняем соединение с базой данных
     */
    public function Connect($user, $password, $base, $host = 'localhost', $port = 3306)
    {
        // Формируем строку соединения с сервером
        $connectString = 'mysql:host=' . $host . ';port= ' . $port . ';dbname=' . $base . ';charset=UTF8;';
        setlocale(LC_ALL, 'ru_RU.UTF8');    
        $this->db = new PDO($connectString, $user, $password);
        $this->db->exec('SET NAMES UTF8');
        $this->db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    }

    /*
     * Выполнить запрос к БД
     */
    public function Query($query, $params = array())
    {
        $res = $this->db->prepare($query);
        $res->execute($params);
        if($res->errorCode() != PDO::ERR_NONE){
            $info = $res->errorInfo();
            die($info[2]);
        } 
        return $res;
         
    }

    /*
     * Выполнить запрос с выборкой данных
     */
 
        public function Select($query, $params = array()){
        $res = $this->db->prepare($query); 
        $res->execute($params);
        
        if($res->errorCode() != PDO::ERR_NONE){
            $info = $res->errorInfo();
            die($info[2]);
        }else{
            return $res->fetchAll();
        }              
    }
}
?>
