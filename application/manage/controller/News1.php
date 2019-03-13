<?php
namespace app\manage\controller;
use \think\Controller;

class News extends Base
{
    public function index()
    {

        return $this->fetch();
    }
	
    public function show()
    {
		$news=model('News');
		$map=[];
		
		$title=input('title');
		if($title){
			$map['title']=['like','%'.$title.'%'];
		}

		
		$count=$news->where($map)->count();
		//$data=$news->where($map)->paginate(10);
		
		$page=input('page/d',1);
		$limit=input('limit/d',10);	
		$data=$news->where($map)->limit(($page-1)*$limit,$limit)->order('id desc')->select();
		
		$arr=[];
		foreach($data as $v){
			
			$arr[]=[
			'id'=>$v['id'],
			'title'=>$v['title'],
			'username'=>getUserName($v['userid']),
			'classid'=>$v['classid'],
			'classname'=>getClassName('nclass',$v['classid']),
			'addtime'=>$v['addtime'],
			];
		}

		
		$result=[
		'code'=>0,
		'msg'=>'数据返回成功',
		'count'=>$count,
		'data'=>$arr
		];
	
		//dump($a);
		return json($result);
		
    }

    public function add(){

        $this->assign([
            'res'=>[
              'addtime'=>date('Y-m-d')
            ],
            'Action'=>'Add'
        ]);
        return $this->fetch();
    }

    public function edit(){

        $res=model('News')->where('id',input('id/d'))->find();
        $this->assign([
            'res'=>$res,
            'Action'=>'Edit'
        ]);
        return $this->fetch('add');
    }

    public function save($tb='nclass')
    {
        $data = input('post.');
        $rule = [
            'classname' => 'require|min:2',
            'parentid' => 'number',
            'orderid' => 'number'
        ];
        $msg = [
            'classname.require' => '类名不能为空',
            'classname.min' => '类名至少2位',
            'parentid.number' => '上级ID只能为数字',
            'orderid.number' => '排序ID只能为数字'
        ];
        //$validate = Validate::make($rule, $msg);
        $validate = new Validate($rule, $msg);
        if (!$validate->check($data)) {
            $this->error($validate->getError());
        } else {

            if ($data['Action'] == 'Add') {
                $classid = db($tb)->strict(false)->insertGetId($data);
                if ($classid) {
                    if ($this->saveClassPath($tb, $data['parentid'], $classid)) {
                        htmlendjs('新增成功');
                    }
                }

            }
            if ($data['Action'] == 'Edit') {
                $classid = input('classid/d');
                //dump($data);
                db($tb)->where('classid', $classid)->strict(false)->data($data)->fetchsql(false)->update();
                $this->saveClassPath($tb, $data['parentid'], $classid);
                htmlendjs('修改成功');
            }

        }
    }

    public function del(){
        $res=model('News')->where('id','in',input('post.id'))->delete();
        return $res;
    }

}