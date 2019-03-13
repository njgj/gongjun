<?php
namespace app\manage\controller;
use app\common\validate\UserInfo as  UserValidate;

class User extends Base
{
    public function index(){
		$data=input('get.');
		//dump($data);
		$map=[];

        if(!empty($data['username'])){
            $map['username']=['like','%'.$data['username'].'%'];
        }
        if(!empty($data['groupid'])){
            $map['groupid']=$data['groupid'];
        }
        if(!empty($data['cityid'])){
            $map['cityid']=$data['cityid'];
        }        
        if(@$data['states']!=''){
            $map['states']=$data['states'];
        }
		$res=model('UserInfo')->where($map)->order("userid desc")->paginate(['query'=> $data]);
	    //dump($res);
		$this->assign([
			'res'=>$res
		]);
		return $this->fetch();
	}

	public function add(){
        $this->assign([
            'Action'=>'Add',
        ]);
        return $this->fetch();
    }

    public function edit(){

        $res=model('UserInfo')->where('userid',input('userid/d'))->find();
        $this->assign([
            'res'=>$res,
            'Action'=>'Edit',
        ]);
        return $this->fetch('add');
    }

    public function save(){
        $data = input('post.');
        $data['userpwd']=md5('666666');
        $data['addtime']=date('Y-m-d H:i:j');
        $data['uptime']=date('Y-m-d H:i:j');
        $data['uptime']=date('Y-m-d H:i:j');
        $data['lastloginip']=request()->ip();

        $validate = new UserValidate;

        if($data['Action']=='Add'){
            if (!$validate->scene('add')->check($data)) {

                $this->error($validate->getError());
            }else{

                if(model('UserInfo')->allowField(true)->save($data)){
                    htmlendjs('注册成功');
                }
            }
        }

        if($data['Action']=='Edit'){
            if (!$validate->scene('edit')->check($data)) {

                $this->error($validate->getError());
            }else{

                model('UserInfo')->allowField(true)->save($data,['userid'=>input('userid/d')]);
                htmlendjs('修改成功');

            }
        }

    }

    public function del(){
        $res=model('UserInfo')->where('userid','in',input('post.id'))->delete();
        return $res;
    }

    public function chk(){
        $res=model('UserInfo')->save(['states'=>input('post.states/d')],[
            'userid'=>input('post.id/d')
        ]);
        return $res;
    }

    public function detail(){
        $res=model('UserInfo')->where('userid',input('userid/d'))->find();
        $this->assign([
            'res'=>$res,
        ]);
		return $this->fetch();
    }
	
    public function password(){

		return $this->fetch();
    }

    public function savepwd(){
        $data=input('post.');
        $data['uptime']=date('Y-m-d H:i:j');
		//dump($data);
		 $validate = new UserValidate;
		 if (!$validate->scene('password')->check($data)) {
			 $this->error($validate->getError());
		 }else{
			if($res=model('UserInfo')->where(['userpwd'=>md5($data['olduserpwd']),'userid'=>session('userid')])->find()){
				model('UserInfo')->save(['userpwd'=>md5($data['userpwd'])],['userid'=>session('userid')]);
				$this->success('密码修改成功','password');
			}else{
				$this->error('旧密码错误');
			}
		 }
    }	
	
    public function myedit(){
		$res=model('UserInfo')->where('userid',session('userid'))->find();
        $this->assign([
            'res'=>$res,
        ]);		
		return $this->fetch();
    }
	
	public function savemyedit(){
        $data=input('post.');
        $data['uptime']=date('Y-m-d H:i:j');
		//dump($data);
		 $validate = new UserValidate;
		 if (!$validate->scene('myedit')->check($data)) {
			 $this->error($validate->getError());
		 }else{
			    model('UserInfo')->save($data,['userid'=>session('userid')]);
     
				$this->success('修改成功','myedit');
			
		 }
    }

}