<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){
        $this -> display(); 
    }

    public function demo()
    {
        echo 111;
    }

    public function show(){
        echo 'enjoy the show~';
    }
}