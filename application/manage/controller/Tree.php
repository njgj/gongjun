<?php
namespace app\manage\controller;
use think\Validate;

class Tree extends Base
{
    public function index(){
        //tablename,textid,codeid,id,isdx,flag
        $data=input('get.');
        if(!isset($data['tb']) || !isset($data['textid']) || !isset($data['codeid'])){
            return 0;
        }
        $id=(isset($data['id']))?$data['id']:0;
        $isdx=(isset($data['isdx']))?$data['isdx']:0;
        $iscode=(isset($data['iscode']))?$data['iscode']:0;
        $flag=(isset($data['flag']))?$data['flag']:0;

        $res=db($data['tb'])->where("find_in_set($id,classpath)")->select();
        $arr=[];
        $i=1;
        foreach ($res as $v){
            $arr[]=[
                'id'=>$v['classid'],
                'pId'=>$v['parentid'],
                'name'=>$v['classname'],
                'code'=>$v['code'],
                'open'=>($i==1)?true:false
            ];
            $i++;
        }
		$json=json_encode($arr,JSON_UNESCAPED_UNICODE);
		//return $res;
        $this->assign([
            'textid'=>$data['textid'],
            'codeid'=>$data['codeid'],
            'iscode'=>$iscode,
            'isdx'=>$isdx,
            'flag'=>$flag,
            'res'=>$json
        ]);
        return $this->fetch();
    }

}