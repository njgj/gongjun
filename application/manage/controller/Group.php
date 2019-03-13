<?php
namespace app\manage\controller;
use think\Validate;

class Group extends Base
{
    public function index(){


		$res=db('UserGroup')->order("orderid")->select();
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

        $res=db('UserGroup')->where('groupid',input('groupid/d'))->find();
        $this->assign([
            'res'=>$res,
            'Action'=>'Edit',
        ]);
        return $this->fetch('add');
    }

    public function save(){
        $data = input('post.');
        $validate = Validate::make([
            'groupname|名称'  => 'require|max:25',
            'orderid|排序' => 'number'
        ]);

        if($data['Action']=='Add'){

            if (!$validate->check($data)) {

                $this->error($validate->getError());
            }else{

                if(db('UserGroup')->strict(false)->insert($data)){
                    htmlendjs('新增成功');
                }
            }
        }

        if($data['Action']=='Edit'){
            if (!$validate->check($data)) {

                $this->error($validate->getError());
            }else{

                db('UserGroup')->strict(false)->where('groupid',input('post.groupid'))->update($data);
                htmlendjs('修改成功');

            }
        }

    }

    public function del(){
        $res=db('UserGroup')->where('groupid',input('post.id'))->delete();
        return $res;
    }
    public function tree(){
        $groupid=input('groupid/d');
        $qx=db('UserGroup')->where('groupid',$groupid)->value('qx');
        $data=db('UserQx')->order('orderid')->select();
        $arr=[];
        foreach ($data as $v){
            if( strpos(','.$qx.',', ','.$v['classid'].',') !== false){
                $chk=true;
                }else{
                $chk=false;
                }
            $arr[]=[
                'id'=>$v['classid'],
                'pId'=>$v['parentid'],
                'name'=>$v['classname'],
                'open'=>true,
                'checked'=>$chk
            ];
        }
        $this->assign([
            'groupid'=>$groupid,
            'res'=>json_encode($arr,JSON_UNESCAPED_UNICODE)
        ]);
        return $this->fetch();
    }

    public function treesave(){
        db('UserGroup')->where('groupid',input('post.groupid'))->update(['qx'=>input('post.ids')]);
        htmlendjs('修改成功');
    }
}