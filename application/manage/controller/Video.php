<?php
namespace app\manage\controller;
use think\Validate;

class Video extends Base
{
    public function index()
    {
        $data=input('get.');
        //dump($data);
        $map=[];

        if(!empty($data['title'])){
            $map['title']=['like','%'.$data['title'].'%'];
        }
        if(!empty($data['classid'])){
            $map['classid']=$data['classid'];
        }
        if(@$data['states']!=''){
            $map['states']=$data['states'];
        }
        $res=model('Video')->where($map)->order("id desc")->paginate(['query'=> $data]);
        //dump($res);
        $this->assign([
            'res'=>$res
        ]);
        return $this->fetch();
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

        $res=model('Video')->where('id',input('id/d'))->find();
        $this->assign([
            'res'=>$res,
            'Action'=>'Edit'
        ]);
        return $this->fetch('add');
    }

    public function save()
    {
        $data = input('post.');
        $rule = [
            'title|标题' => 'require|min:2',
            'classid|类别' => 'number',
            'content|内容' => 'require'
        ];
        //$validate = Validate::make($rule, $msg);
        $validate = new Validate($rule);
        if (!$validate->check($data)) {
            $this->error($validate->getError());
        } else {

            if($data['Action']=='Add'){
                $data['userid']=session('userid');
                $data['addtime']=date('Y-m-d H:i:s');
                if(model('Video')->allowField(true)->save($data)){
                    htmlendjs('新增成功');
                    //$this->success('新增成功','index');
                }
            }

            if($data['Action']=='Edit'){
                $data['addtime']=date('Y-m-d H:i:s');
                model('Video')->allowField(true)->save($data,['id'=>input('id/d')]);
                htmlendjs('修改成功');
            }

        }
    }

    public function del(){
        $res=model('Video')->where('id','in',input('post.id'))->delete();
        return $res;
    }

    public function chk(){
        $res=model('Video')->save(['states'=>input('post.states/d')],[
            'id'=>input('post.id/d')
        ]);
        return $res;
    }
}