<?php
/**
 * Created by PhpStorm.
 * User: guopj
 * Date: 2015/8/25
 * Time: 9:04
 */
namespace Admin\Controller;
use Think\Controller;
class BoardController extends Controller {

    public function index(){

        if(!islogin()){
            $this->redirect('/admin.php/Login');
        }

        if(!empty($_GET['bid'])){
            $this->display();
        }else{
            $this -> error('参数不能为空！');
        }

    }

    /*
     *ajx获取board
     * */
    public function getBoard(){
        if(IS_POST){
            $id = $_REQUEST['b'];
            $model = M('lists');
            $list = $model ->select();
            //card获取
            $c_model = M('cards');
            foreach($list as &$v){
                $c_data= $c_model ->where('list_id='.$v['id'])-> select();
                $v['cards'] = isset($c_data)?$c_data:array();
            }
            $data['lists'] = $list;
            $this -> ajaxReturn($data,'json');
        }else{
            $data = array();
            $this->ajaxReturn($data,'json');
        }
    }

    public function addList(){

        //取参数
        $bid = $_POST['b'];
        $title = $_POST['t'];
        $user_id = 1;

        if(IS_POST&&!empty($bid)&&!empty($title)){
            $l_model = M('lists');
            M()->startTrans();

            $data['title'] = $title;
            $data['board_id'] = $bid;
            $data['position'] = $l_model->count();
            $data['created_at'] = date('Y-m-d H:i:s');
            $data['updated_at'] = date('Y-m-d H:i:s');

            try{
                $l_model -> create($data);
                $l_model -> add();
                $lid = $l_model->getLastInsID();
                //动作表操作
                $ba_model = M('board_activities');
                $ba_data['description'] ='Add List'.$lid;
                $ba_data['board_id'] = $bid;
                $ba_data['member_id'] = $user_id;
                $ba_data['created_at'] = date('Y-m-d H:i:s');
                $ba_data['updated_at'] = date('Y-m-d H:i:s');
                $ba_model-> create($ba_data);
                $res = $ba_model -> add();
                if($res>0){
                    M() -> commit();
                    $return['id'] = $lid;
                    $this ->ajaxReturn($return,'json');
                }
            }catch(\Exception $e){
                M()->rollback();
                $this->ajaxReturn('添加错误','json');
            }
        }else{
                $this->ajaxReturn('参数错误','json');
        }

    }

    public function delList(){

        //取参数
        $lid = $_POST['lid'];
        $user_id = 1;
        if(IS_POST&&!empty($lid)&&is_numeric($lid)){
            M()->startTrans();
            try{
                $l_model = M('lists');
                $res = $l_model -> find($lid);
                if($res){
                    $l_model -> delete();
                    //动作表操作
                    $ba_model = M('board_activities');
                    $ba_data['description'] ='delete List'.$lid;
                    $ba_data['board_id'] = $res['board_id'];
                    $ba_data['member_id'] = $user_id;
                    $ba_data['created_at'] = date('Y-m-d H:i:s');
                    $ba_data['updated_at'] = date('Y-m-d H:i:s');
                    $ba_model-> create($ba_data);
                    $ba_model -> add();

                    //list位置position更新
                    $l_model -> where('position > '.$res['position']) -> setDec('position',1);

//                    echo $l_model -> getlastsql();
                    M()->commit();
                }
            }catch (\Exception $e){
                M()->rollback();
            }
        }

    }

    public function listPosition(){

        //获取变量
        $lid = $_POST['lid'];
        $n_position = $_POST['np'];
        $bid = $_POST['b'];
        $user_id = 1;

        if(IS_POST&&!empty($lid)&&!empty($bid)&&isset($n_position)){
            M()->startTrans();
            try{
                $l_model = M('lists');
                $res = $l_model -> find($lid);
                if($res){
                    //受影响list位置position更新，两步走
                    $l_model -> where('position > '.$res['position']) -> setDec('position',1);
                    $l_model ->where('position >= '.$n_position)->setInc('position',1);
                    //先空出来位置，再position更新
                    $l_model -> position = $n_position;
                    $l_model -> save();
//                    echo $l_model -> getlastsql();
                    M()->commit();
                }
            }catch (\Exception $e){
                M()->rollback();
            }
        }else{
        }

    }

