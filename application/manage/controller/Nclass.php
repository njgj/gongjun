<?php
namespace app\manage\controller;
use think\Validate;

class Nclass extends Base
{
	public function index($tb='nclass')
    {
		$res=db($tb)->order('orderid')->select();
		$res=tree($res);
		 //dump($res);
		$this->assign([
			'res'=>$res,
			'tb'=>$tb,
		]);	
        return $this->fetch();
    }

    public function code($tb='nclass')
    {
        $res=db($tb)->order('orderid')->select();
        $res=tree($res);
        //dump($res);
        $this->assign([
            'res'=>$res,
            'tb'=>$tb,
        ]);
        return $this->fetch();
    }

	public function add($tb='nclass')
    {
		$option=getTreeOption($tb,0,input('classid'));
		
		$this->assign([
			'option'=>$option,
			'Action'=>'Add',
			'tb'=>$tb,
		]);
		return $this->fetch();
    }
	public function edit($tb='nclass')
    {	
		$res=db($tb)->where('classid',input('classid'))->find();

		$option=getTreeOption($tb,0,$res['parentid']);
		
		$this->assign([
			'res'=>$res,
			'option'=>$option,
			'Action'=>'Edit',
			'tb'=>$tb,
		]);
		return $this->fetch('add');
    }		
	public function save($tb='nclass')
    {
		$data=input('post.');
		$rule = [
            'classname'     => 'require|min:2',
            'parentid'     => 'number',
            'orderid'   => 'number'
        ];
        $msg  =   [
            'classname.require'     => '类名不能为空',
			'classname.min'     => '类名至少2位',
            'parentid.number'     => '上级ID只能为数字',
            'orderid.number'   => '排序ID只能为数字'
        ];
        //$validate = Validate::make($rule, $msg);
		$validate = new Validate($rule, $msg);
        if (!$validate->check($data)) {
            $this->error($validate->getError());
        }else{
			
			if($data['Action']=='Add'){
			   $classid=db($tb)->strict(false)->insertGetId($data);	
				if($classid){
					if($this->saveClassPath($tb,$data['parentid'],$classid)){
						htmlendjs('新增成功');
					}
				}
				
			}
			if($data['Action']=='Edit'){
			   $classid=input('classid/d');
				//dump($data);
			    db($tb)->where('classid',$classid)->strict(false)->data($data)->fetchsql(false)->update();
				$this->saveClassPath($tb,$data['parentid'],$classid);
				htmlendjs('修改成功');	
			}
			
		}

    }
	public function del($tb='nclass')
    {	
		$res=db($tb)->where("find_in_set(".input('post.id/d').",classpath)")->delete();
		return $res;
    }
	
	private function saveClassPath($tb,$parentid,$classid){
		$classpath='';
		if($parentid==0){
			$classpath='0,'.$classid.',';
		}else{
			$old=db($tb)->where('classid',$parentid)->value('classpath');
			if($old){
				$classpath=$old.$classid.',';
			}
		}
		
		return db($tb)->where('classid',$classid)->data(['classpath' => $classpath])->update();
	}
}