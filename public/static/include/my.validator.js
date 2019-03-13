//jQuery精简版验证插件 
//made by gongjun 20160526 update20170816
;(function($){
   $.fn.validator=function(options){ 
	  var defaults = {
		  'msg': ''
	  };	  
	  var settings = $.extend(defaults, options); 
	  var obj=$("input,textarea,select",this);
	    

	  obj.bind("blur change",function(){
		  check($(this));
	  });	  

	 //checkbox,radio 
	 obj.each(function(){
	     if($(this).attr('datatype')=='Group'){
		     $('input[name=\"'+$(this).attr('name')+'\"]').attr('datatype','Group').unbind('blur');	
	     }
	 });
	 obj.parent().append("<span class='valid'></span>");//初始化span
		  	  	    
	  this.submit(function(){
		  var errno=0;
		  obj.each(function(index, element) {
			  if(check($(this))){ errno++; };
		  });
		  if(errno==0){
		     if(options){
				 if(confirm(settings.msg)==false){
					 return false;
				 }
		     }
		  }else{
		     return false; 
		  }
		  return true;
	  });
   }
})(jQuery);

function showMsg(obj,fn,index){
	var founderror=false;
	var msg=obj.attr('msg')||'';
	
	if(obj.attr('type')=='radio'||obj.attr('type')=='checkbox'){
		msg=$('input[name=\"'+obj.attr('name')+'\"]:last').attr('msg')||'';
		msg=(msg=='')?'未选择':msg;
		obj=obj.parent();//上级元素
		if(!fn){
			founderror=true;
			obj.children('.valid').html('<span class=errorTip>'+msg+'</span>');
		}else{
			obj.children('.valid').html('');
		}
	}else{
		msg=msg.split('|');//文本框多条件转为数组
	    //fn返回false时
		if(!fn){
			obj.css({'border':'1px solid #C00'});
			founderror=true;
			if(msg[index]){
			obj.next('.valid').html('<span class=errorTip>'+msg[index]+'</span>');
			}
		}else{
			obj.css({'border':'1px solid #CCC'});
			obj.next('.valid').html('');
		}
	}
	
	if(obj.attr('require')=='false' && obj.val()==''){
		founderror=false;
		obj.css({'border':'1px solid #CCC'});
		obj.next('.valid').html('');
	}
	return founderror;
}
		
function check(obj){
	var founderror=false;
	var str=obj.attr("datatype")||'';
	var arr=str.split('|');
	for(var i=0;i<arr.length;i++){
	     if(checkone(obj,arr[i],i)){ founderror=true; break;  }
	}
	return founderror;
}	
//index：数组索引
function checkone(obj,datatype,index){
	var founderror=false;
	var v=obj.val();
	
	switch(datatype){
	case 'Require':
        founderror=showMsg(obj,/.+/.test(v),index);
		break;
    case 'Number':
        founderror=showMsg(obj,/^\d+$/.test(v),index);
		break;	
	case 'Double':
	    founderror=showMsg(obj,/^[-\+]?\d+(\.\d+)?$/.test(v),index);
		break;
    case 'Username':
	    founderror=showMsg(obj,/^[a-zA-Z\u4e00-\u9fa5][\u0391-\uFFE5\w]{1,}$/.test(v),index);
		break;	
	case 'Email':
	    founderror=showMsg(obj,/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/.test(v),index);
		break;	
	case 'Mobile':
	    founderror=showMsg(obj,/^1[3|4|5|7|8]\d{9}$/.test(v),index);
		break;	
	case 'Url':
	    founderror=showMsg(obj,/^http:\/\/[A-Za-z0-9]+\.[A-Za-z0-9]+[\/=\?%\-&_~`@[\]\':+!]*([^<>\"\"])*$/.test(v),index);
		break;						
	case 'Limit':
	    var maxval=obj.attr("max")||Number.MAX_VALUE;
		var minval=obj.attr("min")||Number.MIN_VALUE;
		founderror=(minval<= v.length && v.length<=maxval);//为真
		founderror=showMsg(obj,founderror,index);
		break;	
	case 'Group':
	    v=$('input[name=\"'+obj.attr('name')+'\"]:checked').val();
		founderror=showMsg(obj,v,index);
		break;	
	case 'Repeat':
	    var to = obj.attr('to');
		founderror=(v!=''&&v==$('input[name="'+to+'"]').eq(0).val());//为真
		founderror=showMsg(obj,founderror,index);
		break;	
	case 'Range':
		v = v|0;
	    var maxval=obj.attr("max")||Number.MAX_VALUE;
		var minval=obj.attr("min")||Number.MIN_VALUE;
		founderror=(minval<=v && v<=maxval);//为真
		founderror=showMsg(obj,founderror,index);
		break;	
	case 'Custom':
		var reg = obj.attr('regexp');
		founderror=new RegExp(reg,"gi").test(v);//为真 修正参数名20180411
		founderror=showMsg(obj,founderror,index);
		break;			
	case 'Ajax':
	    var url=obj.attr('url')||'';
		var v=obj.val();
		if(url!='' && v!=''){
			var result=$.ajax({
				   type: "POST",
				   url: url.split('?')[0],
				   data: url.split('?')[1]+"&zd="+obj.attr('name')+"&data="+escape(v),
				   cache:false,
				   async: false,
			    }).responseText;
			if(result=='success'){
			     founderror=true;//为真
			}
		}
		founderror=showMsg(obj,founderror,index);	
		break;			
	}
	return 	founderror;	
}