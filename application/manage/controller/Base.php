<?php
namespace app\manage\controller;
use \think\Controller;
/**
 * 后台公共控制器
 */
class Base extends Controller
{
    protected function _initialize()
    {

        // 判断登陆
        if (!session('userid')) {
            return $this->error('请登陆之后在操作！',url('/manage/login'));
        }

        //获取当前的模块
        //$module = request()->module();
        //获取模块下的控制器
         $controller = request()->controller();
         $action = request()->action();
         //index共有排除
         if($controller!='Index'){
             $this->check($controller,$action,session('groupid'));
         }

    }

    private function check($controller,$action,$groupid){
        //超管
        if($groupid==1){ return false; }

        $qx=db('user_group')->where('groupid',$groupid)->value('qx');
        $flag=0;
        if(strlen($qx)){
            $res=db('user_qx')->where("FIND_IN_SET(classid,'$qx')")->select();
            //dump($res);
            foreach ($res as $v){
                $str=str_replace('_','',$v['url']);
                //增删改时只比较控制器
                if($action=='add'||$action=='edit'||$action=='del'||$action='left'||$action='right'){
                    if(strpos($str,strtolower($controller.'/'))!==false){
                        $flag=1;
                        break;
                    }
                }else{
                    if(strpos($str,strtolower($controller.'/'.$action))!==false){
                        $flag=1;
                        break;
                    }
                }
                //echo $str.'|'.strtolower($controller.'/'.$action).'|'.$flag.'<br>';
            }

            if(!$flag){
                die('没有权限');
                //die($action.'没有权限');
            }
        }
    }
	
}