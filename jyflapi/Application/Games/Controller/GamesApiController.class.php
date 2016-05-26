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
            $count = $PartModel -> where(array('game_id'=>$val['id'])) -> count();
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
            $com_count = $PartModel -> where(array('company_id'=>$company_info['card_company_id'],'game_id'=>$v['id'])) -> count();
            $com_percent = ($com_count/$v['total'])*100;
            $com_list[$k]['percent'] = round($com_percent,1);
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
        $user_id = I('request.user_id');
        $GamesModel = M('Games');
        $PartModel = M('Participation');
        $gameInfo = $GamesModel -> where(array('id'=>$id)) -> find();
        $partInfo = $PartModel -> where(array('game_id'=>$id,'user_id'=>$user_id)) -> select();
        if(empty($partInfo)){
           $partInfo = 'false';
        }
        if($gameInfo){
            $rudata['result'] = 'true';
            $rudata['game_info'] = $gameInfo;
            $rudata['part_info'] = $partInfo;
            $rudata['msg'] = '成功！';
        }else{
            $rudata['result'] = 'false';
            $rudata['msg'] = '获取失败，请重试！';
        }
        $this -> ajaxReturn($rudata);
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
        $card_pass =I('request.password');
        $num =I('request.number');

        $data = array();
        $data['user_id'] = I('request.user_id');
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

        //判断是否已抢完，如抢完更改buy_status=1,且根据算法得出中奖号码存入中奖表
        $count_all = $Model ->table('__PARTICIPATION__') -> where(array('game_id' => $data['game_id'])) -> count();
        if($count_all==$total){
            $winner = $this->_getWinner($total);
            $wdata['company_id'] = $data['company_id'];
            $wdata['game_id'] = $data['game_id'];
            $wdata['card_num'] = $data['card_num'];
            $wdata['lottery'] = $winner;
            $wdata['create_time'] = date('Y-m-d H:i:s',time());
            $wre = $Model ->table('__WINNERS_LIST__') -> data($wdata) -> add();
            $update_game = $Model ->table('__GAMES__')->data(array('buy_status'=>1))->save();
            if(($wre !== false)&&($update_game !== false)){
                $rudata['result'] = 'true';
                $rudata['msg'] = '抢购成功！';
            }else{
                $Model ->rollback();
                $rudata['result'] = 'false';
                $rudata['msg'] = '抢购失败，请刷新重试！';
                $this -> ajaxReturn($rudata);
            }
        }
        //链接付款接口进行付款，付款成功commit
        $is_pay = true;
        if($is_pay){
            $this -> commit();
        }else{
            $this -> rollback();
        }


        $Model -> commit();
        $this -> ajaxReturn($rudata);

    }

    public function test(){
        $Model = new Model();
        $wdata['company_id'] = 1;
        $wdata['game_id'] = 1;
        $wdata['card_num'] = 123;
        $wdata['lottery'] = 12345;
        $wdata['create_time'] = date('Y-m-d H:i:s',time());
        $wre = $Model ->table('__WINNERS_LIST__')->data($wdata)->add();
        echo '结果：'.$wre.'<br>';
        $total = 99;
        $re = $this->_getWinner($total);
        echo $re;
    }
    /**
     * 获取中奖号码
     */
    private function _getWinner($total){
        $sdLottery = 521;
        $time = date('His',time());
        $total = $total-1;
        $pro = intval($sdLottery)*intval($time);
        $tot_len = strlen($total);
        $cut = substr($pro,-$tot_len);
        $contrast = intval($cut)-intval($total);
        if($contrast>0){
            $cut = substr($cut,1-$tot_len);
            $winner = 10000000+intval($cut);

        }else{
            $winner = 10000000+intval($cut);
        }
        return $winner;
    }
}