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
                '数据中心' =>array(
                    'list' =>array(
                        '项目表'=>array('code'=>array('project_list'),'href'=>U("index/project_list")),
                        '数据表'=>array('code'=>array('table_list'),'href'=>U("index/table_list")),
                        '菜单表'=>array('code'=>array('menu_list'),'href'=>U("index/menu_list")),
                        '从车查询'=>array('code'=>array('search_car'),'href'=>U("index/search_car")),
                        '从人查询'=>array('code'=>array('search_people'),'href'=>U("index/search_people")),
                        '从修理机构查询'=>array('code'=>array('search_company'),'href'=>U("index/search_company")),
                        '从手机号查询'=>array('code'=>array('search_phone'),'href'=>U("index/search_phone")),
                        '录入保险案件'=>array('code'=>array('add_case'),'href'=>U("index/add_case")),
                    ),
                    'code'=>array('menu_list','project_list','add_project','table_list','add_table','add_case','del_case','search_car','search_people','search_company','search_phone')
                ),
                '菜单设置' =>array(
                    'list' =>array(
                        '刑事案件'=>array('code'=>array('criminal_case','add_criminal_case'),'href'=>U("index/criminal_case")),
                        '风险车辆'=>array('code'=>array('risk_car','add_risk_car'),'href'=>U("index/risk_car")),
                        '风险人员'=>array('code'=>array('risk_people','add_risk_people'),'href'=>U("index/risk_people")),
                        '风险机构'=>array('code'=>array('risk_company','add_risk_company'),'href'=>U("index/risk_company")),
                        '风险手机号'=>array('code'=>array('risk_phone','add_risk_phone'),'href'=>U("index/risk_phone")),
                    ),
                    'code'=>array('add_risk_phone','add_risk_company','add_risk_people','add_risk_car','add_criminal_case','criminal_case','risk_car','risk_people','risk_company','risk_phone')
                ),
                '权限设置' =>array(
                    'list' =>array(
                        '通讯录'=>array('code'=>array('risk_book','add_risk_book'),'href'=>U("index/risk_book")),
                    ),
                    'code'=>array('risk_book','add_risk_book')
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