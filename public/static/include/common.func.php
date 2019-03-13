<?php
/*
 * 文件名：common.func.php
 * 功  能：系统公用函数库
 */

/* 防注入函数 */
if (!get_magic_quotes_gpc ()) {
	$_GET = sec ( $_GET );
	$_POST = sec ( $_POST );
	$_COOKIE = sec ( $_COOKIE );
	$_FILES = sec ( $_FILES );
}

function xtrim($str,$key=''){
	if(is_string ( $str )){
		$str=($key=='')?trim($str):trim($str,$key);
		return RemoveXSS($str);
	}
}

function sec(&$array) { 
	//如果是数组，遍历数组，递归调用
	if (is_array ( $array )) {
		foreach ( $array as $k => $v ) {
		$array [$k] = sec ( $v );
		} 
	} else if (is_string ( $array )) {
	//使用addslashes函数来处理
	    $array = addslashes ( $array );	
		$array = stripSql( $array );
	} else if (is_numeric ( $array )) {
	    $array = intval ( $array );
	}
	return $array;
}

/**
 *  修复浏览器XSS hack的函数
 *
 * @param     string   $val  需要处理的内容
 * @return    string
 */
function RemoveXSS($val) {
	$val = preg_replace('/([\x00-\x08|\x0b-\x0c|\x0e-\x19])/', '', $val);
	$search = 'abcdefghijklmnopqrstuvwxyz';
	$search .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$search .= '1234567890!@#$%^&*()';
	$search .= '~`";:?+/={}[]-_|\'\\';
	for ($i = 0; $i < strlen($search); $i++) {
	   $val = preg_replace('/(&#[xX]0{0,8}'.dechex(ord($search[$i])).';?)/i', $search[$i], $val); // with a ;
	   $val = preg_replace('/(&#0{0,8}'.ord($search[$i]).';?)/', $search[$i], $val); // with a ;
	}

	$ra1 = array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', 'style', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound', 'title', 'base');
	$ra2 = array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');
	$ra = array_merge($ra1, $ra2);

	$found = true; 
	while ($found == true) {
	   $val_before = $val;
	   for ($i = 0; $i < sizeof($ra); $i++) {
		  $pattern = '/';
		  for ($j = 0; $j < strlen($ra[$i]); $j++) {
			 if ($j > 0) {
				$pattern .= '(';
				$pattern .= '(&#[xX]0{0,8}([9ab]);)';
				$pattern .= '|';
				$pattern .= '|(&#0{0,8}([9|10|13]);)';
				$pattern .= ')*';
			 }
			 $pattern .= $ra[$i][$j];
		  }
		  $pattern .= '/i';
		  $replacement = substr($ra[$i], 0, 2).'<x>'.substr($ra[$i], 2);
		  $val = preg_replace($pattern, $replacement, $val); 
		  if ($val_before == $val) {
			 $found = false;
		  }
	   }
	}
	return $val;
}

/**
 *  过滤SQL关键字函数
 */
function stripSql($str){
	$sqlkey = array(	 //SQL过滤关键字
	    "/\\\'/",
		//'/\s*select\s*/i',
		'/\s*delete\s*/i',
		'/\s*update\s*/i',
		'/\s*drop\s*/i',
		//'/\s*or(?!der)\s*/i',//排除order
		'/\s*union\s*/i',
		'/\s*outfile\s*/i',
		'/\s*script\s*/i'
	);
	$replace = array(  //和上面数组内容对应
		'',
		//'&nbspselect&nbsp;',
		'&nbsp;delete&nbsp;',
		'&nbsp;update&nbsp;',
		'&nbsp;drop&nbsp;',
		//'&nbsp;or&nbsp;',
		'&nbsp;union&nbsp;',
		'&nbsp;outfile&nbsp;',
		'&nbsp;script&nbsp;'
	);
	if(!is_array($str)){
		//如果不是数组直接替换
		$str = preg_replace($sqlkey,$replace,$str); 
		return $str;
	}else{
		//如果是数组
		$new_str = array();
		foreach($str as $k=>$v){
			//遍历整个数组进行替换
			$new_str[$k] = stripSql($v);
		}
		unset($sqlkey,$replace);
		return $new_str;
	}
}

