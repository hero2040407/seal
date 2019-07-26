<?php
/**
 * Created by PhpStorm.
 * UserModel: LENOVO
 * Date: 2019/1/16
 * Time: 11:11
 */

namespace seal;

class Request
{
    /**
     * 对象实例
     * @var object
     */
    private static $instance;
    /**
     * 为了防止在请求的过程发生一些不愉快事情，全给它private
     * @var array
     */
    private $server;
    private $header;
    private $request;
    private $post;
    private $get;
    private $cookie;
    private $files;
    private $tmpfiles;
    private $rawContent;
    private $getData;
    private $module;
    private $controller;
    private $action;

    /**
     * @return mixed
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * @param mixed $module
     */
    public function setModule($module): void
    {
        $this->module = $module;
    }

    /**
     * @return mixed
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * @param mixed $controller
     */
    public function setController($controller): void
    {
        $this->controller = $controller;
    }

    /**
     * @return mixed
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @param mixed $action
     */
    public function setAction($action): void
    {
        $this->action = $action;
    }



    private function __construct()
    {
    }

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    //先拿到$request，然后挨个给它变身
    public function set($request, $router)
    {
        $this->server = $request->server;
        $this->header = $request->header;
        $this->tmpfiles = $request->tmpfiles;
        $this->request = $request->request;
        $this->cookie = $request->cookie;
        $this->get = $request->get ?? [];
        $this->files = $request->files;
        $this->post = $request->post ?? [];
        $this->rawContent = $request->rawContent();
        $this->getData = $request->getData();
        $this->module = $router['module'];
        $this->controller = $router['controller'];
        $this->action = $router['action'];

        $raw_content = json_decode($this->rawContent, true);
        if (is_array($raw_content)) {
            $param = array_merge($this->get, $this->post, $raw_content);
        }
        else
            $param = array_merge($this->get, $this->post);

        unset($raw_content);
        foreach ($param as $key => $value) {
            $this->$key = $value;
        }
    }
    /*
    // 以上变身方法也可以用魔术方法，我写了，可就是不想用它。
    public function __set($name,$value){
        $this->$name = $value;
    }
    */
    //变身后它就不是废物了，那就得让小伙伴们能使用它，这里使用了这么一个魔术方法。
    public function __get($name)
    {
        if (isset($this->$name))
        return $this->$name;
    }

//    public function __set($name, $value)
//    {
//        $this->$name = $value;
//    }
}