<?php
namespace app\index\controller;
use think\Db;
header("Access-Control-Allow-Origin: *");

class Api extends \think\Controller
{

    public function zixun(){
        $data=input('param.');
        //dump($data);
        $res=[
            'code'=>'ERROR',
            'msg'=>'保存失败'   
        ];
        $data['addtime']=date('Y-m-d H:i:s');
        if(model('Zixun')->allowField(true)->save($data)){
            $res=[
                'code'=>'OK',
                'msg'=>'保存成功'   
            ];
        }

        return json($res);
    }   

    public function focus(){
        $url='http://58.213.132.92:8080/gongjun/public/static/uploadfile/images/';
        $res=model('News')->where('states>0')->order('states desc,addtime desc')->select();
        $arr=[];
        foreach($res as $v){
            $img=$url.str_replace('\\','/',$v['imgurl']);
            $arr[]=[
                'id'=>$v['id'],
                'title'=>$v['title'],
                'url'=>'./case',
                'img'=>$img,
            ];
        }
        return json(['focus'=>$arr]);
    }  
    
    public function cases(){
        $data=input('param.');
        $url='http://58.213.132.92:8080/gongjun/public/static/uploadfile/images/';
        $map=[];
        if(!empty($data['title'])){
            $map['title']=['like','%'.$data['title'].'%'];
        }
        $map['states']=['>',0];
        $res=model('News')->where($map)->order('states desc,addtime desc')->paginate([
            'query'=>$data,
            'list_rows'=>input('param.limit',10)
        ]);
        $arr=[];
        foreach($res as $v){
            $img=$url.str_replace('\\','/',$v['imgurl']);
            $arr[]=[
                'id'=>$v['id'],
                'title'=>$v['title'],
                'url'=>[
                    'path'=>'info?id='.$v['id'],
                    'replace'=>false
                ],
                'img'=>$img,
                'desc'=>$v['content'],
                'src'=>$img,
                'meta'=>[
                    'source'=>$v['source'],
                    'date'=>$v['addtime']
                ] 
            ];
        }
        return json(['cases'=>$arr]);
    }   

     
    public function info($id){
        $res=model('News')->where('id',$id)->find();
        return json($res);
    }
    
    public function news(){
        $api='https://news-at.zhihu.com/api/4/news/latest';
        $json=file_get_contents($api);
        $arr=json_decode($json,true);
        //dump($arr);
        $newarr=[];
        foreach($arr['stories'] as $v){
           $newarr[]=[
               'id'=>$v['id'],
               'title'=>$v['title'],
               'url'=>$v['id'],
               'img'=>$v['images'],
           ];
        }
        return json(['news'=>$newarr,'date'=>$arr['date']]);
    }
 
    public function detail($id){
        $api='https://news-at.zhihu.com/api/4/news/'.$id;
        $json=file_get_contents($api);
        $v=json_decode($json,true);
        //dump($v);
  
           $newarr=[
               'id'=>$v['id'],
               'title'=>$v['title'],
               'content'=>$v['body'],
               'img'=>$v['image'],
           ];
        
        return json(['detail'=>$newarr]);
    }

    public function aboutus($classid){
        $res=db('g_aboutus')->where('classid',$classid)->find();
        return json($res);
    }

    public function skill(){
        $res=[
            array('name'=>'PHP/LAMP/LNMP','label'=>'熟练','value'=>'90','style'=>'success'),
            array('name'=>'Asp.net/ASP+Sqlserver+IIS','label'=>'熟练','value'=>'90','style'=>'success'),
            array('name'=>'Html5+Css3','label'=>'熟练','value'=>'90','style'=>'success'),
            array('name'=>'Javascript+Jquery+Bootstrap','label'=>'良好','value'=>'70','style'=>'info'),
            array('name'=>'Photoshop+CDR','label'=>'良好','value'=>'70','style'=>'info'),
            array('name'=>'Linux','label'=>'良好','value'=>'50','style'=>'warning'),
            array('name'=>'Python、Vue','label'=>'Studying...','value'=>'30','style'=>'danger')
        ];
        return json($res);
    }
}