//获得当前的脚本网址 
function getCurUrl() 
{
  if(!empty($_SERVER["REQUEST_URI"])) 
  {
    $scriptName = $_SERVER["REQUEST_URI"];
    $nowurl = $scriptName;
  } else
  {
    $scriptName = $_SERVER["PHP_SELF"];
    if(empty($_SERVER["QUERY_STRING"])) 
    {
      $nowurl = $scriptName;
    } else
    {
      $nowurl = $scriptName."?".$_SERVER["QUERY_STRING"];
    }
  }
  return $nowurl;
}
 
/* 审核状态显示 */  
function chkState($num,$font=1) {
    if($num==-2){$v='<font color=grey>不通过</font>';}
	if($num==-1){$v='<font color=orange>退回修改</font>';}
	if($num==0){$v='<font color=red>未审核</font>';}
	if($num==1){$v='<font color=blue>已审核</font>';}
	if($num==2){$v='<font color=green>已置顶</font>';}
	$v=($font)?$v:strip_tags($v);
	return $v;
}

/* 咨询状态显示 */  
function chkZxState($num) {	
    if($num==0){$v='<font color=red>未查看</font>';}
	if($num==1){$v='<font color=blue>已查看</font>';}
	if($num==2){$v='<font color=green>已回复</font>';}
	if($num==3){$v='<font color=gray>已查看回复</font>';}
	return $v;
}

/* 审核是否 */  
function chkIsYes($st,$font=1) {
    if($st==0){$v='<font color=red>否</font>';}
	if($st==1){$v='<font color=blue>是</font>';}
	$v=($font)?$v:strip_tags($v);
	return $v;
}

/* 格式化时间 */ 
function formatdate($sj){
    if(!empty($sj)){
	   return @date('Y-m-d',strtotime($sj));
	}
}
 
/* 通用alert提示 */  
function htmlendjs($msg,$act='',$url='') {
	switch($act){
		case '':case 'back':
		    $str="history.back();";
			$str=($msg)?"layer.alert('$msg',{icon: 0},function(){ $str });":$str;
			break;
		case 'close':
		    $str="window.close();";
		    $str=($msg)?"layer.alert('$msg',{icon: 2},function(){ $str });":$str;
			break;			
		case 'open': case 'opener':
		    $str="if(self == top && window.opener){window.opener.location.reload();window.close();}else{window.location='".$_SERVER['HTTP_REFERER']."';}";
			$str2=($act=='opener')?'var obj=(self == top && window.opener)?window.opener:self;':'var obj=self;';//修正批量删除提示
			$str=($msg)?$str2."obj.layer.alert('$msg',{icon: 1},function(){ $str });":$str;
			break; 
		case 'dialog-open':
		    $str="var obj=parent.window.frames['ifr']||parent; var index=parent.layer.getFrameIndex(window.name);";
			$str.="if(index!=null){ obj.location.reload();parent.layer.closeAll(); }else{ window.location='".$_SERVER['HTTP_REFERER']."'; }";
			$str=($msg)?"parent.layer.alert('$msg',{icon: 1},function(){ $str });":$str;
			break;			
		case 'confirm':
		    $str="layer.confirm('$msg', { icon: 3, btn: ['是','否'] },function(){ self.location=document.referrer; },function(){  window.location='$url'; });";
			break;							
		default:
		    $str="window.location='$act';";
			$str=($msg)?"layer.alert('$msg',{icon: 1},function(){ $str });":$str;
	}
    $str="<!DOCTYPE html><html><head><meta charset='utf-8'><meta name='viewport' content='width=device-width, initial-scale=1'><script type='text/javascript' src='".ROOT_PATH."Include/jquery-1.8.3.min.js'></script><script type='text/javascript' src='".ROOT_PATH."layer/layer.js'></script></head><body><script>$str</script></body></html>";
	echo $str;
	exit();	
}


