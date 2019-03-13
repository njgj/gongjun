<?php
namespace app\index\controller;
use think\Validate;
use think\Db;

class Index extends \think\Controller
{
    public function index()
    {
        $res=[];
        $res['news']=model('News')->where('states>0')->where('classid',1)->order('states desc,addtime desc')->select();
        
        $res['intro']=db('g_aboutus')->where('classid',1)->value('content');

        $res['skill']=[
            array('name'=>'PHP/LAMP/LNMP','label'=>'熟练','value'=>'90','style'=>'success'),
            array('name'=>'Asp.net/ASP+Sqlserver+IIS','label'=>'熟练','value'=>'90','style'=>'success'),
            array('name'=>'Html5+Css3','label'=>'熟练','value'=>'90','style'=>'success'),
            array('name'=>'Javascript+Jquery+Bootstrap','label'=>'良好','value'=>'70','style'=>'info'),
            array('name'=>'Photoshop+CDR','label'=>'良好','value'=>'70','style'=>'info'),
            array('name'=>'Linux','label'=>'良好','value'=>'50','style'=>'warning'),
            array('name'=>'Python、Vue','label'=>'Studying...','value'=>'30','style'=>'danger')
        ];

        //dump($res);
        $this->assign([
            'res'=>$res
        ]);
        return $this->fetch();
    }
	
    public function guestbook()
    {
       $data = input('post.');
        $rule = [
            'realname|姓名' => 'require|min:2',
            'tel|电话' => 'require',
            'email|邮箱' => 'require|email',
            'content|留言' => 'require|max:300',
        ];
        //$validate = Validate::make($rule, $msg);
        $validate = new Validate($rule);
        if (!$validate->check($data)) {
            return $validate->getError();
        } else {
            $data['title']=input('post.realname');
            $data['ip']=request()->ip();
            $data['addtime']=date('Y-m-d H:i:s');
            $data['classid']=1;
            return model('Zixun')->save($data);            
		}
    }	
}
