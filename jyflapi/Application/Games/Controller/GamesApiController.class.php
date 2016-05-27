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
        $num = intval($num);

        $data = array();
        $data['user_id'] = I('request.user_id');
        $data['game_id'] = I('request.game_id');
        $data['card_num'] =I('request.card_num');
        $data['company_id'] =I('request.company_id');
        $data['buy_time'] = date('Y-m-d H:i:s',time());
        $count = $Model ->table('__PARTICIPATION__') -> where(array('game_id' => $data['game_id'])) -> count();
        $game_info = $Model ->table('__GAMES__') -> where(array('id' => $data['game_id'])) ->find();
        $total = $game_info['total'];
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
                $this->ajaxReturn($data);

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
            $sdInfo = $this ->_get3DLottery();
            $winner_num = $this->_getWinner($total,$sdInfo['opencode']);
            $winnerInfo = $Model -> table('__PARTICIPATION__') -> where(array('game_id' => $data['game_id'],'lottery_num'=>$winner_num)) -> find();
            $wdata['company_id'] = $data['company_id'];
            $wdata['game_id'] = $data['game_id'];
            $wdata['card_num'] = $winnerInfo['card_num'];
            $wdata['lottery'] = $winner_num;
            $wdata['create_time'] = date('Y-m-d H:i:s',time());
            $wre = $Model ->table('__WINNERS_LIST__') -> data($wdata) -> add();
            $update_game = $Model ->table('__GAMES__')->where(array('id' => $data['game_id']))->data(array('buy_status'=>1))->save();
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
        $Card = new \Ext\card\huayingcard();
        $card_data['CardInfo']['CardNo'] = $data['card_num'];
        $card_data['CardInfo']['CardPwd'] = $card_pass;
        $card_data['TransationInfo']['TransRequestPoints'] = $num*$game_info['point'];

        $is_pay = $Card -> action($card_data,1);
        $Model -> table('__USERS__')->where('user_name')->setDec('card_money',$num*$game_info['point']);
        if($is_pay == 0){
            $Model -> commit();
            $rudata['result'] = 'true';
            $rudata['msg'] = '抢购成功！';
            $this -> ajaxReturn($rudata);
        }else{
            $Model -> rollback();
            $rudata['result'] = 'false';
            $rudata['msg'] = $Card->getMessage();
            $this -> ajaxReturn($rudata);
        }
    }

    /**
     * 获取商品剩余量
     */
    public function getSurplus(){
        $game_id = I('request.game_id');
        $Model = new Model();
        $count = $Model ->table('__PARTICIPATION__') -> where(array('game_id' => $game_id)) -> count();
        $game_info = $Model ->table('__GAMES__') -> where(array('id' => $game_id)) ->find();
        $total = $game_info['total'];
        $surplus = intval($total) - intval($count);
        $this ->ajaxReturn($surplus);
    }
    /**
     * 获取游戏中奖信息
     * @author  zhaoyingchao
     *
     * @param   game_id int 如有值则获取相应的获奖信息，如果无值则获取所有游戏中奖信息
     */
    public function getWinners(){
        $game_id = I('request.game_id');
        $PartModel = M('Participation');
        $WinnerModel = M('WinnersList');
        $UserModel = M('Users');
        $GamesModel = M('Games');
        if($game_id){
            $winnerList = $WinnerModel -> where(array('game_id'=>$game_id)) -> select();
        }else{
            $winnerList = $WinnerModel ->where('company_id = 1') -> select();
        }
        $list = array();
        foreach($winnerList as $key => $val){
            $gameInfo = $GamesModel->where(array('id'=>$val['game_id']))->find();
            $peo_num = $PartModel ->where(array('game_id'=>$val['game_id'])) -> group('user_id') -> select();
            $userInfo = $UserModel -> where(array('user_name'=>$val['card_num'])) -> find();
            $peo_count = count($peo_num);
            $list[$key]['issue'] = date('Ymd',strtotime($val['create_time']));//期号
            $list[$key]['thumbnail'] = $gameInfo['thumbnail'];
            $list[$key]['user_name'] = $userInfo['nickname'];
            $list[$key]['card_num'] = $val['card_num'];
            $list[$key]['peo_count'] = $peo_count;
        }
        $this->ajaxReturn($list);
    }
    /**
     * 获取中奖号码及中奖人
     * @author zhaoyingchao
     *
     * @param   int $total      商品总数
     * @param   int $opencode   3D福彩开奖号
     */
    private function _getWinner($total,$opencode){
        $time = date('His',time());
        $total = $total-1;
        $pro = intval($opencode)*intval($time);
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

    /**
     * 获取3D福彩信息
     */
    private function _get3DLottery(){
        $url = 'http://f.apiplus.cn/fc3d-1.json';
        $httpRequest = new \Ext\card\HttpRequest;
        $jsoncode = $httpRequest->get($url);
        $openInfo = json_decode($jsoncode);
        $expect = $openInfo->data[0]->expect;
        $code = $openInfo->data[0]->opencode;
        $opencode = str_replace(',','',$code);
        $data['expect'] = $expect;
        $data['opencode'] = intval($opencode);
        return $data;
    }
}