//获取nclass表数组，配合tree树形函数(slef:是否包含$classid本身,$zdname:扩展字段，逗号分开)
function getClassArray($tablename,$classid=0,$slef=0,$zdname=''){
	global $db;
	$class_arr=array();
	$sql = "select * from $tablename where classid>0";
	$sql.=(!empty($classid))?" and locate(',$classid,',classpath)>0":'';
	$sql.=(!$slef)?" and classid<>$classid":'';
	$sql.=" order by locate(','+classid+',',classpath),orderid";
	//$sql.=" order by classid";
	if(!empty($zdname)){ $myarray=explode(',',$zdname); }

	$query = $db -> query($sql);
	while($row = $db -> fetch_array($query)){
		$class_arr1 = array('classid'=>$row['classid'],'classname'=>$row['classname'],'parentid'=>$row['parentid'],'orderid'=>$row['orderid']);
		if(count($myarray)>0){
			foreach($myarray as $v){
			$class_arr2[$v] = $row[$v];
			}
		}
		$class_arr[$row['classid']] =array_merge((array)$class_arr1,(array)$class_arr2);
	}
	return makeTree($class_arr);
}
//使用引用-无限极分类生成tree树形数组
function makeTree($class_arr){
	$tree = array();
	foreach($class_arr as $key=>$node){
        if(isset($class_arr[$node['parentid']])){
            $class_arr[$node['parentid']]['son'][] = &$class_arr[$key];
        }else{
            $tree[] = &$class_arr[$node['classid']];
        }
	}
	return $tree;
}
//显示树结构
function tree($class_arr,$classid=0,$level=0){
	$n = str_pad('',$level,'-',STR_PAD_RIGHT);
	$n = str_replace("-","│&nbsp;&nbsp;",$n);
    foreach($class_arr as $key=>$value){
		$str.="<option value='".$value['classid']."'";
		$str.=($value['classid']==$classid)?' selected':'';
		$str.=">".$n."├&nbsp;&nbsp;".$value['classname']."</option>";
        if(!empty($value['son'])){
            $str.=tree($value['son'],$classid,$level+1);
        }
    }
	return $str;
}
 
/*搜索结果加亮 */ 
function replaceKey($key,$text){
	$keys = explode(' ', $key);
	foreach($keys as $v){
		if(preg_match('/'.$v.'/iSU', $text)){
			$text = str_replace($v, '<font color="#FF0000">'.$v.'</font>', $text);
		}
	}
	return $text;
}

/* 搜索获取纯文本 */
function html2text($str){
	$str = strip_tags($str);
	$str = preg_replace('/\s/','',$str);
	$str = str_replace('&nbsp;','', $str);
	$str = str_replace('&emsp;','',$str);
	$str = str_replace(' ','',$str);
	return $str;
}


/**
 * 将实体<br>转换为\n
 */
function htmldecode($str){
	$str = str_replace('<br />',"\n",$str);
	$str = str_replace('<br>',"\n",$str);
	$str = str_replace('&nbsp;'," ",$str);
	$str = str_replace('&lt','<',$str);
	$str = str_replace('&gt','>',$str);
	return $str;

}

/*
 * 转换HTML标签
 */
function htmlcode($str){
	if(!is_array($str)){
		$str = str_replace(' ', '&nbsp;', $str);
		$str = str_replace('<', '&lt', $str);
		$str = str_replace('>', '&gt', $str);
		$str = str_replace("\n", '<br />', $str);
		return $str;
	}else{
		foreach($str as $k=>$v){
			$new_str[$k] = htmlcode($v);
		}
		return $new_str;
	}
}

/**
 * 字符串截取
 */
function gottopic($String, $Length,$act = true) {
	if (mb_strwidth($String, 'UTF8') <= $Length) {
    //  if (mb_strwidth($String) <= $Length) {
		return $String;
	} else {
		$I = 0;
		$len_word = 0;
		while ($len_word < $Length) {
			$StringTMP = substr($String, $I, 1);
			if (ord($StringTMP) >= 224) {
				$StringTMP = substr($String, $I, 3);
				$I = $I +3;
				$len_word = $len_word +2;
			}
			elseif (ord($StringTMP) >= 192) {
				$StringTMP = substr($String, $I, 2);
				$I = $I +2;
				$len_word = $len_word +2;
			} else {
				$I = $I +1;
				$len_word = $len_word +1;
			}
			$StringLast[] = $StringTMP;
		}
		/* raywang edit it for dirk for (es/index.php)*/
		if (is_array($StringLast) && !empty ($StringLast)) {
			$StringLast = implode("", $StringLast);
			if($act){
				$StringLast .= "...";
			}
		}
		return $StringLast;
	}
}

/**
 * 新闻列表显示
 */
