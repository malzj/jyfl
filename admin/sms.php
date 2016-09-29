<?php

/**
 * ECSHOP 短信模块 之 控制器
 * ============================================================================
 * 版权所有 2005-2010 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: yehuaixiao $
 * $Id: sms.php 17155 2010-05-06 06:29:05Z yehuaixiao $
 */

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
require_once(ROOT_PATH . 'includes/cls_sms.php');

$action = isset($_REQUEST['act']) ? $_REQUEST['act'] : 'sms_send_history';
$sms = new sms();

switch ($action)
{
    /* 显示短信发送界面，如果尚未注册或启用短信服务则显示注册界面。 */
    case 'display_send_ui' :
        /* 检查权限 */
         admin_priv('sms_send');

        if ($sms->has_registered())
        {
            $smarty->assign('ur_here', $_LANG['03_sms_send']);

            $special_ranks = get_rank_list();
            $send_rank['1_0'] = $_LANG['user_list'];
            foreach($special_ranks as $rank_key => $rank_value)
            {
                $send_rank['2_' . $rank_key] = $rank_value;
            }
            assign_query_info();
            $smarty->assign('send_rank',   $send_rank);
            $smarty->display('sms_send_ui.htm');
        }
        else
        {

            $smarty->assign('ur_here', $_LANG['register_sms']);
            $smarty->assign('sms_site_info', $sms->get_site_info());
            assign_query_info();
            $smarty->display('sms_register_ui.htm');
        }

        break;
    /* 发送短信 */
    case 'send_sms' :
        admin_priv('sms_send');
        $send_num = isset($_POST['send_num'])   ? $_POST['send_num']    : '';

        if(isset($send_num))
        {
            $phone = $send_num.',';
        }

        $send_rank = isset($_POST['send_rank'])     ? $_POST['send_rank'] : 0;

        if ($send_rank != 0)
        {
            $rank_array = explode('_', $send_rank);

            if($rank_array['0'] == 1)
            {
                $sql = 'SELECT mobile_phone FROM ' . $ecs->table('users') . "WHERE mobile_phone <>'' ";
                $row = $db->query($sql);
                while ($rank_rs = $db->fetch_array($row))
                {
                    $value[] = $rank_rs['mobile_phone'];
                }
            }
            else
            {
                $rank_sql = "SELECT * FROM " . $ecs->table('user_rank') . " WHERE rank_id = '" . $rank_array['1'] . "'";
                $rank_row = $db->getRow($rank_sql);
                //$sql = 'SELECT mobile_phone FROM ' . $ecs->table('users') . "WHERE mobile_phone <>'' AND rank_points > " .$rank_row['min_points']." AND rank_points < ".$rank_row['max_points']." ";

                if($rank_row['special_rank']==1) 
                {
                    $sql = 'SELECT mobile_phone FROM ' . $ecs->table('users') . " WHERE mobile_phone <>'' AND user_rank = '" . $rank_array['1'] . "'";
                }
                else
                {
                    $sql = 'SELECT mobile_phone FROM ' . $ecs->table('users') . "WHERE mobile_phone <>'' AND rank_points > " .$rank_row['min_points']." AND rank_points < ".$rank_row['max_points']." ";
                }
                
                $row = $db->query($sql);
                
                while ($rank_rs = $db->fetch_array($row))
                {
                    $value[] = $rank_rs['mobile_phone'];
                }
            }
            if(isset($value))
            {
                $phone .= implode(',',$value);
            }
        }       
      
        $msg       = isset($_POST['msg'])       ? $_POST['msg']         : '';


        $send_date = isset($_POST['send_date']) ? $_POST['send_date']   : '';   

        $result = $sms->send($phone, $msg, $send_date, $send_num = 13);

        $link[] = array('text'  =>  $_LANG['back'] . $_LANG['03_sms_send'],
                        'href'  =>  'sms.php?act=display_send_ui');

        if ($result === true)//发送成功
        {

            sys_msg($_LANG['send_ok'], 0, $link);
        }
        else
        {
            @$error_detail = $_LANG['server_errors'][$sms->errors['server_errors']['error_no']]
                          . $_LANG['api_errors']['send'][$sms->errors['api_errors']['error_no']];
            sys_msg($_LANG['send_error'] . $error_detail, 1, $link);
        }

        break;
   /* 发送记录 */
   case 'sms_send_history' :
       /* 检查权限 */
       admin_priv('sms_send_history');
       $smarty->assign('ur_here', $_LANG['05_sms_send_history']);
       $result = $sms->get_send_history();

       $smarty->assign('sms_send_history', $result['item']);
       $smarty->assign('ur_here', $_LANG['05_sms_send_history']);

       /* 分页信息 */
       $turn_page = array( 'total_records' => $result['record_count'],
                           'total_pages'   => intval(ceil($result['record_count']/$result['filter']['page_size'])),
                           'page'          => $result['filter']['page'],
                           'page_size'     => $result['filter']['page_size']);
       $smarty->assign('turn_page', $turn_page);
       $smarty->assign('start_date', $result['filter']['start_date']);
       $smarty->assign('end_date', $result['filter']['end_date']);

       assign_query_info();

       $smarty->display('sms_send_history.htm');

       
       break;
     /* 设置短信模板 */
   case 'get_send_history' :
       /* 检查权限 */
      admin_priv('sms_send');

      $smarty->assign('ur_here', $_LANG['05_sms_send_history']);
       $result = $sms->get_send_history();

       $smarty->assign('sms_send_history', $result['item']);
       $smarty->assign('ur_here', $_LANG['05_sms_send_history']);

       /* 分页信息 */
       $turn_page = array( 'total_records' => $result['record_count'],
                           'total_pages'   => intval(ceil($result['record_count']/$result['filter']['page_size'])),
                           'page'          => $result['filter']['page'],
                           'page_size'     => $result['filter']['page_size']);
       $smarty->assign('turn_page', $turn_page);
       $smarty->assign('start_date', $result['filter']['start_date']);
       $smarty->assign('end_date', $result['filter']['end_date']);      
      assign_query_info();
      $smarty->display('sms_send_history.htm');
       
      break;     
   /* 设置短信模板 */
   case 'send_content' :
       /* 检查权限 */
      admin_priv('sms_send');
      $sql = 'SELECT * FROM ' . $ecs->table('sms_content');
      $row = $db->query($sql);
      while ($rank_rs = $db->fetch_array($row))
      {
          $list[] = $rank_rs;
      }

      $smarty->assign('cmsContent', $list);
      $smarty->assign('act', 'send_content');
      // $phone='15835146534';
      // $msg=$sms->sms_content($arr,1);
      // $result = $sms->send($phone, $msg, $send_date, $send_num = 13);
      //规格化短信内容
// echo "<pre>";
// print_r($msg);
// print_r($arr);
// print_r($result);
// // print_r($content1);
// echo "</pre>";
// die;      
      $smarty->assign('ur_here', $_LANG['07_sms_content']);
      assign_query_info();
      $smarty->display('sms_content.htm');
      break;
   /* 编辑短信模板 */
   case 'editValue' :
       /* 检查权限 */
      admin_priv('sms_send');
      $id=$_REQUEST['id'];
      if(empty($id)){
        return false;
      }
      $sql = 'SELECT * FROM ' . $ecs->table('sms_content')." where id='$id'";
      $row = $db->getRow($sql);
      $smarty->assign('cmsContent', $row);

      $smarty->assign('act', 'editValue');
      $smarty->assign('ur_here', $_LANG['07_sms_content']);
      assign_query_info();
      $smarty->display('sms_content.htm');
      break;
     /* 编辑短信模板动作 */
  case 'doEditValue' :
       /* 检查权限 */
      admin_priv('sms_send');
      $id=$_REQUEST['id'];
      if(empty($id)){
        return false;
      }
      $value=$_REQUEST['value']; 
      $sql = 'update ' . $ecs->table('sms_content')." set value='$value'"." where id='$id'";
      $row = $db->query($sql);
      if($row){
        sys_msg('编辑成功');
      }
      break;
     /* 添加短信模板 */
   case 'add_content' :
       /* 检查权限 */
      admin_priv('sms_send');
      $smarty->assign('act', 'add_content');
      $smarty->assign('ur_here', $_LANG['07_sms_content']);
      assign_query_info();
      $smarty->display('sms_content.htm');
      break;
     /* 添加短信模板动作 */
   case 'doAddContent' :
       /* 检查权限 */
      admin_priv('sms_send');
      $value=$_REQUEST['value'];
      if(empty($value)){
        sys_msg('短信内容不能为空');
      }else{
        $sql='insert into '.$ecs->table('sms_content')." set value='$value'";
        $row = $db->query($sql);
        if($row){
          sys_msg('添加成功');
        }        
      } 

      break;      
     /* 删除短信模板 */
   case 'del_content' :
       /* 检查权限 */
      admin_priv('sms_send');
      $id=$_REQUEST['id'];
      if(empty($id)){
        return false;
      }
      $sql = 'delete FROM ' . $ecs->table('sms_content')." where id='$id'";
      $row = $db->query($sql);
      if($row){
        sys_msg('删除成功');
      }
      break;         
   /* 显示我的短信服务个人信息 */
   default :
       /* 检查权限 */
        admin_priv('my_info');
       /* 再次获取个人数据，保证显示的数据是最新的 ，获取余额*/
       $sms_my_info = $sms->getSms();//这里不再进行判空处理，主要是因为如果前个式子不出错，这里一般不会出错
       /* 格式化时间输出 */
       $sms_last_request = $sms_my_info['sms_last_request']? $sms_my_info['sms_last_request']: 0;//赋0防出错
       $sms_my_info['sms_last_request'] = local_date('Y-m-d H:i:s', $sms_my_info['sms_last_request']);
       $smarty->assign('sms_my_info', $sms_my_info);
       $smarty->assign('ur_here', $_LANG['02_sms_my_info']);
       assign_query_info();
       $smarty->display('sms_my_info.htm');
      
}

?>