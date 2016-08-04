<?php
/**
 * Created by PhpStorm.
 * User: chao
 * Date: 2016/8/3
 * Time: 18:55
 */

/**
 * 设置卡规则
 * @param string $ctype
 * @param array $catlist
 */
function set_card_rules($ctype = '', $catlist = array()){
    $arr_nav =  get_navigator($ctype, $catlist);
    $_SESSION['card_id'] = $arr_nav['home']['card_id']; // TODO 当前用户的卡规则id
    // todo guoyunpeng 登录的时候设置卡规则id
    set_card_id($_SESSION['user_name'], $_SESSION['card_id']);
}