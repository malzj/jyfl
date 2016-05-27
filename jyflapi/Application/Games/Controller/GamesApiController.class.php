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
     * @author zhaoyingchao
     * @param   user_id int 用户id
     */
    public function gameList(){
        $GamesModel = M('Games');
        $CompanyModel = M('Company');
        $UserModel = M('Users');
        $GradeModel = M('Grade');
        $PartModel = M('Participation');
        $user_id = I('request.user_id');
        $int_cid = $UserModel -> where(array('user_id'=>$user_id)) -> getField('company_id');
        $company_info = $CompanyModel -> where(array('card_company_id'=>$int_cid)) -> find();
        $grade_name = $GradeModel -> where(array('id'=>$company_info['grade_id'])) -> getField('grade_name');
        $company_info['grade_name'] = $grade_name;
        $game_global = $GamesModel ->where(array('grade_id'=>1,'status'=>1)) -> order('create_time DESC') -> limit(2) -> select();
        $game_company = $GamesModel ->where(array('grade_id'=>$company_info['grade_id'],'status'=>1)) -> order('create_time DESC') -> limit(5) -> select();
        $glo_list = array();
        $com_list = array();
        foreach($game_global as $key => $val){
            $count = $PartModel -> where(array('game_id'=>$val['game_id'])) -> count();
            $percent = ($count/$val['total'])*100;
            $glo_list[$key]['percent'] = round($percent,1);
            $glo_list[$key]['total_point'] = $val['total']*$val['point'];
            $glo_list[$key]['id'] = $val['id'];
            $glo_list[$key]['grade_id'] = $val['grade_id'];
            $glo_list[$key]['game_name'] = $val['game_name'];
            $glo_list[$key]['thumbnail'] = $val['thumbnail'];
            $glo_list[$key]['rules'] = $val['rules'];
            $glo_list[$key]['buy_status'] = $val['buy_status'];
        }
        foreach($game_company as $k => $v){
            $com_count = $PartModel -> where(array('game_id'=>$v['game_id'])) -> count();
            $com_percent = ($com_count/$v['total'])*100;
            $com_list['percent'] = round($com_percent,1);
            $com_list[$k]['total_point'] = $v['total']*$v['point'];
            $com_list[$k]['id'] = $v['id'];
            $com_list[$k]['grade_id'] = $v['grade_id'];
            $com_list[$k]['game_name'] = $v['game_name'];
            $com_list[$k]['thumbnail'] = $v['thumbnail'];
            $com_list[$k]['rules'] = $v['rules'];
            $com_list[$k]['buy_status'] = $v['buy_status'];
        }

        $rudata['company_info'] = $company_info;
        $rudata['game_global'] = $glo_list;
        $rudata['game_company'] = $com_list;
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