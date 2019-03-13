<?php
namespace app\manage\controller;
use \think\Controller;
use \think\Db;

class Login extends Controller
{
    public function index()
    {
		//return 'demo';
		session(null);	
        return $this->fetch();
    }
	
	public function chklogin(){
		$data = [
			'username'  => input('post.username'),
			'userpwd' => input('post.userpwd')
		];
		
		$validate = new \app\common\validate\UserInfo;
		if (!$validate->scene('login')->check($data)) {
			//dump($validate->getError());
			$this->error($validate->getError());
		}else{
			
			$data['userpwd']=md5($data['userpwd']);
			$res=model('UserInfo')->where($data)->find();
			//dump($res);
			
			if($res){
				if ($res['states']==1) {
					$arr['lastlogintime']=date('Y-m-d H:i:j');
					$arr['lastloginip']=request()->ip();
					$arr['logintimes']=Db::raw('logintimes+1');
					
					model('UserInfo')->save($arr,['username'=>$data['username']]);
					
					session('userid',$res['userid']);
					session('groupid',$res['groupid']);
					session('username',$res['username']);
					session('cityid',$res['cityid']);
	
					//$this->success('登陆成功',url('/manage/index'));
					$this->redirect(url('/manage/index'));
				}else{
					$this->error('用户名等待审核中或审核未通过');
				}

			}else{
				$this->error('用户名或密码错误');
			}
			 
			
		}
		
	}

}
