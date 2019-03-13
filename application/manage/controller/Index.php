<?php
namespace app\manage\controller;
use \think\Controller;

class Index extends Base
{
    public function index()
    {
		$qx=db('UserGroup')->where('groupid',session('groupid'))->value('qx');
        $res=db('UserQx')->where('classid','in',$qx)->where('parentid',0)->order('orderid')->select();
		
		foreach($res as $k=>$v){
			$res2=db('UserQx')->where('classid','in',$qx)->where('parentid',$v['classid'])->order('orderid')->select();
			$res[$k]['child'] = $res2; 
        }
        //if(session('groupid')==1){
            $welcome='index/welcome';
        //}else{
           // $welcome='files/tree';
        //}
        
		//return json($res);
        $this->assign([
            'res'=>$res,
            'welcome'=>$welcome
        ]);
        return $this->fetch();
    }
    public function welcome()
    {
        //return 'demo';

            $arr['users']=db('user_info')->count();
            $arr['users_wait']=db('user_info')->where('states=0')->count();   
            $arr['news']=db('g_news')->count();     
            $arr['news_wait']=db('g_news')->where('states=0')->count();    

  
            //通知公告
            $news=model('News')->where('states>0 and classid=1')->order('id desc')->limit(10)->select();
    

            //dump($hots);

            $this->assign([
                'news'=>$news,
                'arr'=>$arr
            ]);

            return $this->fetch();

    }

}

