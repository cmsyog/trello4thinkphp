<?php
namespace Admin\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){

        if(!islogin()){
            $this->redirect('/admin.php/Login');
        }

        $model = M('boards');
        $data = $model -> select();
//        var_dump(cookie('user_info'));
//        var_dump($_SERVER);
//        var_dump(session('use_info'));
        $this -> assign('data',$data);
        $this -> assign('user_info',cookie('user_info'));
        $this -> display();
    }

    //添加版
    public function addBoard(){
        if(IS_POST&&!empty($_POST['name'])&&!empty($_POST['description'])){
            $data['name'] = $_POST['name'];
            $data['description'] = $_POST['description'];
            $data['open'] = 1;
            $data['board_visibility'] = 1;
            $data['created_at'] = date('Y-m-d H:i:s');
            $data['updated_at'] =date('Y-m-d H:i:s');

            M()-> startTrans();
            $model = M('boards');
            try{
                if($model -> create($data)){
                    $model -> add();
                    $bid =  $model -> getLastInsID();
                    //操作关联表board_members
                    $bm_model = M('board_members');
                    $bm_data['board_id'] = $bid;
                    $bm_data['created_at'] = date('Y-m-d H:i:s');
                    $bm_data['updated_at'] =date('Y-m-d H:i:s');
                    $bm_model -> create($bm_data);
                    $bm_model-> add();
                    //操作动作表board_activities
                    $ba_model = M('board_activities');
                    $ba_data['description'] = 'Add Board';
                    $ba_data['board_id'] = $bid;
                    $ba_data['member_id'] = 1;
                    $ba_data['created_at'] = date('Y-m-d H:i:s');
                    $ba_data['updated_at'] =date('Y-m-d H:i:s');
                    $ba_model ->create($ba_data);
                    $res = $ba_model -> add();
                    if($res > 0){
                        $this -> success('添加成功！','/admin.php/Index/index');
                    }else{
                        $this -> error('新增失败！！');
                    }
                };
                M() -> commit();
            }catch (\Exception $e){
                M() -> rollback();
            }
        }else{
            $this->error('参数不合法');
        }
    }



    public function show(){
        echo 'enjoy the show~';
    }
}