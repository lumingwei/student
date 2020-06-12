<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends BaseController {
    //新增专业
    public function admin_edit(){
        $id         = $_SESSION['stu_id'];
        $table      = M('xueji');
        if(!empty($id)){
            $info = $table->where(array('stu_id'=>$id))->find();
        }
        if(IS_AJAX){
            if(empty($_REQUEST['name'])){
                $this->error('请填写登录名！');
            }
            if(empty($_REQUEST['pwd'])){
                $this->error('请填写密码！');
            }
            if($_REQUEST['old_pwd'] != $info['pwd']){
                $this->error('原密码错误！');
            }
            $data['name']   =  !empty($_REQUEST['name'])?trim($_REQUEST['name']):'';
            $data['pwd']   =  !empty($_REQUEST['pwd'])?trim($_REQUEST['pwd']):'';
            $ret        = $table->where(array('id'=>$id))->save($data);
            if($ret){
                $this->success('操作成功', U('index/admin_edit'));
            }else{
                $this->error('操作失败');
            }
        }else{
            $this->assign('info',!empty($info)?$info:array());
            $this->display(); // 输出模板
        }
    }

    //班级管理
    public function banji_list(){
        $postArr['name']       = I('name','','trim');
        $postArr['zhuanye_id'] = I('zhuanye_id',0,'int');
        $where = array();
        if(!empty($postArr['name'])){
            $where['name']  = array('like', "%{$postArr['name']}%");
        }
        if(!empty($postArr['zhuanye_id'])){
            $where['zhuanye_id']  = $postArr['zhuanye_id'];
        }
        $company    = M('banji'); // 实例化User对象
        $count      = $company->where($where)->count();// 查询满足要求的总记录数
        $Page       = $this->getPage($count,20);// 实例化分页类 传入总记录数和每页显示的记录数
        //分页跳转的时候保证查询条件
        foreach($postArr as $key=>$val) {
            $Page->parameter[$key]   =   urlencode($val);
        }
        $show       = $Page->show();// 分页显示输出
// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $list = $company->where($where)->limit($Page->firstRow.','.$Page->listRows)->select();
        $zhuanye_list      = M('zhuanye')->select();
        $zhuanye_list      = $this->tranKeyArray($zhuanye_list,'id');
        if(!empty($list)){
            foreach ($list as $k=>$v){
                $list[$k]['zhuanye_name'] = !empty($zhuanye_list[$v['zhuanye_id']]['name'])?$zhuanye_list[$v['zhuanye_id']]['name']:'';
            }
        }
        $zhuanye_html    = $this->getZhuanyeHtml($postArr['zhuanye_id']);
        $this->assign('zhuanye_html',$zhuanye_html);// 搜索参数
        $this->assign('postArr',$postArr);// 搜索参数
        $this->assign('list',$list);// 赋值数据集
        $this->assign('page',$show);// 赋值分页输出
        $this->display(); // 输出模板
    }

    //新增班级
    public function add_banji(){
        $id         = I('id',0,'intval');
        $table      = M('banji');
        if(!empty($id)){
            $info = $table->where(array('id'=>$id))->find();
        }
        if(IS_AJAX){
            if(empty($_REQUEST['name'])){
                $this->error('请填写班级名称！');
            }
            if(empty($_REQUEST['zhuanye_id'])){
                $this->error('请选择专业！');
            }
            $data['name']   =  !empty($_REQUEST['name'])?trim($_REQUEST['name']):'';
            $data['zhuanye_id']   =  !empty($_REQUEST['zhuanye_id'])?intval($_REQUEST['zhuanye_id']):0;
            if($id){
                $ret        = $table->where(array('id'=>$id))->save($data);
            }else{
                $ret        = $table->add($data);
            }
            if($ret){
                $this->success('操作成功', U('index/banji_list'));
            }else{
                $this->error('操作失败');
            }
        }else{
            $zhuanye_html    = $this->getZhuanyeHtml($info['zhuanye_id']);
            $this->assign('zhuanye_html',$zhuanye_html);// 搜索参数
            $this->assign('info',!empty($info)?$info:array());
            $this->display(); // 输出模板
        }
    }

    //删除班级
    public function del_banji(){
        $id   = I('id',0,'intval');
        if(empty($id)){
            $this->error('非法参数', U('index/banji_list'));
        }
        $company    = M('banji');
        $ret        = $company->where(array('id'=>$id))->delete();
        if($ret){
            $this->success('操作成功', U('index/banji_list'));
        }else{
            $this->error('操作失败', U('index/banji_list'));
        }
    }

    //课程管理
    public function kecheng_list(){
        $postArr['name']       = I('name','','trim');
        $where = array();
        if(!empty($postArr['name'])){
            $where['name']  = array('like', "%{$postArr['name']}%");
        }
        $company    = M('kecheng'); // 实例化User对象
        $count      = $company->where($where)->count();// 查询满足要求的总记录数
        $Page       = $this->getPage($count,20);// 实例化分页类 传入总记录数和每页显示的记录数
        //分页跳转的时候保证查询条件
        foreach($postArr as $key=>$val) {
            $Page->parameter[$key]   =   urlencode($val);
        }
        $show       = $Page->show();// 分页显示输出
// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $list = $company->where($where)->limit($Page->firstRow.','.$Page->listRows)->select();
        $this->assign('postArr',$postArr);// 搜索参数
        $this->assign('list',$list);// 赋值数据集
        $this->assign('page',$show);// 赋值分页输出
        $this->display(); // 输出模板
    }

    //新增课程
    public function add_kecheng(){
        $id         = I('id',0,'intval');
        $table      = M('kecheng');
        if(!empty($id)){
            $info = $table->where(array('id'=>$id))->find();
        }
        if(IS_AJAX){
            if(empty($_REQUEST['name'])){
                $this->error('请填写班级名称！');
            }
            $data['name']   =  !empty($_REQUEST['name'])?trim($_REQUEST['name']):'';
            $data['info']   =  !empty($_REQUEST['info'])?trim($_REQUEST['info']):'';
            if($id){
                $ret        = $table->where(array('id'=>$id))->save($data);
            }else{
                $ret        = $table->add($data);
            }
            if($ret){
                $this->success('操作成功', U('index/kecheng_list'));
            }else{
                $this->error('操作失败');
            }
        }else{
            $this->assign('info',!empty($info)?$info:array());
            $this->display(); // 输出模板
        }
    }

    //删除课程
    public function del_kecheng(){
        $id   = I('id',0,'intval');
        if(empty($id)){
            $this->error('非法参数', U('index/kecheng_list'));
        }
        $company    = M('kecheng');
        $ret        = $company->where(array('id'=>$id))->delete();
        if($ret){
            $this->success('操作成功', U('index/kecheng_list'));
        }else{
            $this->error('操作失败', U('index/kecheng_list'));
        }
    }

    private function getKechengHtml($select_id = ''){
        $select_id = !empty($select_id)?trim($select_id):'';
        $list   = M('kecheng')->select();
        $html   = '<select  name="kecheng_id">';
        $html  .= '<option value="">请选择</option>';
        if(!empty($list)){
            foreach ($list as $v){
                if($v['id'] == $select_id){
                    $html .= '<option value="'.$v['id'].'" selected>'.$v['name'].'</option>';
                }else{
                    $html .= '<option value="'.$v['id'].'">'.$v['name'].'</option>';
                }
            }
        }
        $html .= '</select>';
        return $html;
    }

    private function getStuHtml($select_id = ''){
        $select_id = !empty($select_id)?trim($select_id):'';
        $list   = M('xueji')->select();
        $html   = '<select  name="stu_id">';
        $html  .= '<option value="">请选择</option>';
        if(!empty($list)){
            foreach ($list as $v){
                if($v['id'] == $select_id){
                    $html .= '<option value="'.$v['stu_id'].'" selected>'.$v['name'].'</option>';
                }else{
                    $html .= '<option value="'.$v['stu_id'].'">'.$v['name'].'</option>';
                }
            }
        }
        $html .= '</select>';
        return $html;
    }


    //成绩管理
    public function chengji_list(){
        $postArr['stu_id']       = I('stu_id',0,'int');
        $postArr['kecheng_id'] = I('kecheng_id',0,'int');
        $where = array();
        if(!empty($postArr['stu_id'])){
            $where['stu_id']  = $postArr['stu_id'];
        }
        if(!empty($postArr['kecheng_id'])){
            $where['kecheng_id']  = $postArr['kecheng_id'];
        }
        if(empty($_SESSION['is_admin'])){
            $where['stu_id'] = $_SESSION['stu_id'];
        }
        $company    = M('chengji'); // 实例化User对象
        $count      = $company->where($where)->count();// 查询满足要求的总记录数
        $Page       = $this->getPage($count,20);// 实例化分页类 传入总记录数和每页显示的记录数
        //分页跳转的时候保证查询条件
        foreach($postArr as $key=>$val) {
            $Page->parameter[$key]   =   urlencode($val);
        }
        $show       = $Page->show();// 分页显示输出
// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $list = $company->where($where)->limit($Page->firstRow.','.$Page->listRows)->select();
        $kecheng_list      = M('kecheng')->select();
        $kecheng_list      = $this->tranKeyArray($kecheng_list,'id');
        $stu_list          = M('xueji')->select();
        $stu_list          = $this->tranKeyArray($stu_list,'stu_id');
        if(!empty($list)){
            foreach ($list as $k=>$v){
                $list[$k]['stu_name'] = !empty($stu_list[$v['stu_id']]['name'])?$stu_list[$v['stu_id']]['name']:'';
                $list[$k]['kecheng_name'] = !empty($kecheng_list[$v['kecheng_id']]['name'])?$kecheng_list[$v['kecheng_id']]['name']:'';
            }
        }
        $stu_html    = $this->getStuHtml($postArr['stu_id']);
        $this->assign('stu_html',$stu_html);// 搜索参数
        $kecheng_html    = $this->getKechengHtml($postArr['kecheng_id']);
        $this->assign('kecheng_html',$kecheng_html);// 搜索参数
        $this->assign('postArr',$postArr);// 搜索参数
        $this->assign('list',$list);// 赋值数据集
        $this->assign('page',$show);// 赋值分页输出
        if($_SESSION['is_admin']){
            $this->display(); // 输出模板
        }else{
            $this->display('index/chengji_list1'); // 输出模板
        }

    }

    //新增成绩
    public function add_chengji(){
        $id         = I('id',0,'intval');
        $table      = M('chengji');
        if(!empty($id)){
            $info = $table->where(array('id'=>$id))->find();
        }
        if(IS_AJAX){
            if(empty($_REQUEST['stu_id'])){
                $this->error('请选择学生！');
            }
            if(empty($_REQUEST['kecheng_id'])){
                $this->error('请选择课程！');
            }
            if(empty($_REQUEST['chengji'])){
                $this->error('请填写成绩！');
            }
            if(empty($_REQUEST['xuenian'])){
                $this->error('请填写学年！');
            }

            $data['stu_id']       =  !empty($_REQUEST['stu_id'])?intval($_REQUEST['stu_id']):'';
            $data['kecheng_id']   =  !empty($_REQUEST['kecheng_id'])?intval($_REQUEST['kecheng_id']):0;
            $data['chengji']      =  !empty($_REQUEST['chengji'])?intval($_REQUEST['chengji']):0;
            $data['xuenian']      =  !empty($_REQUEST['xuenian'])?trim($_REQUEST['xuenian']):'';
            if($id){
                $ret        = $table->where(array('id'=>$id))->save($data);
            }else{
                $ret        = $table->add($data);
            }
            if($ret){
                $this->success('操作成功', U('index/chengji_list'));
            }else{
                $this->error('操作失败');
            }
        }else{
            $stu_html    = $this->getStuHtml($info['stu_id']);
            $this->assign('stu_html',$stu_html);// 搜索参数
            $kecheng_html    = $this->getKechengHtml($info['kecheng_id']);
            $this->assign('kecheng_html',$kecheng_html);// 搜索参数
            $this->assign('info',!empty($info)?$info:array());
            $this->display(); // 输出模板
        }
    }

    //删除成绩
    public function del_chengji(){
        $id   = I('id',0,'intval');
        if(empty($id)){
            $this->error('非法参数', U('index/chengji_list'));
        }
        $company    = M('chengji');
        $ret        = $company->where(array('id'=>$id))->delete();
        if($ret){
            $this->success('操作成功', U('index/chengji_list'));
        }else{
            $this->error('操作失败', U('index/chengji_list'));
        }
    }


    //学籍管理
    public function xueji_list(){
        $postArr['banji_id']       = I('banji_id',0,'int');
        $postArr['xuehao']       = I('xuehao','','trim');
        $where = array();
        if(!empty($postArr['banji_id'])){
            $where['banji_id']  = $postArr['banji_id'];
        }
        if(!empty($postArr['xuehao'])){
            $where['xuehao']  = array('like', "%{$postArr['xuehao']}%");
        }
        $company    = M('xueji'); // 实例化User对象
        $count      = $company->where($where)->count();// 查询满足要求的总记录数
        $Page       = $this->getPage($count,20);// 实例化分页类 传入总记录数和每页显示的记录数
        //分页跳转的时候保证查询条件
        foreach($postArr as $key=>$val) {
            $Page->parameter[$key]   =   urlencode($val);
        }
        $show       = $Page->show();// 分页显示输出
// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $list = $company->where($where)->limit($Page->firstRow.','.$Page->listRows)->select();
        $banji_list          = M('banji')->select();
        $banji_list          = $this->tranKeyArray($banji_list,'id');
        $xuexiao_list          = M('xuexiao')->select();
        $xuexiao_list          = $this->tranKeyArray($xuexiao_list,'id');
        if(!empty($list)){
            foreach ($list as $k=>$v){
                $list[$k]['banji_name'] = !empty($banji_list[$v['banji_id']]['name'])?$banji_list[$v['banji_id']]['name']:'';
                $list[$k]['xuexiao_name'] = !empty($xuexiao_list[$v['xuexiao_id']]['name'])?$xuexiao_list[$v['xuexiao_id']]['name']:'';
            }
        }
        $banji_html    = $this->getBanjiHtml($postArr['banji_id']);
        $this->assign('banji_html',$banji_html);// 搜索参数
        $this->assign('postArr',$postArr);// 搜索参数
        $this->assign('list',$list);// 赋值数据集
        $this->assign('page',$show);// 赋值分页输出
        $this->display(); // 输出模板
    }

    public function gere_info(){
        $info = M('xueji')->where(1)->find();
        $banji_html    = $this->getBanjiHtml($info['banji_id']);
        $this->assign('banji_html',$banji_html);// 搜索参数
        $xuexiao_html    = $this->getXuexiaoHtml($info['xuexiao_id']);
        $this->assign('xuexiao_html',$xuexiao_html);// 搜索参数
        $this->assign('info',$info);// 赋值分页输出
        $this->display(); // 输出模板
    }

    //新增学籍
    public function add_xueji(){
        $id         = I('stu_id',0,'intval');
        $table      = M('xueji');
        if(!empty($id)){
            $info = $table->where(array('stu_id'=>$id))->find();
        }
        if(IS_AJAX){
            if(empty($_REQUEST['xuehao'])){
                $this->error('请填写学生学号！');
            }
            if(empty($_REQUEST['name'])){
                $this->error('请填写学生姓名！');
            }

            $data['age']       =  !empty($_REQUEST['age'])?intval($_REQUEST['age']):0;
            $data['xuehao']      =  !empty($_REQUEST['xuehao'])?trim($_REQUEST['xuehao']):'';
            $data['name']        =  !empty($_REQUEST['name'])?trim($_REQUEST['name']):'';
            $data['sex']        =  !empty($_REQUEST['sex'])?trim($_REQUEST['sex']):'';
            $data['ruxueshijian']        =  !empty($_REQUEST['ruxueshijian'])?trim($_REQUEST['ruxueshijian']):'';
            $data['biyeshijian']        =  !empty($_REQUEST['biyeshijian'])?trim($_REQUEST['biyeshijian']):'';
            $data['xuezhi']        =  !empty($_REQUEST['xuezhi'])?trim($_REQUEST['xuezhi']):'';
            $data['xuexiao_id']        =  !empty($_REQUEST['xuexiao_id'])?intval($_REQUEST['xuexiao_id']):0;
            $data['banji_id']        =  !empty($_REQUEST['banji_id'])?intval($_REQUEST['banji_id']):0;
            if($id){
                $ret        = $table->where(array('stu_id'=>$id))->save($data);
            }else{
                $ret        = $table->add($data);
            }
            if($ret){
                $this->success('操作成功', U('index/xueji_list'));
            }else{
                $this->error('操作失败');
            }
        }else{
            $banji_html    = $this->getBanjiHtml($info['banji_id']);
            $this->assign('banji_html',$banji_html);// 搜索参数
            $xuexiao_html    = $this->getXuexiaoHtml($info['xuexiao_id']);
            $this->assign('xuexiao_html',$xuexiao_html);// 搜索参数
            $this->assign('info',!empty($info)?$info:array());
            $this->display(); // 输出模板
        }
    }

    //删除学籍
    public function del_xueji(){
        $id   = I('stu_id',0,'intval');
        if(empty($id)){
            $this->error('非法参数', U('index/xueji_list'));
        }
        $company    = M('xueji');
        $ret        = $company->where(array('stu_id'=>$id))->delete();
        if($ret){
            $this->success('操作成功', U('index/xueji_list'));
        }else{
            $this->error('操作失败', U('index/xueji_list'));
        }
    }

    private function getXuexiaoHtml($select_id = ''){
        $select_id = !empty($select_id)?trim($select_id):'';
        $list   = M('xuexiao')->select();
        $html   = '<select  name="xuexiao_id" id="xuexiao_id">';
        $html  .= '<option value="">请选择</option>';
        if(!empty($list)){
            foreach ($list as $v){
                if($v['id'] == $select_id){
                    $html .= '<option value="'.$v['id'].'" selected>'.$v['name'].'</option>';
                }else{
                    $html .= '<option value="'.$v['id'].'">'.$v['name'].'</option>';
                }
            }
        }
        $html .= '</select>';
        return $html;
    }

    private function getBanjiHtml($select_id = ''){
        $select_id = !empty($select_id)?trim($select_id):'';
        $list   = M('banji')->select();
        $html   = '<select  name="banji_id">';
        $html  .= '<option value="">请选择</option>';
        if(!empty($list)){
            foreach ($list as $v){
                if($v['id'] == $select_id){
                    $html .= '<option value="'.$v['id'].'" selected>'.$v['name'].'</option>';
                }else{
                    $html .= '<option value="'.$v['id'].'">'.$v['name'].'</option>';
                }
            }
        }
        $html .= '</select>';
        return $html;
    }

    //后台首页
    public function index(){
        $this->display(); // 输出模板
    }

    //后台首页
    public function login(){
        $pwd          = I('pwd','','trim');
        $name         = I('name','','trim');
        if($pwd && $name){
            $info         = M('xueji')->where(array('pwd'=>$pwd,'name'=>$name))->find();
        }
        if($info){
            $_SESSION['is_admin'] = $info['is_admin'];
            $_SESSION['stu_id']   = $info['stu_id'];
            $_SESSION['detail']   = $info;
            $this->success('操作成功');
        }else{
            $this->error('操作失败');
        }
    }


    public function uploadImg(){
        $ret    = array('code'=>0,'url'=>'');
        $upFile = $_FILES['file'];
        //判断文件是否为空或者出错
        if ($upFile['error']==0 && !empty($upFile)) {
            $dirpath                = 'Public/static/img/' . date('Ym') . '/'; // 设置上传目录,相对路径
            if (is_dir($dirpath) || @mkdir($dirpath, 0766, true)) {
                if(!empty($_FILES['file']['name'])){
                    $tmp       = explode('.',$_FILES['file']['name']);
                    $type_name = end($tmp);
                    $filename = date('YmdHis').rand(1,100).'.'.$type_name;
                    $queryPath = $dirpath.$filename;
                    //move_uploaded_file将浏览器缓存file转移到服务器文件夹
                    if(move_uploaded_file($_FILES['file']['tmp_name'],$queryPath)){
                        $ret['url']          = $queryPath;
                        $ret['code']         = 1;
                    }
                }
            }
        }
        echo json_encode($ret);
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

}