    public function addCard(){

        //取参数
        $lid = $_POST['l'];
        $title = $_POST['t'];
        $user_id = 1;

        if(IS_POST&&!empty($lid)&&!empty($title)){
            $c_model = M('cards');
            M()->startTrans();

            $data['title'] = $title;
            $data['list_id'] = $lid;
            $data['position'] = $c_model -> where('list_id ='.$lid) ->count();
            $data['created_at'] = date('Y-m-d H:i:s');
            $data['updated_at'] = date('Y-m-d H:i:s');

            try{
                $c_model -> create($data);
                $c_model -> add();
                $cid = $c_model->getLastInsID();

                //关联表操作
                $cm_model = M('card_members');
                $cm_data ['card_id'] = $cid;
                $cm_data['member_id'] = $user_id;
                $cm_data['created_at'] = date('Y-m-d H:i:s');
                $cm_data['updated_at'] = date('Y-m-d H:i:s');
                $cm_model ->create($cm_data);
                $cm_model -> add();

                //动作表操作
                $ca_model = M('card_activities');
                $ca_data['description'] ='Add Card'.$cid;
                $ca_data['card_id'] = $cid;
                $ca_data['member_id'] = $user_id;
                $ca_data['created_at'] = date('Y-m-d H:i:s');
                $ca_data['updated_at'] = date('Y-m-d H:i:s');
                $ca_model-> create($ca_data);
                $res = $ca_model -> add();

                if($res>0){
                    M() -> commit();
                    $return['id'] = $cid;
                    $this ->ajaxReturn($return,'json');
                }
            }catch(\Exception $e){
                M()->rollback();
                $this->ajaxReturn('添加错误','json');
            }
        }else{
            $this->ajaxReturn('参数错误','json');
        }
    }

    public function cardPosition(){

        //接受变量
        $cid = $_POST['cid'];
        $n_lid = $_POST['nl'];
        $n_position = $_POST['np'];

        if(IS_POST&&!empty($cid)&&!empty($n_lid)&&isset($n_position)){

            M()-> startTrans();
            $c_model = M('cards');
            $c_info = $c_model -> find($cid);
            $c_model -> position = $n_position;
            $c_model -> list_id = $n_lid;
            try{
                //位置变化3部走
                $res=$c_model ->where("position> '%d' AND list_id = '%s'",$c_info['position'],$c_info['list_id']) -> setDec('position',1);
//                var_dump($c_model->getlastsql());
                $res=$c_model -> where("position>='%d' AND list_id = '%s'",$n_position,$n_lid)->setInc('position',1);
                $res =  $c_model-> save();

                M()->commit();
            }catch (\Exception $e){
                M() -> rollback();
            }
        }
    }

    public function cardDescription(){

        //接受变量
        $cid = $_POST['cid'];
        $description = $_POST['cardDesc'];

        if(IS_POST&&!empty($cid)){
            M() -> startTrans();
            $c_model = M('cards');
            $c_model -> find($cid);
            $c_model -> description = $description;
            try{
                $c_model -> save();
                M() -> commit();
            }catch (\Exception $e){}
            M() -> rollback();
        }

    }

    public function delCard(){

        //接受变量
        $cid = $_POST['cid'];
        $user_id = 1;
        if(IS_POST&&!empty($cid)){
            M() -> startTrans();
            $c_model = M('cards');
            $info = $c_model -> find($cid);
            try{
                $c_model -> delete();
                //动作表
                $ca_model = M('card_activities');
                $ca_model -> description = 'delete card'.$cid;
                $ca_model -> card_id = $cid;
                $ca_model ->member_id = $user_id;
                $ca_model -> created_at = date('Y-m-d H:i:s');
                $ca_model -> updated_at = date('Y-m-d H:i:s');
                $ca_model -> add();
                echo $ca_model -> getlastsql();
                //关系表
                $cm_model = M('card_members');
                $cm_model -> where('card_id = %d',$cid)->delete();
                //position变化
                $c_model -> where('position>%d and list_id = %s',$cid,$info['list_id']) -> setDec('position',1);
                M() -> commit();
            }catch (\Exception $e){
                M() -> rollback();
            }

        }

    }

}