function NewsClass($classid,$num=8,$len,$istime=1,$tbname='g_news',$pre='article'){
	global $db;	
	if($classid){
		$str='<ul class="list">';
		$sql="select id,title,addtime from $tbname where states>0 and classid=$classid order by states desc,id desc limit $num";
		$rs = $db -> query($sql);
		while ($row=$db->fetch_array($rs)) {
			$str.="<li><a href='$pre-$row[id].html'>".gottopic($row['title'],$len)."</a>";
			if ($istime) {
				$str.='<span>['.formatdate($row['addtime']).']</span>';
			}
			$str.='</li>';
		}
		$str.='</ul>';
		return $str;
	}
	else{
	    return '';	
	}
}

//自动获取GET变量,$no:排除参数(多参数逗号分开)
function getQueryStr($no){
	foreach($_GET as $k=>$v){
		if(strpos(','.$no.',' , ','.$k.',')===false){		
			if($v!=''){
				$v=(is_string($v))?xtrim($v):$v;
				$str.=$k.'='.$v.'&';
			} 
		}  
	}
	return $str;
}

//默认页面变量
$page = $_GET['page'];

//$totle：信息总数；
//$pagesize：每页显示信息数，这里设置为默认是20；
//$url：分页导航中的链接参数(除page)
function TurnPage($totle,$pagesize=20,$url='auto',$halfPer=5){
	
	global $page,$offset,$pagenav;	
	//$GLOBALS["pagesize"]=$pagesize;
	
	$totalpage=ceil($totle/$pagesize); //最后页，也是总页数
	//$page=min($totalpage,$page);
	if(!isset($page)||$page<1) $page=1;
	
	$prepage=$page-1; //上一页
	$nextpage=$page+1; //下一页
	$offset=($page-1)*$pagesize;//偏移量
	if($totalpage<=1) return false;
	
	//自动获取GET变量
	if($url=='auto'){
		$url=getQueryStr('page');//排除page
	}	
	//获取当前文件名
	//$filename_arr=explode('/',$_SERVER['PHP_SELF']);$filename = end($filename_arr); 
	
	//开始分页导航条代码：
	$pagenav="显示第 <B>".max($offset+1,1)."</B>-<B>".min($offset+$pagesize,$totle)."</B> 条记录，共 <B>$totle</B> 条记录";
	
	$pagenav.=" <a href=$filename?$url"."page=1>首页</a> ";
	if($prepage>0) $pagenav.=" <a href=$filename?$url"."page=$prepage>上页</a> "; 
	
	for ( $i = $page - $halfPer,$i > 0 || $i = 1 , $j = $page + $halfPer, $j <= $totalpage || $j = $totalpage;$i <= $j ;$i++ )
	{
		$pagenav .= $i == $page 
			? " <span class='current'>$i</span>" 
			: " <a href=$filename?$url"."page=$i>$i</a>";
	}
	
	if($nextpage<$totalpage) $pagenav.=" <a href=$filename?$url"."page=$nextpage>下页</a> "; 
	$pagenav.=" <a href=$filename?$url"."page=$totalpage>尾页</a> ";
	
	//下拉跳转列表，循环列出所有页码：
	$pagenav.="　到第 <select name='topage' size='1' onchange='window.location=\"$filename?$url"."page=\"+this.value'>\n";
	for($i=1;$i<=$totalpage;$i++){
	  if($i==$page){
		  $pagenav.="<option value='$i' selected>$i</option>\n";}
	  else{
		  $pagenav.="<option value='$i'>$i</option>\n"; 
	   } 
	}
	$pagenav.="</select> 页，共 <B>$totalpage</B> 页";
}

/**
 * 建立请求，以表单HTML形式构造（默认）
 * @param $para 请求参数数组
 * @param $method 提交方式。两个值可选：post、get
 * @return 提交表单HTML文本
 */
function buildRequestForm($action,$para,$method) {		
	$sHtml = "<form id='autosubmit' name='autosubmit' action='".$action."' method='".$method."'>";
	while (list ($key, $val) = each ($para)) {
		$sHtml.= "<input type='hidden' name='".$key."' value='".$val."'/>";
	}
	$sHtml = $sHtml."</form>";
	
	$sHtml = $sHtml."<script>document.forms['autosubmit'].submit();</script>";
	return $sHtml;	
}

