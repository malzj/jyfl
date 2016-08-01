<?php
/* 
 *  为手机端专门初始一些东西
 */

// josn 数据返回，jsonp数据返回
function JsonpEncode($jsonArray)
{
    $jsonType = 'json';
    $callback = 'jsoncallback';
    if ($jsonType == 'json')
        exit(json_encode($jsonArray));
    else
        exit($_GET[$callback]."(".json_encode($jsonArray).")");
}

/** 拼接图片地址
 *  @param  string $path    图片名
 *  @param  string $ext     
 */
function getImagePath($path, $ext='goods')
{
    $imagePath = '';
    $httpHost = 'http://www.juyoufuli.com';
    $newPath = strpos($path,'/') === 0 ? substr($path, 1) : $path;
    
    switch ($ext)
    {
        case "goods":      $imagePath = strpos($path, 'http://') !== 0 ? $httpHost.'/'.$newPath : $newPath ;   break;
             
        case "ad":         $imagePath = strpos($path, 'http://') !== 0 ? $httpHost.'/data/afficheimg/'.$newPath : $newPath;   break;
    }
     
     return $imagePath;
}

/**
 * 创建分页信息
 *
 * @access  public
 * @param   string  $app            程序名称，如category
 * @param   string  $cat            分类ID
 * @param   string  $record_count   记录总数
 * @param   string  $size           每页记录数
 * @param   string  $sort           排序类型
 * @param   string  $order          排序顺序
 * @param   string  $page           当前页
 * @param   string  $keywords       查询关键字
 * @param   string  $brand          品牌
 * @param   string  $price_min      最小价格
 * @param   string  $price_max      最高价格
 * @return  void
 */
