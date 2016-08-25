<?php
/**
 * Created by PhpStorm.
 * User: chao
 * Date: 2016/5/19
 * Time: 11:20
 */

namespace Games\Controller;


use Think\Controller;
use Think\Model;
use Think\Page;
use Think\Upload;

class GamesController extends Controller
{
    /**
     * 游戏列表
     * @author  zhaoyingchao
     * @date    2016-5-19
     * @time    11:28
     * @since   v1.0
     */

    public function gameList(){
        $gameName=I('request.searchkey');
        $gradeId = I('request.grade_id');
        $GamesModel = M('Games');
        $GradeModel = M('Grade');
        if(!empty($gameName))
            $sqldata['game_name']=$gameName;
        if(!empty($gradeId))
            $sqldata['grade_id']=I('request.grade_id');
        $count = $GamesModel->where($sqldata) -> count();
        $Page = new Page($count,10);
        $pages = $Page ->show();
        $data = $GamesModel ->where($sqldata) -> limit($Page -> firstRow.','.$Page -> listRows) -> select();
        $grade=$GradeModel->select();
        $gradeList=array();
        foreach ($grade as $k=>$val){
            $gradeList[$val['id']]=$val['grade_name'];
        }
        $gameList = array();
        foreach($data as $key => $value){
            $gameList[$key]['id'] = $value['id'];
            $gameList[$key]['game_name'] = $value['game_name'];
            $gameList[$key]['total'] = $value['total'];
            $gameList[$key]['point'] = $value['point'];
            $gameList[$key]['grade_name'] = $gradeList[$value['grade_id']];
            $gameList[$key]['status'] = $value['status'];
            $gameList[$key]['buy_status'] = $value['buy_status']==0?'可购买':'点数售完';
            $gameList[$key]['create_time'] = $value['create_time'];
        }
        $this -> assign('game_list',$gameList);
        $this -> assign('grade_list',$gradeList);
        $this -> assign('pages',$pages);
        $this -> display();
    }
    /**
     * 新增游戏
     * @author  zhaoyingchao
     * @date    2016-5-19
     * @time    12:00
     * @since   v1.0
     */
    public function gameAdd(){
        if(IS_POST){
            $data = array();
            $data['game_name'] = I('post.game_name');
            $data['total'] = I('post.total');
            $data['point'] = I('post.point');
            $data['grade_id'] = I('post.grade_id',0);
            $data['description'] = I('post.description');
            $data['rules'] = I('post.rules');
            $data['status'] = I('post.status');
            $data['buy_status'] = 0;
            $data['create_time'] = date('Y-m-d H:i:s',time());

            if(!ctype_digit($data['total'])){
                $this -> error('总数字段应为整数！');
            }
            if($_FILES['thumbnail']['name']){
                //图片上传设置

                $config = array(
                    'maxSize' => 5145728,
                    'savePath' => 'Public/games/upload/',
                    'rootPath' => './',
                    'exts' => array('jpg','gif','png','jpeg'),
                    'autoSub' => false,
                );
                
                $Upload = new Upload($config);
                $images = $Upload -> upload($_FILES);
                //判断是否有图
                if($images){
                    $data['thumbnail'] = $images['thumbnail']['savename'];
                }else{
                    $this -> error($Upload->getError());//获取失败信息
                }
            }
            $Model = new Model();
            $Model -> startTrans();
            $result = $Model ->table('__GAMES__') -> add($data);
            if($result){
                $datalist = array();
                $count = 0;
                $return = array();
                $total = intval($data['total']);
                while ($count < $total)
                {
                    $return[] = mt_rand(0, $total-1);
                    $return = array_flip(array_flip($return));
                    $count = count($return);
                }
                foreach($return as $value)
                {
                    $num = strval(10000000+$value);
                    $datalist[] = array('game_id'=>$result,'num'=>$num);
                }
                $re = $Model ->table('__LOTTERY__') ->addAll($datalist);
                if($re){
                    $Model -> commit();
                    $this -> redirect('Games/gameList');
                }else{
                    $Model -> rollback();
                    $this -> error('新增失败！');
                }
            }else{
                $Model -> rollback();
                $this -> error('新增失败！');
            }
        }else{
            $GradeModel = M('Grade');
            $gradeList = $GradeModel -> field('id,grade_name') -> select();
            $this -> assign('grade_list',$gradeList);
            $this -> display();
        }

    }
    /**
     * 编辑游戏
     * @author  zhaoyingchao
     * @date    2016-5-19
     * @time    13:46
     * @since   v1.0
     */
    public function gameEdit(){
        $GamesModel = M('Games');
        if(IS_POST){
            $id = I('post.id');
            $gameTotal=$GamesModel->where(array('id'=>$id))->getField('total');
            $data = array();
            $data['game_name'] = I('post.game_name');
            $data['total'] = I('post.total');
            $data['point'] = I('post.point');
            $data['grade_id'] = I('post.grade_id',0);
            $data['description'] = I('post.description');
            $data['rules'] = I('post.rules');
            $data['status'] = I('post.status');
            $data['buy_status'] = I('post.buy_status');
            $data['create_time'] = date('Y-m-d H:i:s',time());
            if(!ctype_digit($data['total'])){
                $this -> error('总数字段应为整数！');
            }
            if($_FILES['thumbnail']['name']){
                //图片上传设置

                $config = array(
                    'maxSize' => 5145728,
                    'savePath' => 'Public/games/upload/',
                    'rootPath' => './',
                    'exts' => array('jpg','gif','png','jpeg'),
                    'autoSub' => false,
                );

                $Upload = new Upload($config);
                $images = $Upload -> upload($_FILES);
                //判断是否有图
                if($images){
                    $data['thumbnail'] = $images['thumbnail']['savename'];
                }else{
                    $this -> error($Upload->getError());//获取失败信息
                }
            }
            $Model = new Model();
            $Model -> startTrans();
            if(intval($data['total']!=$gameTotal)) {
                $re = $Model->table('__LOTTERY__')->where(array('game_id' => $id))->delete();
                if ($re !== false) {
                    $result = $Model->table('__GAMES__')->where(array('id' => $id))->data($data)->save();
                    if ($result !== false) {
                        $datalist = array();
                        $count = 0;
                        $return = array();
                        $total = intval($data['total']);
                        while ($count < $total) {
                            $return[] = mt_rand(0, $total - 1);
                            $return = array_flip(array_flip($return));
                            $count = count($return);
                        }
                        foreach ($return as $value) {
                            $num = strval(10000000 + $value);
                            $datalist[] = array('game_id' => $id, 'num' => $num);
                        }
                        $res = $Model->table('__LOTTERY__')->addAll($datalist);
                        if ($res) {
                            $Model->commit();
                            $this->redirect('Games/gameList');
                        } else {
                            $Model->rollback();
                            $this->error('编辑失败！');
                        }
                    } else {
                        $Model->rollback();
                        $this->error('编辑失败！');
                    }
                } else {
                    $Model->rollback();
                    $this->error('编辑失败！');
                }
            }else{
                $result = $Model->table('__GAMES__')->where(array('id' => $id))->data($data)->save();
                if ($result !== false) {
                    $Model->commit();
                    $this->redirect('Games/gameList');
                }else{
                    $Model->rollback();
                    $this->error('编辑失败！');
                }
            }
        }else{
            $id = I('request.id');
            $GradeModel = M('Grade');
            $gradeList = $GradeModel -> field('id,grade_name') -> select();
            $gameInfo = $GamesModel -> where(array('id'=>$id)) -> find();
            $this -> assign('game_info',$gameInfo);
            $this -> assign('grade_list',$gradeList);
            $this -> display();
        }
    }