function file_get_contents_post($url, $post) {  
    $options = array(  
        'http' => array(  
            'method' => 'POST',  
            // 'content' => 'name=gongjun&email=hmgj940@sohu.com',  
            'content' => http_build_query($post),  
        ),  
    ); 
    $result = file_get_contents($url, false, stream_context_create($options));  
    return $result;  
}

 
/* 获得当前classid的上级父id */  
function getParentId($tablename,$classid) {
	global $db;
	$v=false;
	if(!empty($classid)){
		 $v=$db->get_one("select parentid from $tablename where classid=$classid");
	}
	return $v;
}

/* 获得当前classid的类别名称 */  
function getClassName($tablename,$classid,$zdname='classid') {
	global $db;
	$v=false;
	if(!empty($classid)){	
	     
		 $v=$db->get_one("select classname from $tablename where $zdname='$classid' limit 1");
	}
	return $v;
}	

/* 获得当前classpath的路径,classpath以逗号分开 */  
function getClassPath($tablename,$classpath,$line=',',$zdname='classid') {
	global $db;
	$str='';
	if(!empty($classpath)){
		 $myarray=explode(',',$classpath);
		 foreach($myarray as $value){
		      if(!empty($value)){
			  $str.=getClassName($tablename,$value,$zdname).$line;
			  }
		 } 
	}
	//return $str;
	return ($str)?substr($str,0,strlen($str)-strlen($line)):'';
}

/* 获得当前code的路径 */ 
function getCodePath($tablename,$code,$line=',') {
	global $db;
	$str='';
	if(!empty($code)){	
	      $classpath=$db->get_one("select classpath from $tablename where code='$code' limit 1");
		  $str=getClassPath($tablename,$classpath,$line);
	}
	return $str;
}

/* 获得用户名 */  
function getUserName($userid) {
    global $db;
	$str='';
	if(!empty($userid)){	
		 $str=$db->get_one("select username from user_info where userid=$userid");
	}
	return $str;
}

/* 获得用户组名称 */  
function getGroupName($groupid) {
    global $db;
	$str='';
	if(!empty($groupid)){	
		 $str=$db->get_one("select groupname from user_group where groupid=$groupid");
	}
	return $str;
}
function getGroupOption($groupid){
	global $db;
	$sql = "select * from user_group where groupid not in(6,7) order by orderid";
	$query = $db -> query($sql);
	while($row = $db -> fetch_array($query)){
		$str.="<option value='".$row['groupid']."'";
		if($row['groupid']==(int)$groupid){ $str.=' selected'; }
		$str.='>'.$row['groupname'].'</option>';
	}
	echo $str;
}

/*
inputName:控件名称 
defaultValue:默认值
defaultName:默认名称*/
function isyes($inputName,$defaultValue=NULL,$defaultArray=array('是'=>1,'否'=>0)){
	foreach($defaultArray as $k=>$v){
		   $nstr='';
		   if($v==$defaultValue){
			  $nstr=' checked'; 
		   }
		   $str.="<input type='radio' name='$inputName' id='$inputName$v' value='$v'$nstr><label for='$inputName$v'>$k</label>&nbsp;";
	}
	return $str;
}

/*inputType:radio/checkbox/select 
inputName:控件名称 
id:所属大类id
defaultValue:默认值(多值用","分隔)
zdname:返回字段*/
function selectBox($tableName,$inputType,$inputName,$id,$defaultValue=NULL,$zdName='classid'){
    global $db;
	$classname=getClassName($tableName,$id);
	$sql="select * from $tableName where parentid=$id order by orderid";
	$query=$db->query($sql);
	$j=1;
	$totals=$db->num_rows($query);
	while($row=$db->fetch_array($query)){
	    if(strtolower($inputType)=='radio' || strtolower($inputType)=='checkbox'){
			$flag=false;
			$arr=explode(',',$defaultValue);
			foreach($arr as $v){
			    if(!empty($v)){
				    if($v==$row[$zdName]){
					     $flag=true;
						 break;
					}
				}
			}
			$ctr=(strtolower($inputType)=='checkbox')?'[]':'';
			$mstr=($flag==true)?' checked':'';
			$nstr=($j==$totals)?" dataType='Group' msg='未选择'.$j":'';
		    $str.="<input type='".strtolower($inputType)."' name='".$inputName.$ctr."' id='".$inputName.$j."' value='".$row[$zdName]."'$mstr$nstr/><label for='".$inputName.$j."'>".$row['classname']."</label>&nbsp;";
		}
	    if(strtolower($inputType)=='select'){
			if(!empty($defaultValue)){
			   if($row[$zdName]==$defaultValue){
			      $mstr=' selected';
			   }
			}
		    $str.="<option value='".$row[$zdName]."'$mstr>".$row['classname']."</option>";
		}
		$ctr=$mstr=$nstr='';
		$j++;
	}
	
	if(strtolower($inputType)=="select"){
	     $str2="<select name='".$inputName."' dataType='Require' msg='未选择'><option value=''>— 请选择 —</option>".$str."</select>";
	}else{
	     $str2=$str;
	}
	return $str2;
}


