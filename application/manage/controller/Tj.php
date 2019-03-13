<?php
namespace app\manage\controller;
use think\Validate;

class Tj extends Base
{
    public function inst($Action='list')
    {
        $data=input('get.');
        //dump($data);
        $map=[];

        if(!empty($data['cname'])){
            $map['cname']=['like','%'.$data['cname'].'%'];
        }
        if(!empty($data['instrCategory'])){
            $map['instrCategory']=['like','%'.$data['instrCategory'].'%'];
        }
        if(!empty($data['lab_no'])){
            $map['lab_no']=['IN',"select classid from nclass_city where FIND_IN_SET(".$data['lab_no'].",classpath)"];
        }
        $map['is_check']=1;
        //SELECT cname,lab_no,FLOOR(sum(TIME_TO_SEC(timediff(e_time,b_time)))/60) as kjs,COUNT(ID) as cs,count(distinct instid) as fws,sum(feeB) as fees,sum(yps) as yps_all FROM v_inst_yy where is_check=1 group by cname order by kjs desc
        $list=db('v_inst_yy')
            ->field('cname,lab_no,FLOOR(sum(TIME_TO_SEC(timediff(e_time,b_time)))/60) as kjs,COUNT(ID) as cs,count(distinct instid) as fws,sum(feeB) as fees,sum(yps) as yps_all')
            ->where($map)
            ->group('cname')
            ->order("kjs desc");

        if($Action=='list'){
            $res=$list->paginate(['query'=> $data]);
            $this->assign([
                'Action'=>$Action,
                'res'=>$res
            ]);
            return $this->fetch();
        }else{
            $res=$list->select();
            $this->assign([
                'Action'=>$Action,
                'res'=>$res
            ]);
            export($Action,$this->fetch());
        }
    }

    public function user_yy($Action='list')
    {
        $data=input('get.');
        //dump($data);
        $map=[];

        if(!empty($data['sj1'])){
            $map['r_date']=['>=TIME',$data['sj1']];
        }
        if(!empty($data['sj2'])){
            $map['r_date']=['<=TIME',$data['sj2']];
        }
        if(!empty($data['realname'])){
            $map['realname']=['like','%'.$data['realname'].'%'];
        }
        if(!empty($data['lab_no'])){
            $map['lab_no']=['IN',"select classid from nclass_city where FIND_IN_SET(".$data['lab_no'].",classpath)"];
        }
        if(!empty($data['daoshi'])){
            $map['daoshi']=['EXISTS',"select * from user_info where username like '%".$data['daoshi']."%' and userid=v_inst_yy.dsid"];
        }
        $map['is_check']=1;
        //SELECT username,realname,cityid,dsid,FLOOR(sum(TIME_TO_SEC(timediff(e_time,b_time)))/60) as kjs,COUNT(ID) as cs,count(distinct instid) as fws,sum(feeB) as fees,sum(yps) as yps_all FROM v_inst_yy where is_check=1 group by username,realname,cityid,dsid order by username
        $list=db('v_inst_yy')
            ->field('username,realname,cityid,dsid,FLOOR(sum(TIME_TO_SEC(timediff(e_time,b_time)))/60) as kjs,COUNT(ID) as cs,count(distinct instid) as fws,sum(feeB) as fees,sum(yps) as yps_all')
            ->where($map)
            ->group('username,realname,cityid,dsid')
            ->order("username");

        if($Action=='list'){
            $res=$list->paginate(['query'=> $data]);
            $this->assign([
                'Action'=>$Action,
                'res'=>$res
            ]);
            return $this->fetch();
        }else{
            $res=$list->select();
            $this->assign([
                'Action'=>$Action,
                'res'=>$res
            ]);
            export($Action,$this->fetch());
        }
    }
    public function year_yy()
    {
        $yearid=input('yearid');
        $arr=[];
        if(empty($yearid)) { $yearid=date('Y'); }
        $x=$y='';
        for($i=1;$i<=12;$i++){
            $x.="'".$i."月',";
            $y.=db('inst_yy')->where("year(r_date)='$yearid' and month(r_date)=$i")->count().',';
        }
        $arr['yearid']=$yearid;
        $arr['x']=trim($x,',');
        $arr['y']=trim($y,',');
        $this->assign([
            'arr'=>$arr,
        ]);
        return $this->fetch();
    }
    public function year_daoshi()
    {
        $yearid=input('yearid');
        $arr=[];
        $map['is_check']=1;
        $map['year(r_date)']=$yearid;
        $res=db('v_inst_yy')
            ->field('dsid,cityid,FLOOR(sum(TIME_TO_SEC(timediff(e_time,b_time)))/60) as kjs,COUNT(ID) as cs,count(distinct instid) as fws')
            ->where($map)
            ->group('dsid,cityid')
            ->order("dsid")->select();
        $x=$y='';
        foreach ($res as $v){
            $x.="'".getUserName($v['dsid'])."',";
            $y.="{value:".$v['kjs'].",name:'".getUserName($v['dsid'])."'},";
        }
        $arr['yearid']=$yearid;
        $arr['x']=trim($x,',');
        $arr['y']=trim($y,',');

        $this->assign([
            'arr'=>$arr,
        ]);

        return $this->fetch();
    }
    public function user_dept()
    {
        $yearid=input('yearid');
        $arr=[];
        $map['year(addtime)']=$yearid;
        $res=db('user_info')
            ->field('cityid,COUNT(*) as sm')
            ->where($map)
            ->group('cityid')->select();
        $x=$y='';
        foreach ($res as $v){
            $deptname=getclassname("nclass_city",$v['cityid']);
            $x.="'".$deptname."',";
            $y.="{value:".$v['sm'].",name:'".$deptname."'},";
        }
        $arr['yearid']=$yearid;
        $arr['x']=trim($x,',');
        $arr['y']=trim($y,',');

        $this->assign([
            'arr'=>$arr,
        ]);

        return $this->fetch();
    }
}