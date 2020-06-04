<?php
/**
* 基类
* @author lmw
* date 2018-02-02
*/
namespace Home\Controller;
use Think\Controller;
class BaseController extends Controller
{
    public function  __construct()
    {
        parent::__construct();
        if(!IS_AJAX){
            //生成菜单html
            $menu_arr = array(
                '功能菜单' =>array(
                    'list' =>array(
                        '修改个人密码'=>array('code'=>array('admin_edit'),'href'=>U("index/admin_edit")),
                        '专业信息添加'=>array('code'=>array('add_zhuanye'),'href'=>U("index/add_zhuanye")),
                        '专业信息管理'=>array('code'=>array('zhuanye_list'),'href'=>U("index/zhuanye_list")),
                        '班级信息添加'=>array('code'=>array('add_banji'),'href'=>U("index/add_banji")),
                        '班级信息管理'=>array('code'=>array('banji_list'),'href'=>U("index/banji_list")),
                        '课程信息录入'=>array('code'=>array('add_kecheng'),'href'=>U("index/add_kecheng")),
                        '课程信息管理'=>array('code'=>array('kecheng_list'),'href'=>U("index/kecheng_list")),
                        '学籍信息管理'=>array('code'=>array('xueji_list'),'href'=>U("index/xueji_list")),
                        '成绩信息管理'=>array('code'=>array('chengji_list'),'href'=>U("index/chengji_list")),
                        '奖惩信息管理'=>array('code'=>array('jiangcheng_list'),'href'=>U("index/jiangcheng_list")),
                    ),
                    'code'=>array('admin_edit','add_zhuanye','zhuanye_list','add_banji','banji_list','add_kecheng','kecheng_list','xueji_list','chengji_list','jiangcheng_list')
                ),
            );
            $act_name = strtolower(ACTION_NAME);
            $menu_html = '';
            if(!empty($menu_arr)){
                $mn = 0;
                foreach($menu_arr as $k=> $v){
                    $mn++;
                    $menu_html .= '<li>';
                    $menu_html .= '<h4 class="'.'M'.$mn.'"><span></span>'.$k.'</h4>';
                    if(in_array($act_name,$v['code'])){
                        $this->assign('position_1',$k);
                        $menu_html .= '<div class="list-item">';
                    }else{
                        $menu_html .= '<div class="list-item none">';
                    }
                    if(!empty($v['list'])){
                       foreach($v['list'] as $kk=>$vv){
                           //if($vv['code'] == $act_name){
                           if(in_array($act_name,$vv['code'])){
                               $this->assign('position_2',$kk);
                               $menu_html .= '<a href="'.$vv['href'].'" style="color:red;">'.$kk.'</a>';
                           }else{
                               $menu_html .= '<a href="'.$vv['href'].'">'.$kk.'</a>';
                           }
                       }
                    }
                    $menu_html .= '</div>';
                    $menu_html .= '</li>';
                }
            }
            $this->assign('menu_html',$menu_html);
        }
    }

    public function getPage($count, $pagesize = 3)
    {
        $p = new \Think\Page($count, $pagesize);
        $p->setConfig('header', '<li class="rows">共<b>%TOTAL_ROW%</b>条记录 第<b>%NOW_PAGE%</b>页/共<b>%TOTAL_PAGE%</b>页</li>');
        $p->setConfig('prev', '上一页');
        $p->setConfig('next', '下一页');
        $p->setConfig('last', '末页');
        $p->setConfig('first', '首页');
        $p->setConfig('theme', '%FIRST%%UP_PAGE%%LINK_PAGE%%DOWN_PAGE%%END%%HEADER%');
        $p->lastSuffix = false;//最后一页不显示为总页数
        return $p;
    }

    public function json_return($data = array() , $code = 0 ,$msg = 'success'){
         $return = array('data'=>$data,'code'=>$code,'msg'=>$msg);
         $this->showJsonResult($return);
    }
    public function showJsonResult($data){
        header( 'Content-type: application/json; charset=UTF-8' );
        if (isset( $_REQUEST['callback'] ) ) {
            echo htmlspecialchars( $_REQUEST['callback'] ) , '(' , json_encode( $data ) , ');';
        } else {
            echo json_encode( $data, JSON_UNESCAPED_UNICODE );
        }

        die();
    }
    public function tranKeyArray($arr = array(),$key=''){
        $new_arr = array();
        if(!empty($arr) && !empty($key)){
            foreach ($arr as $v){
                $new_arr[$v[$key]] = $v;
            }
        }
        return $new_arr;
    }
}