/*'id:所属大类id
'defaultValue:默认值(多值用","分隔)*/
function readCheckBox($tableName,$id,$defaultValue,$zdName='classid'){
    global $db;
	$sql="select * from $tableName where parentid=$id order by orderid";
	$query=$db->query($sql);
	while($row=$db->fetch_array($query)){
			$flag=false;
			$arr=explode(',',$defaultValue);
			foreach($arr as $v){
			    if(!empty($v)){
				    if($v==$row[$zdName]){
					     $flag=true;
						 break;
					}
				}
			}
			$mstr=($flag==true)?'checked':'';
			$str.="<input type=checkbox name=s value='".$row[$zdName]."' $mstr onclick='return false'/>".$row['classname']."&nbsp;";
	}
	return $str;
}


//广告位显示
function myAd($width,$posid,$xgid,$states=1){
	global $db;
	$sql="select * from g_gg where posid=$posid and xgid=$xgid";
	$sql.=($states)?' and states>0':'';
	$sql.=" order by states desc,orderid,id";
	$query=$db->query($sql);
	if($db->num_rows($query)>0){
		$str=($width>0)?"<div style='width:".$width."px' id='ad".$posid."_".$xgid."' class='{class}'><ul>":'';
		while($row=$db->fetch_array($query)){
			if($row['classid']==6){
				$str.="<li>";
				if(!empty($row['url'])){
					$str.="<a href='".ROOT_PATH."ad.php?id=".$row['id']."' target='_blank'><img src='".ROOT_PATH."uploadfile/logo/".$row['code']."' width='".$row['width']."' height='".$row['height']."' border=0/></a>";
				}else{
					$str.="<img src='".ROOT_PATH."uploadfile/logo/".$row['code']."' width='".$row['width']."' height='".$row['height']."' border=0/>";
				}
				$str.="</li>";
			}
		
			if($row['classid']==7){
				$str.="<li><object classid='clsid:D27CDB6E-AE6D-11cf-96B8-444553540000' codebase='http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,28,0' width='".$row['width']."' height='".$row['height']."'><param name='movie' value='".ROOT_PATH."uploadfile/ad/".$row['code']."' /><param name='quality' value='high'/><embed src='".ROOT_PATH."uploadfile/ad/".$row['code']."' quality='high' pluginspage='http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash' type='application/x-shockwave-flash' width='".$row['width']."' height='".$row['height']."'></embed></object></li>";
			}
			
			if($row['classid']==8){ $str.=$row['code']; }				
		}		
		$str.="</ul><p><a href=# onclick=\"$('#ad".$posid."_".$xgid."').remove()\">关闭</a></p></div>";
	}
	//占位
	if($xgid==9){ $str=str_replace("{class}","ad",$str);}
	//左联
	if($xgid==10){ $str=str_replace("{class}","ad ad-left",$str);}
	//右联
	if($xgid==11){ $str=str_replace("{class}","ad ad-right",$str);}
	//漂浮
	if($xgid==12){ $str=str_replace("{class}","ad",$str)&"<script src='".ROOT_PATH."include/floatAd.js' type='text/javascript'></script><script>$('#ad".$posid."_".$xgid."').floatAd({top:30,left:20});</script>";}	
	return $str;			
}

//发送在线消息
function sendUserMsg($fromuserid,$touserid,$title,$content){
	global $db;
	return $db->query("insert into user_msg(fromuserid,touserid,title,content,isdisp,islooked,addtime) values($fromuserid,$touserid,'$title','$content',0,0,now())");
}


//用户组验证 groupid:'1,2,3'
function chk_admin($groupid){
    if(chkqx($groupid)==0){
	    echo "没有权限";
	    exit();
	}
}
function chkqx($qx,$groupid=0){
	$x=0;
	if($groupid==0){ $groupid=$_SESSION['groupid']; }//默认session取代0
    if(strpos(','.$qx.',' , ','.$groupid.',')!==false){
	    $x=1;
	}
	return $x;
}


