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
        $user_id = I('requset.user_id');
        $int_cid = $UserModel -> where(array('user_id'=>$user_id)) -> getFeild('company_id');
        $company_info = $CompanyModel -> where(array('card_company_id'=>$int_cid)) -> find();
        $grade_name = $GradeModel -> where(array('id'=>$company_info['grade_id'])) -> getFeidl('grade_name');
        $company_info['grade_name'] = $grade_name;
        $game_global = $GamesModel ->where(array('grade_id'=>1,'status'=>1)) -> order('create_time DESC') -> limit(2) -> select();
        $game_company = $GamesModel ->where(array('grade_id'=>$company_info['grade_id'],'status'=>1)) -> order('create_time DESC') -> limit(5) -> select();

        $rudata['company_info'] = $company_info;
        $rudata['game_global'] = $game_global;
        $rudata['game_company'] = $game_company;
        $rudata['result'] = 'true';

        $this -> ajaxReturn($rudata);

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