function assign_pager_wap($app, $cat, $record_count, $size, $sort, $order, $page = 1,
    $keywords = '', $brand = 0, $price_min = 0, $price_max = 0, $display_type = 'list', $filter_attr='', $url_format='', $sch_array='')
{
    $sch = array('keywords'  => $keywords,
        'sort'      => $sort,
        'order'     => $order,
        'cat'       => $cat,
        'brand'     => $brand,
        'price_min' => $price_min,
        'price_max' => $price_max,
        'filter_attr'=>$filter_attr,
        'display'   => $display_type
    );

    $page = intval($page);
    if ($page < 1)
    {
        $page = 1;
    }

    $page_count = $record_count > 0 ? intval(ceil($record_count / $size)) : 1;

    $pager['page']         = $page;
    $pager['size']         = $size;
    $pager['sort']         = $sort;
    $pager['order']        = $order;
    $pager['record_count'] = $record_count;
    $pager['page_count']   = $page_count;
    $pager['display']      = $display_type;

    switch ($app)
    {
        case 'category':
            $uri_args = array('cid' => $cat, 'bid' => $brand, 'price_min'=>$price_min, 'price_max'=>$price_max, 'filter_attr'=>$filter_attr, 'sort' => $sort, 'order' => $order, 'display' => $display_type);
            break;
        case 'article_cat':
            $uri_args = array('acid' => $cat, 'sort' => $sort, 'order' => $order);
            break;
        case 'brand':
            $uri_args = array('cid' => $cat, 'bid' => $brand, 'sort' => $sort, 'order' => $order, 'display' => $display_type);
            break;
        case 'search':
            $uri_args = array('cid' => $cat, 'bid' => $brand, 'sort' => $sort, 'order' => $order);
            break;
        case 'exchange':
            $uri_args = array('cid' => $cat, 'integral_min'=>$price_min, 'integral_max'=>$price_max, 'sort' => $sort, 'order' => $order, 'display' => $display_type);
            break;
    }
    /* 分页样式 */
    $pager['styleid'] = isset($GLOBALS['_CFG']['page_style'])? intval($GLOBALS['_CFG']['page_style']) : 0;

    $page_prev  = ($page > 1) ? $page - 1 : 1;
    $page_next  = ($page < $page_count) ? $page + 1 : $page_count;
    if ($pager['styleid'] == 0)
    {
        if (!empty($url_format))
        {
            $pager['page_first'] = $url_format . 1;
            $pager['page_prev']  = $url_format . $page_prev;
            $pager['page_next']  = $url_format . $page_next;
            $pager['page_last']  = $url_format . $page_count;
        }
        else
        {
            $pager['page_first'] = build_uri($app, $uri_args, '', 1, $keywords);
            $pager['page_prev']  = build_uri($app, $uri_args, '', $page_prev, $keywords);
            $pager['page_next']  = build_uri($app, $uri_args, '', $page_next, $keywords);
            $pager['page_last']  = build_uri($app, $uri_args, '', $page_count, $keywords);
        }
        $pager['array']      = array();

        for ($i = 1; $i <= $page_count; $i++)
        {
            $pager['array'][$i] = $i;
        }
    }
    else
    {
        $_pagenum = 10;     // 显示的页码
        $_offset = 2;       // 当前页偏移值
        $_from = $_to = 0;  // 开始页, 结束页
        if($_pagenum > $page_count)
        {
            $_from = 1;
            $_to = $page_count;
        }
        else
        {
            $_from = $page - $_offset;
            $_to = $_from + $_pagenum - 1;
            if($_from < 1)
            {
                $_to = $page + 1 - $_from;
                $_from = 1;
                if($_to - $_from < $_pagenum)
                {
                    $_to = $_pagenum;
                }
            }
            elseif($_to > $page_count)
            {
                $_from = $page_count - $_pagenum + 1;
                $_to = $page_count;
            }
        }
        if (!empty($url_format))
        {
            $pager['page_first'] = ($page - $_offset > 1 && $_pagenum < $page_count) ? $url_format . 1 : '';
            $pager['page_prev']  = ($page > 1) ? $url_format . $page_prev : '';
            $pager['page_next']  = ($page < $page_count) ? $url_format . $page_next : '';
            $pager['page_last']  = ($_to < $page_count) ? $url_format . $page_count : '';
            $pager['page_kbd']  = ($_pagenum < $page_count) ? true : false;
            $pager['page_number'] = array();
            for ($i=$_from;$i<=$_to;++$i)
            {
                $pager['page_number'][$i] = $url_format . $i;
            }
        }
        else
        {
            $pager['page_first'] = ($page - $_offset > 1 && $_pagenum < $page_count) ? build_uri($app, $uri_args, '', 1, $keywords) : '';
            $pager['page_prev']  = ($page > 1) ? build_uri($app, $uri_args, '', $page_prev, $keywords) : '';
            $pager['page_next']  = ($page < $page_count) ? build_uri($app, $uri_args, '', $page_next, $keywords) : '';
            $pager['page_last']  = ($_to < $page_count) ? build_uri($app, $uri_args, '', $page_count, $keywords) : '';
            $pager['page_kbd']  = ($_pagenum < $page_count) ? true : false;
            $pager['page_number'] = array();
            for ($i=$_from;$i<=$_to;++$i)
            {
                $pager['page_number'][$i] = build_uri($app, $uri_args, '', $i, $keywords);
            }
        }
    }
    if (!empty($sch_array))
    {
        $pager['search'] = $sch_array;
    }
    else
    {
        $pager['search']['category'] = $cat;
        foreach ($sch AS $key => $row)
        {
            $pager['search'][$key] = $row;
        }
    }
    return $pager;
    //$GLOBALS['smarty']->assign('pager', $pager);
}