//验证id是否属于当前用户(表字段含用户ID)
function chkUserId($tablename,$id,$user_zdname='userid',$id_zdname='id'){
    global $db;
	$userid=$db->get_one("select $user_zdname from $tablename where $id_zdname=".$id);
	//return $userid;
	if($userid==$_SESSION['userid']){
	    return true;
	}else{
	    return false;
	}
}
//验证用户操作ID权限(表字段含用户ID)
function chk_user($tablename,$id,$user_zdname='userid',$id_zdname='id'){
	//验证id是否属于当前用户(管理员除外)
	if(!chkUserId($tablename,$id,$user_zdname,$id_zdname) && chkqx('1')==0){
		exit('没有权限');
	}else{
	    return true;
	}
}

//判断有效期
function chkValidDate($fromtime,$totime){
    $nowtime=date("Y-m-d");
	if(!strtotime($totime)){
	    $str='长期有效';
    }else{
		if(strtotime($nowtime) > strtotime($totime)){
			$str='<font color=red>已过期</font>';
		}else{
		    $str=$fromtime.' 至 '.$totime;
		}
	}
	return $str;
}


/**
* 对变量进行 JSON 编码
* @param mixed value 待编码的 value ，除了resource 类型之外，可以为任何数据类型，该函数只能接受 UTF-8 编码的数据
* @return string 返回 value 值的 JSON 形式
*/
function json_encode_ex($value){
	 if(version_compare(PHP_VERSION,'5.4.0','<')){
		  $str = json_encode($value);
		  $str = preg_replace_callback(
			   "#\\\u([0-9a-f]{4})#i",
			   function($matchs){
				 return iconv('UCS-2BE', 'UTF-8', pack('H4', $matchs[1]));
			   },
				$str
		  );
		  return $str;
	 }else{
		  return json_encode($value, JSON_UNESCAPED_UNICODE);
	 }
}

//重组数组获取json字符串
//$arr=array('filename'=>$_POST['filename'],'fileurl'=>$_POST['fileurl']);
function makejsonStr($arr){
	$newarr=array();
	if(empty($arr) || !is_array($arr)){ return ''; }
	if(count($arr) == count($arr, 1)) {
		$newarr=$arr;  //一维数组
	}else{
		foreach(current($arr) as $m=>$n ){
			if(!empty($n)){
			foreach(array_keys($arr)as $k){
				$arr_key[$k]=$arr[$k][$m];
			}
			$newarr[]=$arr_key;
			}
		}
	}
	if(!empty($newarr)){ $str=json_encode_ex($newarr); } 
	return $str;
}

//发送Email函数 参数说明(发送到, 邮件主题, 邮件内容, 用户名)
function smtp_mail ($sendto_email, $subject, $body, $user_name) { 
	$mail = new PHPMailer(); 
	$mail->IsSMTP(); // send via SMTP 
	$mail->Host = "mail.jspc.org.cn"; // SMTP servers 
	$mail->SMTPAuth = true; // turn on SMTP authentication 
	$mail->Username = "jcpark"; // SMTP username 注意：普通邮件认证不需要加 @域名 
	$mail->Password = "abc85485869"; // SMTP password 
	
	$mail->From = "jcpark@jspc.org.cn"; // 发件人邮箱 
	$mail->FromName = "金册网"; // 发件人 ,比如金册网
	
	$mail->CharSet = "UTF-8"; // 这里指定字符集！ 
	$mail->Encoding = "base64"; 
	
	$mail->AddAddress($sendto_email,$user_name); // 收件人邮箱和姓名 
	$mail->AddReplyTo("","金册网"); 
	
	//$mail->WordWrap = 50; // set word wrap 
	//$mail->AddAttachment("/var/tmp/file.tar.gz"); // attachment 附件1
	//$mail->AddAttachment("/tmp/image.jpg", "new.jpg"); //附件2
	$mail->IsHTML(true); // send as HTML 
	$mail->Subject = $subject; 
	
	// 邮件内容 可以直接发送html文件
	$mail->Body = $body;
	$mail->AltBody ="text/html"; 
	if($mail->Send()) { 
	   $result=1;
	} else { 
	   $result="$user_name 失败,错误信息$mail->ErrorInfo";
	} 
	return $result;
}
?>