<?php
/**
 * Created by PhpStorm.
 * User: chao
 * Date: 2016/5/19
 * Time: 17:56
 */

namespace Games\Controller;


use Think\Controller;
use Think\Model;

class GamesApiController extends Controller
{
    /**
     * 游戏列表接口(右侧渲染)
     */
    public function gameList(){
        $GamesModel = M('Games');
        $CompanyModel = M('Company');
        $UserModel = M('Users');
        $GradeModel = M('Grade');
        
        $gameGlobal = $GamesModel ->where(array('grade_id'=>1,'status'=>1)) -> limit(2) -> select();
        $gameCompany = $GamesModel ->where(array('grade_id'=>2,'status'=>1)) -> limit(2) -> select();


        $username = $_SESSION['user_name'];
        $int_cid = $db->getOne('SELECT company_id FROM '.$ecs->table('users')." WHERE user_name = '$username'");
        $company_info = $db->getRow('SELECT * FROM '.$ecs->table('company')." WHERE card_company_id='".$int_cid."'");
        $grade_name = $db->getOne('SELECT grade_name FROM '.$ecs->table('grade')." WHERE id='".$company_info['grade_id']."'");
        $company_info['grade_name'] = $grade_name;
        $game_global = $db->getAll('SELECT * FROM '.$ecs->table('games')." WHERE status=1 AND grade_id=1 ORDER BY create_time DESC LIMIT 2");
        $game_company = $db->getAll('SELECT * FROM '.$ecs->table('games')." WHERE status=1 AND grade_id='".$company_info['grade_id']."' ORDER BY create_time DESC LIMIT 5");

        $smarty->assign('company_info',$company_info);
        $smarty->assign('game_global',$game_global);
        $smarty->assign('game_company',$game_company);
        $rudata['game_global'] = $gameGlobal;
        $rudata['game_company'] = $gameCompany;
        $rudata['result'] = 'true';
        var_dump($rudata);
        exit;
        $jsondData = json_encode($rudata);
        echo $jsondData;


    }
    
    /**
     * 游戏详情接口
     * @author  zhaoyingchao
     * @param   int game_id  游戏id
     */
    public function getGame(){
        $id = I('request.game_id');
        $GameModel = M('Games');
        $gameInfo = $GameModel -> where(array('id'=>$id)) -> find();
        if($gameInfo){
            $rudata['result'] = 'true';
            $rudata['game_info'] = $gameInfo;
            $rudata['msg'] = '成功！';
        }else{
            $rudata['result'] = 'false';
            $rudata['msg'] = '获取失败，请重试！';
        }
        $jsondData = json_encode($rudata);
        echo $jsondData;
    }

    /**
     * 抢购接口
     * @author  zhaoyingchao
     * @param   int game_id  游戏id
     */
    public function purchase(){
//        $PartModel = M('Participation');
//        $LotteryModel = M('Lottery');
//        $GamesModel = M('Games');
        $Model = new Model();
        $card_pass =I('request.card_pass');
        $num =I('request.num');

        $data = array();
        $data['game_id'] = I('request.game_id');
        $data['card_num'] =I('request.card_num');
        $data['company_id'] =I('request.company_id');
        $data['buy_time'] = date('Y-m-d H:i:s',time());
        $count = $Model ->table('__PARTICIPATION__') -> where(array('game_id' => $data['game_id'])) -> count();
        $total = $Model ->table('__GAMES__') -> where(array('id' => $data['game_id'])) ->getField('total');
        $surplus = intval($total) - intval($count);
        if($surplus<$num){
            $rudata['result'] = 'false';
            $rudata['msg'] = '商品剩余量不足！';
            $this -> ajaxReturn($rudata);
        }
        $Model -> startTrans();
        for($i=1;$i<=$num;$i++) {
            $lastLottery = $Model->table('__PARTICIPATION__') ->where(array('game_id' => $data['game_id']))->order('lottery_id desc')->getField('lottery_id');
            if ($lastLottery !== false) {
                if (!$lastLottery) {
                    $lastLottery = 0;
                }
            } else {
                $rudata['result'] = 'false';
                $rudata['msg'] = '抢购失败，请刷新重试！';
                $this -> ajaxReturn($rudata);
            }
            $map = array();
            $map['game_id'] = $data['game_id'];
            $map['id'] = array('gt',$lastLottery);
            $lotteryInfo = $Model ->table('__LOTTERY__') ->where($map)->order('id asc')->find();
            $data['lottery_id'] = $lotteryInfo['id'];
            $data['lottery_num'] = $lotteryInfo['num'];
            $result = $Model -> table('__PARTICIPATION__') -> data($data) ->add();
            if ($result) {
                $rudata['result'] = 'true';
                $rudata['msg'] = '抢购成功！';
            } else {
                $Model ->rollback();
                $rudata['result'] = 'false';
                $rudata['msg'] = '抢购失败，请刷新重试！';
                $this -> ajaxReturn($rudata);
            }
        }
        if($rudata['result'] == 'true'){
            $Model -> commit();
        }
        $this -> ajaxReturn($rudata);

    }
}