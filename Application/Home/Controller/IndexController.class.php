<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends BaseController {
    //新增专业
    public function admin_edit(){
        $id         = 1;
        $table      = M('admin');
        if(!empty($id)){
            $info = $table->where(array('id'=>$id))->find();
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
    //专业管理
    public function zhuanye_list(){
        $postArr['name'] = I('name','','trim');
        $where = array();
        if(!empty($postArr['name'])){
            $where['name']  = array('like', "%{$postArr['name']}%");
        }
        $company    = M('zhuanye'); // 实例化User对象
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

    //新增专业
    public function add_zhuanye(){
        $id         = I('id',0,'intval');
        $table      = M('zhuanye');
        if(!empty($id)){
            $info = $table->where(array('id'=>$id))->find();
        }
        if(IS_AJAX){
            if(empty($_REQUEST['name'])){
                $this->error('请填写专业名称！');
            }
            $data['name']   =  !empty($_REQUEST['name'])?trim($_REQUEST['name']):'';
            $data['info']   =  !empty($_REQUEST['info'])?trim($_REQUEST['info']):'';
            if($id){
                $ret        = $table->where(array('id'=>$id))->save($data);
            }else{
                $ret        = $table->add($data);
            }
            if($ret){
                $this->success('操作成功', U('index/zhuanye_list'));
            }else{
                $this->error('操作失败');
            }
        }else{
            $this->assign('info',!empty($info)?$info:array());
            $this->display(); // 输出模板
        }
    }

    //删除专业
    public function del_zhuanye(){
        $id   = I('id',0,'intval');
        if(empty($id)){
            $this->error('非法参数', U('index/zhuanye_list'));
        }
        $company    = M('zhuanye');
        $ret        = $company->where(array('id'=>$id))->delete();
        if($ret){
            $this->success('操作成功', U('index/zhuanye_list'));
        }else{
            $this->error('操作失败', U('index/zhuanye_list'));
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

    private function getZhuanyeHtml($select_id = ''){
        $select_id = !empty($select_id)?trim($select_id):'';
        $list   = M('zhuanye')->select();
        $html   = '<select  name="zhuanye_id">';
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
        $this->display(); // 输出模板
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



    //奖惩管理
    public function jiangcheng_list(){
        $postArr['stu_id']       = I('stu_id',0,'int');
        $where = array();
        if(!empty($postArr['stu_id'])){
            $where['stu_id']  = $postArr['stu_id'];
        }
        $company    = M('jiangcheng'); // 实例化User对象
        $count      = $company->where($where)->count();// 查询满足要求的总记录数
        $Page       = $this->getPage($count,20);// 实例化分页类 传入总记录数和每页显示的记录数
        //分页跳转的时候保证查询条件
        foreach($postArr as $key=>$val) {
            $Page->parameter[$key]   =   urlencode($val);
        }
        $show       = $Page->show();// 分页显示输出
// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $list = $company->where($where)->limit($Page->firstRow.','.$Page->listRows)->select();
        $stu_list          = M('xueji')->select();
        $stu_list          = $this->tranKeyArray($stu_list,'stu_id');
        if(!empty($list)){
            foreach ($list as $k=>$v){
                $list[$k]['stu_name'] = !empty($stu_list[$v['stu_id']]['name'])?$stu_list[$v['stu_id']]['name']:'';
            }
        }
        $stu_html    = $this->getStuHtml($postArr['stu_id']);
        $this->assign('stu_html',$stu_html);// 搜索参数
        $this->assign('postArr',$postArr);// 搜索参数
        $this->assign('list',$list);// 赋值数据集
        $this->assign('page',$show);// 赋值分页输出
        $this->display(); // 输出模板
    }

    //新增奖惩
    public function add_jiangcheng(){
        $id         = I('id',0,'intval');
        $table      = M('jiangcheng');
        if(!empty($id)){
            $info = $table->where(array('id'=>$id))->find();
        }
        if(IS_AJAX){
            if(empty($_REQUEST['stu_id'])){
                $this->error('请选择学生！');
            }
            if(empty($_REQUEST['shijian'])){
                $this->error('请选择时间！');
            }
            if(empty($_REQUEST['jiang']) && empty($_REQUEST['cheng'])){
                $this->error('请填写奖励或者惩罚！');
            }

            $data['stu_id']       =  !empty($_REQUEST['stu_id'])?intval($_REQUEST['stu_id']):'';
            $data['shijian']      =  !empty($_REQUEST['shijian'])?trim($_REQUEST['shijian']):'';
            $data['jiang']        =  !empty($_REQUEST['jiang'])?trim($_REQUEST['jiang']):'';
            $data['cheng']        =  !empty($_REQUEST['cheng'])?trim($_REQUEST['cheng']):'';
            if($id){
                $ret        = $table->where(array('id'=>$id))->save($data);
            }else{
                $ret        = $table->add($data);
            }
            if($ret){
                $this->success('操作成功', U('index/jiangcheng_list'));
            }else{
                $this->error('操作失败');
            }
        }else{
            $stu_html    = $this->getStuHtml($info['stu_id']);
            $this->assign('stu_html',$stu_html);// 搜索参数
            $this->assign('info',!empty($info)?$info:array());
            $this->display(); // 输出模板
        }
    }

    //删除奖惩
    public function del_jiangcheng(){
        $id   = I('id',0,'intval');
        if(empty($id)){
            $this->error('非法参数', U('index/jiangcheng_list'));
        }
        $company    = M('jiangcheng');
        $ret        = $company->where(array('id'=>$id))->delete();
        if($ret){
            $this->success('操作成功', U('index/jiangcheng_list'));
        }else{
            $this->error('操作失败', U('index/jiangcheng_list'));
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
        $html   = '<select  name="xuexiao_id">';
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


    private function getProjectHtml($select_id = ''){
        $select_id = !empty($select_id)?trim($select_id):'';
        $list   = M('admin_projects')->select();
        $html   = '<select  name="project_code">';
        $html  .= '<option value="">请选择</option>';
        if(!empty($list)){
            foreach ($list as $v){
                if($v['project_code'] == $select_id){
                    $html .= '<option value="'.$v['project_code'].'" selected>'.$v['project_name'].'</option>';
                }else{
                    $html .= '<option value="'.$v['project_code'].'">'.$v['project_name'].'</option>';
                }
            }
        }
        $html .= '</select>';
        return $html;
    }

    //项目表
    public function project_list(){
        $postArr['project_code'] = I('project_code','','trim');
        $postArr['project_name']   = I('project_name','','trim');
        $where = array();
        $where['admin_id'] = 1;
        if(!empty($postArr['project_code'])){
            $where['project_code']  = $postArr['project_code'];
        }
        if(!empty($postArr['project_name'])){
            $where['project_name']  = array('like', "%{$postArr['project_name']}%");
        }
        $company    = M('admin_projects'); // 实例化User对象
        $count      = $company->where($where)->count();// 查询满足要求的总记录数
        $Page       = $this->getPage($count,20);// 实例化分页类 传入总记录数和每页显示的记录数
        //分页跳转的时候保证查询条件
        foreach($postArr as $key=>$val) {
            $Page->parameter[$key]   =   urlencode($val);
        }
        $show       = $Page->show();// 分页显示输出
// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $list = $company->where($where)->limit($Page->firstRow.','.$Page->listRows)->select();
        if(!empty($list)){
            foreach ($list as $k=>$v){
                $list[$k]['add_time']    = !empty($v['add_time'])?date('Y-m-d H:i:s',$v['add_time']):'';
                $list[$k]['update_time'] = !empty($v['update_time'])?date('Y-m-d H:i:s',$v['update_time']):'';
            }
        }
        $this->assign('postArr',$postArr);// 搜索参数
        $this->assign('list',$list);// 赋值数据集
        $this->assign('page',$show);// 赋值分页输出
        $this->display(); // 输出模板
    }

    //新增项目
    public function add_project(){
        $id         = I('id',0,'intval');
        $table      = M('admin_projects');
        if(!empty($id)){
            $info = $table->where(array('id'=>$id))->find();
        }
        if(IS_AJAX){
            $data['admin_id']       =  1;//$SESSION['admin_id']
            $data['project_code']   =  !empty($_REQUEST['project_code'])?trim($_REQUEST['project_code']):'';
            $data['project_name']     =  !empty($_REQUEST['project_name'])?trim($_REQUEST['project_name']):'';
            $data['project_detail']     =  !empty($_REQUEST['project_detail'])?trim($_REQUEST['project_detail']):'';
            if($id){
                $data['update_time']        = time();
                $ret        = $table->where(array('id'=>$id))->save($data);
            }else{
                $data['add_time']           = time();
                $ret        = $table->add($data);
            }
            if($ret){
                $this->success('操作成功', U('index/project_list'));
            }else{
                $this->error('操作失败');
            }
        }else{
            $list = !empty($info['table_detail'])?json_decode($info['table_detail'],1):array();
            $this->assign('info',!empty($info)?$info:array());
            $this->assign('list',$list);// 赋值数据集
            $this->display(); // 输出模板
        }
    }

    //删除项目
    public function del_project(){
        $id   = I('id',0,'intval');
        if(empty($id)){
            $this->error('非法参数', U('index/project_list'));
        }
        $company    = M('admin_projects');
        $ret        = $company->where(array('id'=>$id))->delete();
        if($ret){
            $this->success('操作成功', U('index/project_list'));
        }else{
            $this->error('操作失败', U('index/project_list'));
        }
    }

    //数据表
    public function table_list(){
        $postArr['project_code'] = I('project_code','','trim');
        $postArr['table_name']   = I('table_name','','trim');
        $postArr['table_annotation']   = I('table_annotation','','trim');
        $where = array();
        if(!empty($postArr['project_code'])){
            $where['project_code']  = $postArr['project_code'];
        }
        if(!empty($postArr['table_name'])){
            $where['table_name']  = $postArr['table_name'];
        }
        if(!empty($postArr['table_annotation'])){
            $where['table_annotation']  = array('like', "%{$postArr['table_annotation']}%");
        }
        $company    = M('admin_tables'); // 实例化User对象
        $count      = $company->where($where)->count();// 查询满足要求的总记录数
        $Page       = $this->getPage($count,20);// 实例化分页类 传入总记录数和每页显示的记录数
        //分页跳转的时候保证查询条件
        foreach($postArr as $key=>$val) {
            $Page->parameter[$key]   =   urlencode($val);
        }
        $show       = $Page->show();// 分页显示输出
// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $list = $company->where($where)->limit($Page->firstRow.','.$Page->listRows)->select();
        $project_list      = M('admin_projects')->where(array('admin_id'=>1))->select();
        $project_list      = $this->tranKeyArray($project_list,'project_code');
        if(!empty($list)){
            foreach ($list as $k=>$v){
                $list[$k]['project_name'] = !empty($project_list[$v['project_code']]['project_name'])?$project_list[$v['project_code']]['project_name']:'';
                $list[$k]['add_time']    = !empty($v['add_time'])?date('Y-m-d H:i:s',$v['add_time']):'';
                $list[$k]['update_time'] = !empty($v['update_time'])?date('Y-m-d H:i:s',$v['update_time']):'';
            }
        }
        $project_html    = $this->getProjectHtml($postArr['project_code']);
        $this->assign('project_html',$project_html);// 搜索参数
        $this->assign('postArr',$postArr);// 搜索参数
        $this->assign('list',$list);// 赋值数据集
        $this->assign('page',$show);// 赋值分页输出
        $this->display(); // 输出模板
    }

    //新增数据表
    public function add_table(){
        $id         = I('id',0,'intval');
        $table      = M('admin_tables');
        if(!empty($id)){
            $info = $table->where(array('id'=>$id))->find();
        }
        if(IS_AJAX){
            if(empty($_REQUEST['project_code']) || empty($_REQUEST['table_name'])){
                $this->error('参数缺失！');
            }
            $table_detail            = $this->getTableDetail();
            $data['admin_id']       =  1;//$SESSION['admin_id']
            $data['project_code']   =  !empty($_REQUEST['project_code'])?trim($_REQUEST['project_code']):'';
            $data['table_name']     =  !empty($_REQUEST['table_name'])?trim($_REQUEST['table_name']):'';
            $data['table_annotation']     =  !empty($_REQUEST['table_annotation'])?trim($_REQUEST['table_annotation']):'';
            $data['table_detail']   =  !empty($table_detail)?json_encode($table_detail):'';
            if($id){
                $data['update_time']        = time();
                $ret        = $table->where(array('id'=>$id))->save($data);
            }else{
                $data['add_time']           = time();
                $ret        = $table->add($data);
            }
            if($ret){
                $this->success('操作成功', U('index/table_list'));
            }else{
                $this->error('操作失败');
            }
        }else{
            $project_html    = $this->getProjectHtml($info['project_code']);
            $this->assign('project_html',$project_html);// 搜索参数
            $list = !empty($info['table_detail'])?json_decode($info['table_detail'],1):array();
            if(empty($list)){
                $list[] = array();
                $list[] = array();
                $list[] = array();
                $list[] = array();
                $list[] = array();
                $list[] = array();
                $list[] = array();
                $list[] = array();
            }
            $this->assign('info',!empty($info)?$info:array());
            $this->assign('list',$list);// 赋值数据集
            $this->display(); // 输出模板
        }
    }

    //删除数据表
    public function del_table(){
        $id   = I('id',0,'intval');
        if(empty($id)){
            $this->error('非法参数', U('index/table_list'));
        }
        $company    = M('admin_tables');
        $ret        = $company->where(array('id'=>$id))->delete();
        if($ret){
            $this->success('操作成功', U('index/table_list'));
        }else{
            $this->error('操作失败', U('index/table_list'));
        }
    }

    private function getTableDetail(){
        $ret     = array();
        $columns = array('field_name','field_type','max_length','default_value','annotation','index_type');
        $num     = !empty($_REQUEST['field_name'])?count($_REQUEST['field_name']):0;
        if($num){
            for($i=0;$i<$num;$i++){
                $tmp    = array();
                foreach($columns as $col){
                    if(!empty($_REQUEST[$col][$i])){
                        $tmp[$col]  = $_REQUEST[$col][$i];
                    }else{
                        continue 2;
                    }
                }
                $ret[] = $tmp;
            }
        }
        return $ret;
    }

    //菜单信息表
    public function menu_list(){
        $table_html                 = '';
        $postArr['project_code']   = I('project_code','','trim');
        $where = $where2 = array();
        if(!empty($postArr['project_code'])){
            $where2['project_code']  = $postArr['project_code'];
        }
        if(!empty($postArr['project_code'])){
            $project_info = M('admin_projects')->where($where2)->find();
        }
        if(!empty($project_info) && !empty($project_info['project_code'])){
            $where['project_code']  = $project_info['project_code'];
            $company      = M('project_menus'); // 实例化User对象
           // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
            $list = $company->where($where)->order('sort desc,menu_id asc')->select();
            if(!empty($list)){
                foreach ($list as $k=>$v){
                    $list[$k]['add_time']     = !empty($v['add_time'])?date('Y-m-d H:i:s',$v['add_time']):'';
                    $list[$k]['update_time']  = !empty($v['update_time'])?date('Y-m-d H:i:s',$v['update_time']):'';
                }
                $key_son_list      = $this->tranKeyArray($list,'menu_id');
                foreach ($list as $k=>$v){
                    //第一层
                    if(!empty($v['parent_id']) && isset($key_son_list[$v['parent_id']])){
                        $key_son_list[$v['parent_id']]['son_list'][] = $v;
                        unset($list[$k]);
                    }
                }
/*                //第二层
                foreach ($list as $k=>$v){
                    if(!empty($v['parent_id']) && isset($key_son_list[$v['parent_id']]) && !empty($v['son_ids'])){
                        $key_son_list[$v['parent_id']]['son_list'][] = $v;
                        unset($list[$k]);
                    }
                }*/
            }
            //生成 table_html
            if(!empty($list)){
                $show_column = array('menu_name','sort','is_show','add_time','update_time');
                foreach($list as $v){
                    $table_html.='<tr>';
                    foreach($show_column as $col){
                        if($col == 'menu_name'){
                            $v[$col] = '&nbsp;&nbsp;&nbsp;&nbsp;'.$v[$col];
                            $table_html.="<td style=\"text-align:left\">{$v[$col]}</td>";
                        }elseif($col == 'is_show'){
                            $v[$col] = $v[$col] == 1?'是':'否';
                            $table_html.="<td>{$v[$col]}</td>";
                        }else{
                            $table_html.="<td>{$v[$col]}</td>";
                        }
                    }
                    //顶级目录操作
                    $operate_str = "<a href='".U('index/add_menu')."&parent_id={$v['menu_id']}&project_code={$v['project_code']}' title='新增子菜单'><span>新增子菜单</span></a>&nbsp;&nbsp;";
                    $operate_str .= "<a href='".U('index/add_menu')."&id={$v['menu_id']}' title='修改'><span>修改</span></a>&nbsp;&nbsp;";
                    $operate_str .= "<a href='".U('index/del_menu')."&id={$v['menu_id']}' title='删除'><span>删除</span></a>";
                    $table_html.="<td>".$operate_str."</td>";
                    $table_html.='</tr>';
                    if(!empty($key_son_list[$v['menu_id']]['son_list'])){
                        foreach ($key_son_list[$v['menu_id']]['son_list'] as $sl){
                            $table_html.='<tr>';
                            foreach($show_column as $col){
                                if($col == 'menu_name'){
                                    $sl[$col] = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$sl[$col];
                                    $table_html.="<td style=\"text-align:left\">{$sl[$col]}</td>";
                                }elseif($col == 'is_show'){
                                    $sl[$col] = $sl[$col] == 1?'是':'否';
                                    $table_html.="<td>{$sl[$col]}</td>";
                                }else{
                                    $table_html.="<td>{$sl[$col]}</td>";
                                }
                            }
                            //二级目录操作
                            $operate_str = "<a href='".U('index/add_menu')."&parent_id={$sl['menu_id']}&project_code={$v['project_code']}' title='新增子菜单'><span>新增子菜单</span></a>&nbsp;&nbsp;";
                            $operate_str .= "<a href='".U('index/add_menu')."&id={$sl['menu_id']}' title='修改'><span>修改</span></a>&nbsp;&nbsp;";
                            $operate_str .= "<a href='".U('index/del_menu')."&id={$sl['menu_id']}' title='删除'><span>删除</span></a>";
                            $table_html.="<td>".$operate_str."</td>";
                            $table_html.='</tr>';
                            if(!empty($key_son_list[$sl['menu_id']]['son_list'])){
                                foreach ($key_son_list[$sl['menu_id']]['son_list'] as $ssl){
                                    $table_html.='<tr>';
                                    foreach($show_column as $col){
                                        if($col == 'menu_name'){
                                            $ssl[$col] = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$ssl[$col];
                                            $table_html.="<td style=\"text-align:left\">{$ssl[$col]}</td>";
                                        }elseif($col == 'is_show'){
                                            $ssl[$col] = $ssl[$col] == 1?'是':'否';
                                            $table_html.="<td>{$ssl[$col]}</td>";
                                        }else{
                                            $table_html.="<td>{$ssl[$col]}</td>";
                                        }
                                    }
                                    //底层菜单操作
                                    $operate_str  = "<a href='".U('index/add_menu')."&id={$ssl['menu_id']}' title='修改'><span>修改</span></a>&nbsp;&nbsp;";
                                    $operate_str .= "<a href='".U('index/del_menu')."&id={$ssl['menu_id']}' title='删除'><span>删除</span></a>&nbsp;&nbsp;";
                                    $table_html.="<td>".$operate_str."</td>";
                                    $table_html.='</tr>';
                                }
                            }
                        }
                    }
                }
            }
        }else{
            $list = array();
        }
        $project_html    = $this->getProjectHtml($postArr['project_code']);
        $this->assign('project_html',$project_html);// 搜索参数
        $this->assign('table_html',$table_html);// 搜索参数
        $this->assign('postArr',$postArr);// 搜索参数
        $this->assign('list',$list);// 赋值数据集
        $this->assign('page','');// 赋值分页输出
        $this->display(); // 输出模板
    }

    //新增菜单
    public function add_menu(){
        $id                = I('id',0,'intval');
        $parent_id         = I('parent_id',0,'intval');
        $project_code      = I('project_code','','trim');
        $table             = M('project_menus');
        if(!empty($id)){
            $info = $table->where(array('menu_id'=>$id))->find();
            if(empty($info)){
                $this->error('数据异常');
            }
        }else{
            $info['is_show'] = 1;
        }
        if(IS_AJAX){
            if(empty($_REQUEST['project_code']) || empty($_REQUEST['menu_name'])){
                $this->error('参数缺失！');
            }
            $data                = $table->create(); // 把无用的都顾虑掉了
            $data['sort']        = intval($data['sort']);
            $data['parent_id']   = intval($data['parent_id']);
            if($id){
                $data['update_time']        = time();
                $ret        = $table->where(array('menu_id'=>$id))->save($data);
            }else{
                $data['add_time']           = time();
                $nid = $ret                  = $table->add($data);
            }
            if($ret){
                if(empty($id)){
                    if(!empty($data['parent_id'])){
                        $father        = $table->where(array('menu_id'=>$data['parent_id']))->find();
                        if(!empty($father)){
                            if(empty($father['son_ids'])){
                                $son_ids = $nid;
                            }else{
                                $son_ids = $father['son_ids'].','.$nid;
                            }
                            $table->where(array('menu_id'=>$data['parent_id']))->save(array('son_ids'=>$son_ids));
                        }
                    }
                }
                $this->success('操作成功', U('index/menu_list').'&project_code='.$data['project_code']);
            }else{
                $this->error('操作失败'.'&project_code='.$data['project_code']);
            }
        }else{
            if(empty($project_code)){
                $project_code = !empty($info['project_code'])?$info['project_code']:'';
            }
            $project_html    = $this->getProjectHtml($project_code);
            $this->assign('project_html',$project_html);// 搜索参数
            if(!empty($parent_id)){
                $info['parent_id'] = $parent_id;
            }
            $this->assign('info',!empty($info)?$info:array());
            $this->display(); // 输出模板
        }
    }

    //删除菜单
    public function del_menu(){
        $id   = I('id',0,'intval');
        if(empty($id)){
            $this->error('非法参数', U('index/menu_list'));
        }
        $table    = M('project_menus');
        $info = $table->where(array('menu_id'=>$id))->find();
        if(empty($info)){
            $this->error('数据异常');
        }
        $ret        = $table->where(array('menu_id'=>$id))->delete();
        if($ret){
            if(!empty($info['parent_id'])){
                //删除
                $father        = $table->where(array('menu_id'=>$info['parent_id']))->find();
                if(!empty($father)){
                    if(!empty($father['son_ids'])){
                        $son_list = explode(',',$father['son_ids']);
                        foreach($son_list as $k=>$v){
                            if($v  == $id){
                                unset($son_list[$k]);
                            }
                        }
                        $son_ids = !empty($son_list)?implode(',',$son_list):'';
                        $table->where(array('menu_id'=>$info['parent_id']))->save(array('son_ids'=>$son_ids));
                    }
                }
            }
            //删除
            if(!empty($info['son_ids'])){
                $son_list = explode(',',$info['son_ids']);
                foreach($son_list as $son){
                    if(!empty($son['son_ids'])){
                        $table->where(array('menu_id'=>array('in',$son['son_ids'])))->delete();
                    }
                }
                $table->where(array('menu_id'=>array('in',$info['son_ids'])))->delete();
            }
            $this->success('操作成功', U('index/menu_list').'&project_code='.$info['project_code']);
        }else{
            $this->error('操作失败', U('index/menu_list').'&project_code='.$info['project_code']);
        }
    }

    //后台首页
    public function index(){
        $this->display(); // 输出模板
    }

    public function search_car(){
        $postArr['license_number'] = I('license_number','','trim');
        $postArr['chassis_number'] = I('chassis_number','','trim');
        $where = array();
        if(!empty($postArr['license_number'])){
            $where['license_number']  = array('like', "%{$postArr['license_number']}%");
        }
        if(!empty($postArr['chassis_number'])){
            $where['chassis_number']  = array('like', "%{$postArr['chassis_number']}%");
        }
        $company    = M('case'); // 实例化User对象
        $count      = $company->where($where)->count();// 查询满足要求的总记录数
        $Page       = $this->getPage($count,20);// 实例化分页类 传入总记录数和每页显示的记录数
       //分页跳转的时候保证查询条件
        foreach($postArr as $key=>$val) {
            $Page->parameter[$key]   =   urlencode($val);
        }
        $show       = $Page->show();// 分页显示输出
// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $list = $company->where($where)->limit($Page->firstRow.','.$Page->listRows)->select();
        if(!empty($list)){
            foreach ($list as $k=>$v){
                $list[$k]['status'] = $v['status'] == 1?'已完结':'未完结';
            }
        }
        $this->assign('postArr',$postArr);// 搜索参数
        $this->assign('list',$list);// 赋值数据集
        $this->assign('page',$show);// 赋值分页输出
        $this->display(); // 输出模板
    }

    public function search_people(){
        $postArr['car_owner'] = I('car_owner','','trim');
        $postArr['id_card'] = I('id_card','','trim');
        $where = array();
        if(!empty($postArr['car_owner'])){
            $where['car_owner']  = array('like', "%{$postArr['car_owner']}%");
        }
        if(!empty($postArr['id_card'])){
            $where['id_card']  = array('like', "%{$postArr['id_card']}%");
        }
        $company    = M('case'); // 实例化User对象
        $count      = $company->where($where)->count();// 查询满足要求的总记录数
        $Page       = $this->getPage($count,20);// 实例化分页类 传入总记录数和每页显示的记录数
        //分页跳转的时候保证查询条件
        foreach($postArr as $key=>$val) {
            $Page->parameter[$key]   =   urlencode($val);
        }
        $show       = $Page->show();// 分页显示输出
// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $list = $company->where($where)->limit($Page->firstRow.','.$Page->listRows)->select();
        if(!empty($list)){
            foreach ($list as $k=>$v){
                $list[$k]['status'] = $v['status'] == 1?'已完结':'未完结';
            }
        }
        $this->assign('postArr',$postArr);// 搜索参数
        $this->assign('list',$list);// 赋值数据集
        $this->assign('page',$show);// 赋值分页输出
        $this->display(); // 输出模板
    }

    public function search_company(){
        $postArr['company_name'] = I('company_name','','trim');
        $where = array();
        if(!empty($postArr['company_name'])){
            $where['company_name']  = array('like', "%{$postArr['company_name']}%");
        }
        $company    = M('case'); // 实例化User对象
        $count      = $company->where($where)->count();// 查询满足要求的总记录数
        $Page       = $this->getPage($count,20);// 实例化分页类 传入总记录数和每页显示的记录数
        //分页跳转的时候保证查询条件
        foreach($postArr as $key=>$val) {
            $Page->parameter[$key]   =   urlencode($val);
        }
        $show       = $Page->show();// 分页显示输出
// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $list = $company->where($where)->limit($Page->firstRow.','.$Page->listRows)->select();
        if(!empty($list)){
            foreach ($list as $k=>$v){
                $list[$k]['status'] = $v['status'] == 1?'已完结':'未完结';
            }
        }
        $this->assign('postArr',$postArr);// 搜索参数
        $this->assign('list',$list);// 赋值数据集
        $this->assign('page',$show);// 赋值分页输出
        $this->display(); // 输出模板
    }

    public function search_phone(){
        $postArr['car_owner_phone'] = I('car_owner_phone','','trim');
        $where = array();
        if(!empty($postArr['car_owner_phone'])){
            $where['car_owner_phone']  = array('like', "%{$postArr['car_owner_phone']}%");
        }
        $company    = M('case'); // 实例化User对象
        $count      = $company->where($where)->count();// 查询满足要求的总记录数
        $Page       = $this->getPage($count,20);// 实例化分页类 传入总记录数和每页显示的记录数
        //分页跳转的时候保证查询条件
        foreach($postArr as $key=>$val) {
            $Page->parameter[$key]   =   urlencode($val);
        }
        $show       = $Page->show();// 分页显示输出
// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $list = $company->where($where)->limit($Page->firstRow.','.$Page->listRows)->select();
        if(!empty($list)){
            foreach ($list as $k=>$v){
                $list[$k]['status'] = $v['status'] == 1?'已完结':'未完结';
            }
        }
        $this->assign('postArr',$postArr);// 搜索参数
        $this->assign('list',$list);// 赋值数据集
        $this->assign('page',$show);// 赋值分页输出
        $this->display(); // 输出模板
    }


    //新增/编辑案件
    public function add_case(){
        $id         = I('id',0,'intval');
        $pin_code   = I('pin_code','','trim');
        $from       = I('from','search_car','trim');
        if(!empty($id)){
            $info = M('case')->where(array('id'=>$id))->find();
        }
        if(!empty($pin_code)){
            $info = M('case')->where(array('pin_code'=>$pin_code))->find();
        }
        if(IS_AJAX){
            $company    = M('case');
            $data       = $company->create(); // 把无用的都顾虑掉了
            $data['danger_time'] = !empty($data['danger_time'])?strtotime($data['danger_time']):0;
            if($id){
                $ret        = $company->where(array('id'=>$id))->save($data);
            }else{
                $ret        = $company->add($data);
            }
            if($ret){
                $this->success('操作成功', U('index/'.$from));
            }else{
                $this->error('操作失败');
            }
        }else{
            !empty($info) && $info['danger_time'] = !empty($info['danger_time'])?date('Y-m-d',$info['danger_time']):'';
            $this->assign('info',!empty($info)?$info:array());
            $this->assign('from',$from);
        }
        $this->display(); // 输出模板
    }

    //新增/编辑案件
    public function look_case(){
        $id         = I('id',0,'intval');
        $pin_code   = I('pin_code','','trim');
        $from       = I('from','search_car','trim');
        if(!empty($id)){
            $info = M('case')->where(array('id'=>$id))->find();
        }
        if(!empty($pin_code)){
            $info = M('case')->where(array('pin_code'=>$pin_code))->find();
        }
        if(IS_AJAX){
            $company    = M('case');
            $data       = $company->create(); // 把无用的都顾虑掉了
            $data['danger_time'] = !empty($data['danger_time'])?strtotime($data['danger_time']):0;
            if($id){
                $ret        = $company->where(array('id'=>$id))->save($data);
            }else{
                $ret        = $company->add($data);
            }
            if($ret){
                $this->success('操作成功', U('index/'.$from));
            }else{
                $this->error('操作失败');
            }
        }else{
            !empty($info) && $info['danger_time'] = !empty($info['danger_time'])?date('Y-m-d',$info['danger_time']):'';
            $this->assign('info',!empty($info)?$info:array());
        }
        $this->display(); // 输出模板
    }

    //删除案件
    public function del_case(){
        $id   = I('id',0,'intval');
        $from = I('from','search_car','trim');
        if(empty($id)){
            $this->error('非法参数', U('index/'.$from));
        }
        $company    = M('case');
        $ret        = $company->where(array('id'=>$id))->delete();
        if($ret){
            $this->success('操作成功', U('index/'.$from));
        }else{
            $this->error('操作失败', U('index/'.$from));
        }
    }


    public function criminal_case(){
        $postArr['fraud_type']           = I('fraud_type',0,'intval');
        $postArr['company_name']         = I('company_name','','trim');
        $postArr['name']                  = I('name','','trim');
        $postArr['id_card']               = I('id_card','','trim');
        $where = array();
        if(!empty($postArr['company_name'])){
            $where['company_name']  = array('like', "%{$postArr['company_name']}%");
        }
        if(!empty($postArr['fraud_type'])){
            $where['fraud_type']  = $postArr['fraud_type'];
        }
        if(!empty($postArr['name'])){
            $where['name']  = array('like', "%{$postArr['name']}%");
        }
        if(!empty($postArr['id_card'])){
            $where['id_card']  = array('like', "%{$postArr['id_card']}%");
        }
        $company    = M('criminal_case'); // 实例化User对象
        $count      = $company->where($where)->count();// 查询满足要求的总记录数
        $Page       = $this->getPage($count,20);// 实例化分页类 传入总记录数和每页显示的记录数
        //分页跳转的时候保证查询条件
        foreach($postArr as $key=>$val) {
            $Page->parameter[$key]   =   urlencode($val);
        }
        $show       = $Page->show();// 分页显示输出
// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $list = $company->where($where)->limit($Page->firstRow.','.$Page->listRows)->select();
        $fraud_type_list   = M('fraud_type')->select();
        $company_list      = M('risk_company')->select();
        $company_list      = $this->tranKeyArray($company_list,'id');
        $fraud_type_list   = $this->tranKeyArray($fraud_type_list,'id');
        if(!empty($list)){
            foreach ($list as $k=>$v){
                $list[$k]['fraud_type_name'] = !empty($fraud_type_list[$v['fraud_type']]['name'])?$fraud_type_list[$v['fraud_type']]['name']:'';
            }
        }
        $company_html    = $this->getCompanyHtml($postArr['company_id']);
        $fraud_type_html = $this->getFraudTypeHtml($postArr['fraud_type']);
        $this->assign('company_html',$company_html);
        $this->assign('fraud_type_html',$fraud_type_html);
        $this->assign('postArr',$postArr);// 搜索参数
        $this->assign('list',$list);// 赋值数据集
        $this->assign('page',$show);// 赋值分页输出
        $this->display(); // 输出模板
    }

    //新增/编辑刑事案件
    public function add_criminal_case(){
        $id = I('id',0,'intval');
        if(!empty($id)){
            $info = M('criminal_case')->where(array('id'=>$id))->find();
        }
        if(IS_AJAX){
            $company    = M('criminal_case');
            $data       = $company->create(); // 把无用的都顾虑掉了
            if($id){
                $ret        = $company->where(array('id'=>$id))->save($data);
            }else{
                $ret        = $company->add($data);
            }
            if($ret){
                $this->success('操作成功', U('index/criminal_case'));
            }else{
                $this->error('操作失败');
            }
        }else{
            $company_html    = $this->getCompanyHtml($info['company_id']);
            $fraud_type_html = $this->getFraudTypeHtml($info['fraud_type']);
            $this->assign('company_html',$company_html);
            $this->assign('fraud_type_html',$fraud_type_html);
            $this->assign('info',!empty($info)?$info:array());
        }
        $this->display(); // 输出模板
    }

    //删除刑事案件
    public function del_criminal_case(){
        $id = I('id',0,'intval');
        if(empty($id)){
            $this->error('非法参数', U('index/criminal_case'));
        }
        $company    = M('criminal_case');
        $ret        = $company->where(array('id'=>$id))->delete();
        if($ret){
            $this->success('操作成功', U('index/criminal_case'));
        }else{
            $this->error('操作失败', U('index/criminal_case'));
        }
    }

    public function risk_car(){
        $postArr['brand_type']           = I('brand_type',0,'intval');
        $postArr['fraud_type']           = I('fraud_type',0,'intval');
        $postArr['license_number']       = I('license_number','','trim');
        $postArr['chassis_number']       = I('chassis_number','','trim');
        $postArr['vehicle_type']         = I('vehicle_type','','trim');
        $where = array();
        if(!empty($postArr['brand_type'])){
            $where['brand_type']  = $postArr['brand_type'];
        }
        if(!empty($postArr['fraud_type'])){
            $where['fraud_type']  = $postArr['fraud_type'];
        }
        if(!empty($postArr['license_number'])){
            $where['license_number']  = array('like', "%{$postArr['license_number']}%");
        }
        if(!empty($postArr['chassis_number'])){
            $where['chassis_number']  = array('like', "%{$postArr['chassis_number']}%");
        }
        if(!empty($postArr['vehicle_type'])){
            $where['vehicle_type']  = array('like', "%{$postArr['vehicle_type']}%");
        }
        $company    = M('risk_car'); // 实例化User对象
        $count      = $company->where($where)->count();// 查询满足要求的总记录数
        $Page       = $this->getPage($count,20);// 实例化分页类 传入总记录数和每页显示的记录数
        //分页跳转的时候保证查询条件
        foreach($postArr as $key=>$val) {
            $Page->parameter[$key]   =   urlencode($val);
        }
        $show       = $Page->show();// 分页显示输出
// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $list = $company->where($where)->limit($Page->firstRow.','.$Page->listRows)->select();
        $fraud_type_list   = M('fraud_type')->select();
        $brand_type_list   = M('brand_type')->select();
        $brand_type_list   = $this->tranKeyArray($brand_type_list,'id');
        $fraud_type_list   = $this->tranKeyArray($fraud_type_list,'id');
        if(!empty($list)){
            foreach ($list as $k=>$v){
                $list[$k]['fraud_type_name']    = !empty($fraud_type_list[$v['fraud_type']]['name'])?$fraud_type_list[$v['fraud_type']]['name']:'';
                $list[$k]['brand_type_name']    = !empty($brand_type_list[$v['brand_type']]['name'])?$brand_type_list[$v['brand_type']]['name']:'';
            }
        }
        $brand_type_html    = $this->getBrandTypeHtml($postArr['brand_type']);
        $fraud_type_html    = $this->getFraudTypeHtml($postArr['fraud_type']);
        $this->assign('brand_type_html',$brand_type_html);
        $this->assign('fraud_type_html',$fraud_type_html);
        $this->assign('postArr',$postArr);// 搜索参数
        $this->assign('list',$list);// 赋值数据集
        $this->assign('page',$show);// 赋值分页输出
        $this->display(); // 输出模板
    }

    //新增/编辑刑事案件
    public function add_risk_car(){
        $id = I('id',0,'intval');
        if(!empty($id)){
            $info = M('risk_car')->where(array('id'=>$id))->find();
        }
        if(IS_AJAX){
            $company    = M('risk_car');
            $data       = $company->create(); // 把无用的都顾虑掉了
            if($id){
                $ret        = $company->where(array('id'=>$id))->save($data);
            }else{
                $ret        = $company->add($data);
            }
            if($ret){
                $this->success('操作成功', U('index/risk_car'));
            }else{
                $this->error('操作失败');
            }
        }else{
            $brand_type_html    = $this->getBrandTypeHtml($info['brand_type']);
            $fraud_type_html = $this->getFraudTypeHtml($info['fraud_type']);
            $this->assign('brand_type_html',$brand_type_html);
            $this->assign('fraud_type_html',$fraud_type_html);
            $this->assign('info',!empty($info)?$info:array());
        }
        $this->display(); // 输出模板
    }

    //删除刑事案件
    public function del_risk_car(){
        $id = I('id',0,'intval');
        if(empty($id)){
            $this->error('非法参数', U('index/criminal_case'));
        }
        $company    = M('criminal_case');
        $ret        = $company->where(array('id'=>$id))->delete();
        if($ret){
            $this->success('操作成功', U('index/criminal_case'));
        }else{
            $this->error('操作失败', U('index/criminal_case'));
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

    private function getCompanyHtml($select_id = 0){
        $select_id = !empty($select_id)?intval($select_id):0;
        $list   = M('risk_company')->select();
        $html   = '<select  name="company_id">';
        $html  .= '<option value="0">请选择</option>';
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
    private function getFraudTypeHtml($select_id = 0){
        $select_id = !empty($select_id)?intval($select_id):0;
        $list   = M('fraud_type')->select();
        $html   = '<select  name="fraud_type">';
        $html  .= '<option value="0">请选择</option>';
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
    private function getBrandTypeHtml($select_id = 0){
        $select_id = !empty($select_id)?intval($select_id):0;
        $list   = M('brand_type')->select();
        $html   = '<select  name="brand_type">';
        $html  .= '<option value="0">请选择</option>';
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



    /*
     * 通讯录模块
     * risk_book
     */
    //列表
    public function risk_book(){
        $postArr['linkman']        = I('linkman','','trim');
        $postArr['company_name']   = I('company_name','','trim');
        $postArr['area_name']      = I('area_name','','trim');
        $where = array();
        if(!empty($postArr['linkman'])){
            $where['linkman']  = array('like', "%{$postArr['linkman']}%");
        }
        if(!empty($postArr['company_name'])){
            $where['company_name']  = array('like', "%{$postArr['company_name']}%");
        }
        if(!empty($postArr['area_name'])){
            $where['area_name']  = array('like', "%{$postArr['area_name']}%");
        }
        $company    = M('risk_book'); // 实例化User对象
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

    //新增
    public function add_risk_book(){
        $id       = I('id',0,'intval');
        if(!empty($id)){
            $info = M('risk_book')->where(array('id'=>$id))->find();
        }
        if(IS_AJAX){
            $user               = M('risk_book');
            $data               = $user->create(); // 把无用的都顾虑掉了
            if($id){
                $ret        = $user->where(array('id'=>$id))->save($data);
            }else{
                $ret        = $user->add($data);
            }
            if($ret){
                $this->success('操作成功', U('index/risk_book'));
            }else{
                $this->error('操作失败');
            }
        }else{
            $this->assign('info',!empty($info)?$info:array());
        }
        $this->display(); // 输出模板
    }
    

    //删除
    public function del_risk_book(){
        $id = I('id',0,'intval');
        if(empty($id)){
            $this->error('非法参数', U('index/risk_book'));
        }
        $company    = M('risk_book');
        $ret        = $company->where(array('id'=>$id))->delete();
        if($ret){
            $this->success('操作成功', U('index/risk_book'));
        }else{
            $this->error('操作失败', U('index/risk_book'));
        }
    }


 /*
 * 风险人员
 * risk_people
 */
    //列表
    public function risk_people(){
        $postArr['name']        = I('name','','trim');
        $postArr['id_card']   = I('id_card','','trim');
        $postArr['phone']      = I('phone','','trim');
        $postArr['driving_licence']      = I('driving_licence','','trim');
        $where = array();
        if(!empty($postArr['name'])){
            $where['name']  = array('like', "%{$postArr['name']}%");
        }
        if(!empty($postArr['id_card'])){
            $where['id_card']  = array('like', "%{$postArr['id_card']}%");
        }
        if(!empty($postArr['phone'])){
            $where['phone']  = array('like', "%{$postArr['phone']}%");
        }
        if(!empty($postArr['driving_licence'])){
            $where['driving_licence']  = array('like', "%{$postArr['driving_licence']}%");
        }
        $company    = M('risk_people'); // 实例化User对象
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

    //新增
    public function add_risk_people(){
        $id       = I('id',0,'intval');
        if(!empty($id)){
            $info = M('risk_people')->where(array('id'=>$id))->find();
        }
        if(IS_AJAX){
            $user               = M('risk_people');
            $data               = $user->create(); // 把无用的都顾虑掉了
            if($id){
                $ret        = $user->where(array('id'=>$id))->save($data);
            }else{
                $ret        = $user->add($data);
            }
            if($ret){
                $this->success('操作成功', U('index/risk_people'));
            }else{
                $this->error('操作失败');
            }
        }else{
            $this->assign('info',!empty($info)?$info:array());
        }
        $this->display(); // 输出模板
    }


    //删除
    public function del_risk_people(){
        $id = I('id',0,'intval');
        if(empty($id)){
            $this->error('非法参数', U('index/risk_people'));
        }
        $company    = M('risk_people');
        $ret        = $company->where(array('id'=>$id))->delete();
        if($ret){
            $this->success('操作成功', U('index/risk_people'));
        }else{
            $this->error('操作失败', U('index/risk_people'));
        }
    }


/*
* 风险机构
* risk_company
*/
    //列表
    public function risk_company(){
        $postArr['name']        = I('name','','trim');
        $postArr['id_card']   = I('id_card','','trim');
        $postArr['phone']      = I('phone','','trim');
        $postArr['driving_licence']      = I('driving_licence','','trim');
        $where = array();
        if(!empty($postArr['name'])){
            $where['name']  = array('like', "%{$postArr['name']}%");
        }
        if(!empty($postArr['id_card'])){
            $where['id_card']  = array('like', "%{$postArr['id_card']}%");
        }
        if(!empty($postArr['phone'])){
            $where['phone']  = array('like', "%{$postArr['phone']}%");
        }
        if(!empty($postArr['driving_licence'])){
            $where['driving_licence']  = array('like', "%{$postArr['driving_licence']}%");
        }
        $company    = M('risk_company'); // 实例化User对象
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

    //新增
    public function add_risk_company(){
        $id       = I('id',0,'intval');
        if(!empty($id)){
            $info = M('risk_company')->where(array('id'=>$id))->find();
        }
        if(IS_AJAX){
            $user               = M('risk_company');
            $data               = $user->create(); // 把无用的都顾虑掉了
            if($id){
                $ret        = $user->where(array('id'=>$id))->save($data);
            }else{
                $ret        = $user->add($data);
            }
            if($ret){
                $this->success('操作成功', U('index/risk_company'));
            }else{
                $this->error('操作失败');
            }
        }else{
            $this->assign('info',!empty($info)?$info:array());
        }
        $this->display(); // 输出模板
    }


    //删除
    public function del_risk_company(){
        $id = I('id',0,'intval');
        if(empty($id)){
            $this->error('非法参数', U('index/risk_company'));
        }
        $company    = M('risk_company');
        $ret        = $company->where(array('id'=>$id))->delete();
        if($ret){
            $this->success('操作成功', U('index/risk_company'));
        }else{
            $this->error('操作失败', U('index/risk_company'));
        }
    }

/*
* 风险手机号
* risk_phone
*/
    //列表
    public function risk_phone(){
        $postArr['name']        = I('name','','trim');
        $postArr['phone']       = I('phone','','trim');
        $where = array();
        if(!empty($postArr['name'])){
            $where['name']  = array('like', "%{$postArr['name']}%");
        }
        if(!empty($postArr['phone'])){
            $where['phone']  = array('like', "%{$postArr['phone']}%");
        }
        $company    = M('risk_phone'); // 实例化User对象
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

    //新增
    public function add_risk_phone(){
        $id       = I('id',0,'intval');
        if(!empty($id)){
            $info = M('risk_phone')->where(array('id'=>$id))->find();
        }
        if(IS_AJAX){
            $user               = M('risk_phone');
            $data               = $user->create(); // 把无用的都顾虑掉了
            if($id){
                $ret        = $user->where(array('id'=>$id))->save($data);
            }else{
                $ret        = $user->add($data);
            }
            if($ret){
                $this->success('操作成功', U('index/risk_phone'));
            }else{
                $this->error('操作失败');
            }
        }else{
            $this->assign('info',!empty($info)?$info:array());
        }
        $this->display(); // 输出模板
    }


    //删除
    public function del_risk_phone(){
        $id = I('id',0,'intval');
        if(empty($id)){
            $this->error('非法参数', U('index/risk_phone'));
        }
        $company    = M('risk_phone');
        $ret        = $company->where(array('id'=>$id))->delete();
        if($ret){
            $this->success('操作成功', U('index/risk_phone'));
        }else{
            $this->error('操作失败', U('index/risk_phone'));
        }
    }

    //新增/编辑企业
    public function add_company(){
        $id = I('id',0,'intval');
        if(!empty($id)){
            $info = M('company')->where(array('id'=>$id))->find();
        }
        if(IS_AJAX){
            $company    = M('company');
            $data       = $company->create(); // 把无用的都顾虑掉了
            if($id){
                $ret        = $company->where(array('id'=>$id))->save($data);
            }else{
                $ret        = $company->add($data);
            }
            if($ret){
                $this->success('操作成功', U('index/company_list'));
            }else{
                $this->error('操作失败');
            }
        }else{
            $this->assign('info',!empty($info)?$info:array());
        }
        $this->display(); // 输出模板
    }

    //删除企业
    public function del_company(){
        $id = I('id',0,'intval');
        if(empty($id)){
            $this->error('非法参数', U('index/company_list'));
        }
        $company    = M('company');
        $ret        = $company->where(array('id'=>$id))->delete();
        if($ret){
            $this->success('操作成功', U('index/company_list'));
        }else{
            $this->error('操作失败', U('index/company_list'));
        }
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