    /**
     * 删除游戏
     * @author  zhaoyingchao
     * @date    2016-5-19
     * @time    13:46
     * @since   v1.0
     */
    public function gameDelete(){
        $id = I('request.id');
        $Model = new Model();
        $Model -> startTrans();
        $re = $Model -> table('__GAMES__') -> where(array('id'=>$id)) -> delete();
        if($re !== false){
            $result = $Model -> table('__LOTTERY__') -> where(array('game_id'=>$id)) -> delete();
            if($result !== false){
                $Model -> commit();
                $this -> redirect('Games/gameList');
            }else{
                $Model -> rollback();
                $this -> error('删除失败！');
            }
        }else{
            $Model -> rollback();
            $this -> error('删除失败！');
        }
    }
    /**
     * ajax切换开启状态
     * @author zhaoyingchao
     */
    public function ajaxStatusChange(){
        $id = I('post.id');
        $data['status'] = I('post.status');

        $GamesModel = M('Games');
        $result = $GamesModel -> where(array('id'=>$id)) ->data($data) -> save();
        if($result !== false){
            $rudata['result'] = 'true';
            $rudata['msg'] = '切换状态成功！';
        }else{
            $rudata['result'] = 'false';
            $rudata['msg'] = '切换状态失败！';
        }
        $jsondData = json_encode($rudata);
        echo $jsondData;
    }
    /**
     * ajax更新游戏彩票列表
     * @author zhaoyingchao
     */
    public function ajaxUpdateLottery(){
        $id = I("post.id");
        $Model = new Model();
        $Model -> startTrans();
        $gameInfo = $Model->table('__GAMES__')->where(array('id'=>$id))->find();
        $re = $Model ->table('__LOTTERY__') -> where(array('game_id' => $id))->delete();
        if($re !== false) {
            $datalist = array();
            $count = 0;
            $return = array();
            $total = intval($gameInfo['total']);
            while ($count < $total)
            {
                $return[] = mt_rand(0, $total-1);
                $return = array_flip(array_flip($return));
                $count = count($return);
            }
            foreach($return as $value)
            {
                $num = strval(10000000+$value);
                $datalist[] = array('game_id'=>$id,'num'=>$num);
            }
            $res = $Model ->table('__LOTTERY__') ->addAll($datalist);
            if($res){
                $Model -> commit();
                $redata['result']='true';
                $redata['msg']='更新成功';
                $this->ajaxReturn($redata);
            }else{
                $Model -> rollback();
                $redata['result']='false';
                $redata['msg']='更新失败';
                $this -> ajaxReturn($redata);
            }
        }else{
            $Model -> rollback();
            $redata['result']='false';
            $redata['msg']='更新失败';
            $this -> ajaxReturn($redata);
        }
    }

    public function showWinner(){
        $Model = new Model();
        $sql = "SELECT w.card_num,w.lottery,w.create_time,c.company_name,g.game_name,g.thumbnail FROM __PREFIX__games g,__PREFIX__winners_list w LEFT JOIN __PREFIX__company c ON w.company_id =
            c.card_company_id WHERE g.id = w.game_id";
        $winner_info = $Model -> query($sql);
        var_dump($winner_info);
    }
}