/* 手机端推荐推荐的商品 */
function category_get_goods_wap($children, $sort, $limit=4)
{

    $display = $GLOBALS['display'];
    $where = "g.is_on_sale = 1 AND g.is_alone_sale = 1 AND g.supplier_status = 1 AND ".
        " g.is_delete = 0 AND ($children) ".
        " AND g.is_hot = 1 ";
     
    $where .= ' AND FIND_IN_SET('.intval($GLOBALS['int_cityId']).', g.region_ids) AND gs.default_show = 1 ';

    if (!empty($_SESSION['card_id']))
    {
        $where .= ' AND LOCATE(",'.$_SESSION['card_id'].',",g.rule_ids) = 0';
    }
     
    $sql = 'SELECT g.goods_id, g.goods_name,g.region_ids,g.sort_order, g.rule_ids, g.goods_name_style, g.market_price, g.is_new, g.is_best, g.is_hot,g.goods_num, g.shop_price, ' .
        "g.promote_price, g.goods_type, g.promote_start_date, g.promote_end_date, g.goods_brief, g.goods_thumb , g.goods_img, " .
        'gs.* ' .
        'FROM ' . $GLOBALS['ecs']->table('goods') . ' AS g ' .
        'LEFT JOIN ' . $GLOBALS['ecs']->table('goods_spec') . ' AS gs ' .
        "ON gs.goods_id = g.goods_id " .
        "WHERE $where GROUP BY g.goods_name ORDER BY ".$sort." LIMIT $limit";

    $res = $GLOBALS['db']->getAll($sql);

    $arr = array();
    foreach ($res as $key=>$row)
    {
        if ($row['promote_price'] > 0)
        {
            $promote_price = bargain_price($row['promote_price'], $row['promote_start_date'], $row['promote_end_date']);
        }
        else
        {
            $promote_price = 0;
        }

        /* 处理商品水印图片 */
        $watermark_img = '';

        if ($promote_price != 0)
        {
            $watermark_img = "watermark_promote_small";
        }
        elseif ($row['is_new'] != 0)
        {
            $watermark_img = "watermark_new_small";
        }
        elseif ($row['is_best'] != 0)
        {
            $watermark_img = "watermark_best_small";
        }
        elseif ($row['is_hot'] != 0)
        {
            $watermark_img = 'watermark_hot_small';
        }

        if ($watermark_img != '')
        {
            $arr[$row['goods_id']]['watermark_img'] =  $watermark_img;
        }

        $arr[$row['goods_id']]['goods_id']         = $row['goods_id'];
        if($display == 'grid')
        {
            $arr[$row['goods_id']]['goods_name']       = $GLOBALS['_CFG']['goods_name_length'] > 0 ? sub_str($row['goods_name'], $GLOBALS['_CFG']['goods_name_length']) : $row['goods_name'];
        }
        else
        {
            $arr[$row['goods_id']]['goods_name']       = $row['goods_name'];
        }
        $arr[$row['goods_id']]['name']             = $row['goods_name'];
        $arr[$row['goods_id']]['goods_brief']      = $row['goods_brief'];
        $arr[$row['goods_id']]['goods_style_name'] = add_style($row['goods_name'],$row['goods_name_style']);
        $arr[$row['goods_id']]['market_price']     = price_format($row['market_price']);
        // 商品规格价格处理
        // TODO 当前卡规则不显示这个规格的情况下，去最低的规格价格、（未做）
        $spec_array = array('spec_nember'=> $row['spec_nember'], 'goods_id'=>$row['goods_id']);
        $arr[$row['goods_id']]['shop_price']       = get_spec_ratio_price($spec_array);

        $arr[$row['goods_id']]['spec_name']       = $row['spec_name'];
        $arr[$row['goods_id']]['spec_nember']       = $row['spec_nember'];

        $arr[$row['goods_id']]['type']             = $row['goods_type'];
        $arr[$row['goods_id']]['goods_num']        = $row['goods_num'];
        $arr[$row['goods_id']]['promote_price']    = ($promote_price > 0) ? price_format($promote_price) : '';
        $arr[$row['goods_id']]['goods_thumb']      = get_image_path($row['goods_id'], $row['goods_thumb'], true);
        $arr[$row['goods_id']]['goods_img']        = get_image_path($row['goods_id'], $row['goods_img']);
        $arr[$row['goods_id']]['url']              = build_uri('goods', array('gid'=>$row['goods_id']), $row['goods_name']);
        $arr[$row['goods_id']]['sort_order']       = $row['sort_order'];
    }
    return $arr;
}

/**
 * 返回加密卡号
 *
 * @param $num              要加密数字(卡号)
 * @return string|void
 */
function getCode($num){
    include_once(ROOT_PATH . 'includes/httpRequest.php');
    $url='http://a.piaowutong.cc:9001/encryptvoucher/encode';
    $HttpRequest = new HttpRequest();
    $code = $HttpRequest->get($url,array('voucherno'=>$num));
    return $code;
}
