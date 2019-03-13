var Common = new Object();
Common.trim = function(str){
	return str.replace(/(^\s*)|(\s*$)/g, "");
}

Common.strlen = function (str){
/*	var Charset = jQuery.browser.msie ?document.charset : document.characterSet
	if(Charset.toLowerCase() == 'utf-8'){
		return str.replace(/[\u4e00-\u9fa5]/g, "***").length;
	} else {
		return str.replace(/[^\x00-\xff]/g, "**").length;
	}*/
	return str.length;//同步validator.js长度20151022
}

validator={
	errinput : 'errinput',
	errmsg : 'errmsg',
	errcls : 'no',
	yescls : 'yes',
	errorTip : 'errorTip',
	errorInput : 'errorInput',
	validTip   : 'validTip',

	Require : /.+/,	
	Email : /^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/,
	Phone : /^((\(\d{2,3}\))|(\d{3}\-))?(\(0\d{2,3}\)|0\d{2,3}-)?[1-9]\d{6,7}(\-\d{1,4})?$/,
	/*Mobile : /^((\(\d{3}\))|(\d{3}\-))?13[0-9]\d{8}?$|15[89]\d{8}?$/,*/
	Mobile : /^1[3|4|5|7|8]\d{9}$/,
	Url : /^http:\/\/[A-Za-z0-9]+\.[A-Za-z0-9]+[\/=\?%\-&_~`@[\]\':+!]*([^<>\"\"])*$/,
	IdCard : "this.isIdCard(value)",
	Currency : /^\d+(\.\d+)?$/,
	Number : /^\d+$/,
	Zip : /^[1-9]\d{5}$/,
	ip  : /^[\d\.]{7,15}$/,
	QQ : /^[1-9]\d{4,8}$/,
	Integer : /^[-\+]?\d+$/,
	Double : /^[-\+]?\d+(\.\d+)?$/,
	English : /^[A-Za-z]+$/,
	Chinese : /^[\u0391-\uFFE5]+$/,
	/*UserName : /^[A-Za-z0-9_]{3,}$/i,*/
	UserName : /^[a-zA-Z\u4e00-\u9fa5][\u0391-\uFFE5\w]{1,}$/i,       //以字母或中文开头至少2位
	//unSafe : /^(([A-Z]*|[a-z]*|\d*|[-_\~!@#\$%\^&\*\.\(\)\[\]\{\}<>\?\\\/\'\"]*)|.{0,5})$|\s/,
	UnSafe : /[<>\?\#\$\*\&;\\\/\[\]\{\}=\(\)\.\^%,]/,
	//safeStr : /[^#\'\"~\.\*\$&;\\\/\|]/,
	IsSafe : function(str){return !this.UnSafe.test(str);},
	SafeString : "this.isSafe(value)",
	Filter : "this.doFilter(value)",
	Limit : "this.checkLimit(Common.strlen(value))",
	LimitB : "this.checkLimit(this.LenB(value))",
	Date : "this.isDate(value)",
	Repeat : "this.checkRepeat(value)",
	Range : "this.checkRange(value)",
	Compare : "this.checkCompare(value)",
	Custom : "this.Exec(value)",
	Group : "this.mustChecked()",
	Ajax: "this.doajax(errindex)",

	isIdCard : function(number){
	var date, Ai;
	var verify = "10x98765432";
	var Wi = [7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2];
	var area = ['','','','','','','','','','','','北京','天津','河北','山西','内蒙古','','','','','','辽宁','吉林','黑龙江','','','','','','','','上海','江苏','浙江','安微','福建','江西','山东','','','','河南','湖北','湖南','广东','广西','海南','','','','重庆','四川','贵州','云南','西藏','','','','','','','陕西','甘肃','青海','宁夏','新疆','','','','','','台湾','','','','','','','','','','香港','澳门','','','','','','','','','国外'];

	var re = number.match(/^(\d{2})\d{4}(((\d{2})(\d{2})(\d{2})(\d{3}))|((\d{4})(\d{2})(\d{2})(\d{3}[x\d])))$/i);
	if(re == null) return false;
	if(re[1] >= area.length || area[re[1]] == "") return false;
	if(re[2].length == 12){
		Ai = number.substr(0, 17);
		date = [re[9], re[10], re[11]].join("-");
	} else {
		Ai = number.substr(0, 6) + "19" + number.substr(6);
		date = ["19" + re[4], re[5], re[6]].join("-");
	}
	if(!this.isDate(date, "ymd")) return false;
	var sum = 0;
	for(var i = 0;i<=16;i++){
		sum += Ai.charAt(i) * Wi[i];
	}
	Ai += verify.charAt(sum%11);

	return (number.length ==15 || number.length == 18 && number == Ai);
	},

	isDate : function(op){
		var formatString = this['element'].attr('format');
		formatString = formatString || "ymd";
		var m, year, month, day;
		switch(formatString){
		case "ymd" :
			m = op.match(new RegExp("^((\\d{4})|(\\d{2}))([-./])(\\d{1,2})\\4(\\d{1,2})$"));
			if(m == null ) return false;
			day = m[6];
			month = m[5]*1;
			year = (m[2].length == 4) ? m[2] : GetFullYear(parseInt(m[3], 10));
		break;
		case "dmy" :
			m = op.match(new RegExp("^(\\d{1,2})([-./])(\\d{1,2})\\2((\\d{4})|(\\d{2}))$"));
			if(m == null ) return false;
			day = m[1];
			month = m[3]*1;
			year = (m[5].length == 4) ? m[5] : GetFullYear(parseInt(m[6], 10));
		break;
		default :
			break;
		}
		if(!parseInt(month)) return false;
		month = month==0 ?12:month;
		var date = new Date(year, month-1, day);
		return (typeof(date) == "object" && year == date.getFullYear() && month == (date.getMonth()+1) && day == date.getDate());
		function GetFullYear(y){
			return ((y<30 ? "20" : "19") + y)|0;
		}
	}, //end isDate
	doFilter : function(value){
		var filter =this['element'].attr('accept');
		return new RegExp("^.+\.(?=EXT)(EXT)$".replace(/EXT/g,filter.split(/\s*,\s*/).join("|")),"gi").test(value);
	},

	checkLimit:function(len){
		var minval=this['element'].attr('min') ||Number.MIN_VALUE;
		var maxval=this['element'].attr('max') ||Number.MAX_VALUE;
		return (minval<= len && len<=maxval);

	},

	LenB : function(str){
		return str.replace(/[^\x00-\xff]/g,"**").length;
	},

	checkRepeat:function(value){
		var to = this['element'].attr('to');
		return value==jQuery('input[name="'+to+'"]').eq(0).val();
	},

	checkRange : function(value){
		value = value|0;
		var minval=this['element'].attr('min') || Number.MIN_VALUE;
		var maxval=this['element'].attr('max') || Number.MAX_VALUE;
		return (minval<=value && value<=maxval);
	},

	checkCompare : function(value){
		var compare=this['element'].attr('compare');
		if(isNaN(value)) return false;
		value = parseInt(value);
		return eval(value+compare);
	},

	Exec : function(value){
		var reg = this['element'].attr('regexp');
		return new RegExp(reg,"gi").test(value);
	},

	mustChecked : function(){
		var tagName=this['element'].attr('name');
		var f=this['element'].parents('form');
		var n=f.find('input[name="'+tagName+'"]:checked').length;
		var count = f.find('input[name="'+tagName+'"]').length;
		var minval=this['element'].attr('min') || 1;
		var maxval=this['element'].attr('max') || count;
		return (minval<=n && n<=maxval);
	},

	doajax : function(value) {	
		var element = this['element'];
		var errindex = this['errindex'];
		var url=this['element'].attr('url');
		var msgid = jQuery('#'+element.attr('msgid'));
		var val = this['element'].val();
		var str_errmsg=this['element'].attr('msg');
		var arr_errmsg ;
		var errmsg ;
		if(str_errmsg.indexOf('|')>-1) {
      		arr_errmsg= str_errmsg.split('|') ;
      		errmsg = arr_errmsg[errindex] ;
		} else {
      		errmsg='';
		}
		var type=this['element'].attr('type');
/*		var Charset = jQuery.browser.msie ? document.charset : document.characterSet;
		var methodtype = (Charset.toLowerCase() == 'utf-8') ? 'post' : 'get';
		var method=this['element'].attr('method') || methodtype;*/
		var method=this['element'].attr('method') || 'post';
		var name = this['element'].attr('name');
		if(url=="" || url==undefined) {
		    alert('Please specify url');
		    return false ;
		}
		if(url.indexOf('?')>-1){
            url = url+"&zd="+name+"&data="+escape(val);
		} else {
            url = url+'?'+name+"="+escape(val);
		}
		validator.removeErr(this['element']);
		this['element'].parent().find('.'+validator.errorTip+',.'+validator.validTip).remove();
		
		var s = $.ajax({
			type: method,
			url: url.split('?')[0],
			data: url.split('?')[1],
			cache: false,
			async: false,
			success: function(data){
				   //alert(data);
				   data = data.replace(/(^\s*)|(\s*$)/g, "");
				   if(data != 'success'){
					  errmsg = errmsg=="" ? data : errmsg;
					  (type!='checkbox' && type!='radio' && element.addClass(validator.errorInput));
					  
						if(msgid.length>0){
							  msgid.removeClass(validator.validTip).addClass(validator.errorTip).html(errmsg);
						} else{
							  element.parent().find('.'+validator.errorTip+',.'+validator.validTip).remove();
							  jQuery("<span class='"+validator.errorTip+"'>&nbsp;&nbsp;&nbsp;</span>").html(errmsg).appendTo(element.parent());
						}
					  return false;
				   }
				   
				   if(data=='success') {
					   if(msgid.length>0){
                             msgid.removeClass(validator.errorTip).addClass(validator.validTip).html('&nbsp;&nbsp;&nbsp;');
                   } else {
					        element.parent().find('.'+validator.errorTip+',.'+validator.validTip).remove();
							jQuery("<span class='"+validator.validTip+"'>&nbsp;&nbsp;&nbsp;</span>").appendTo(element.parent());
						}
					   return true;
				   }
			   }
		 }).responseText;
		 s = s.replace(/(^\s*)|(\s*$)/g, "");
		 return s == 'success' ? true : false;
	}
};

// element 
validator.showErr=function (element, errindex){
	var str_errmsg=element.attr('msg') ||'unkonwn';
    
	var arr_errmsg = str_errmsg.split('|');
	var errmsg = arr_errmsg[errindex] ? arr_errmsg[errindex]: arr_errmsg[0];
	var msgid= jQuery('#'+element.attr('msgid'));
	var type=element.attr('type');
	(type!='checkbox' && type!='radio' && element.addClass(this['errorinput']));
	
	element.parent().find('.'+this['errorTip']).remove();
	
	if(msgid.length>0) {
    msgid.removeClass(this['validTip']).addClass(this['errorTip']).html(errmsg);
	} else {
	element.parent().find('.'+validator.errorTip+',.'+validator.validTip).remove();
    jQuery("<span class='"+this['errorTip']+"'>&nbsp;&nbsp;&nbsp;</span>").html(errmsg).appendTo(element.parent());
	}
	return false ;
}

validator.removeErr =  function(element){
	element.removeClass(this['errorInput']);
	element.parent().find('.'+this['errorTip']).remove();
}

validator.checkajax = function(element, datatype, errindex) {
	var value=jQuery.trim(element.val());
	this['element'] = element;
	this['errindex'] = errindex;
	validator.removeErr(element);
	return eval(this[datatype]);
}

validator.checkDatatype = function(element,datatype){
	var value=jQuery.trim(element.val());
	this['element'] = element;
	validator.removeErr(element);
	switch(datatype){
		case "IdCard" :
		case "Date" :
		case "Repeat" :
		case "Range" :
		case "Compare" :
		case "Custom" :
		case "Group" :
		case "Limit" :
		case "LimitB" :
		case "SafeString" :
		case "Filter" :
		//alert(eval(this[datatype]));
		return eval(this[datatype]);
		break;

		default:
			return this[datatype].test(value);
			break;
		}
}

validator.check=function(obj){
	var datatype = obj.attr('datatype');
	var value = jQuery.trim(obj.val());

	/*  20160322 修正raido与checkbox  */
	if(obj.attr('type')=='radio'){
		 var m=$('input[name="'+obj.attr('name')+'"]');
		 if(m.length>1){
		    datatype='Group'; 
			var msg_v=m.last().attr('msg');
		    obj.attr('msg',msg_v);
		 }	 
	}
	if(obj.attr('type')=='checkbox'){
		 //alert(obj.attr('require'));
		 if(obj.attr('require')=="false"){
		    obj.removeClass(validator.errorInput);
            obj.parent().find('.'+validator.errorTip+',.'+validator.validTip).remove();
	        return true;
		 }else{
		    var m=$('input[name="'+obj.attr('name')+'"]');
		    if(m.length>1){
			datatype='Group'; 
			var max_v=m.last().attr('max');
			var min_v=m.last().attr('min');
			var msg_v=m.last().attr('msg');
			obj.attr('max',max_v);
			obj.attr('min',min_v);
			obj.attr('msg',msg_v);
		    }	
		 }	 
	}
	/*  20160322 修正raido与checkbox  */
	
	if(typeof(datatype) == "undefined") return true;

	//if(obj.attr('require')!="true" && value=="") return true;
	/*  20141121 require非必填  */
	if(obj.attr('require')=="false" && value==""){
		obj.removeClass(validator.errorInput);
        obj.parent().find('.'+validator.errorTip+',.'+validator.validTip).remove();
	    return true;
	} 

	var datatypes = datatype.split('|');
	var isValid = true;

	jQuery.each(datatypes,function(index,type){	
		if(typeof(validator[type]) == "undefined") {
			   isValid = false;
			   return  false;
		}
		
		//ajax validate 
		if(type=='Ajax')   return isValid = validator.checkajax(obj, type, index);
		
		if(validator.checkDatatype(obj,type)==false){  //the form element validate failed
            obj.addClass(validator.errorInput);
			validator.showErr(obj, index);
			return isValid=false;
			
		} else { // validate success
			validator.showErr(obj, index);
       		obj.removeClass(validator.errorInput);
       		var msgid = jQuery('#'+obj.attr('msgid'));
			if(msgid.length>0) {
         		msgid.removeClass(validator.errorTip).addClass(validator.validTip).html('&nbsp;&nbsp;');
			} else {				
         		obj.parent().find('.'+validator.errorTip+',.'+validator.validTip).remove();
         		jQuery('<span class="'+validator.validTip+'">&nbsp;&nbsp;&nbsp;&nbsp;</span>').appendTo(obj.parent());
			}				
		}
	});
	return isValid;
}
  
jQuery.fn.checkForm = function(options){
	var form=jQuery(this);
	var items = form.find(':input');//修正radio/checkbox20151022
	//alert(elements.length);
	var defaults = {
        msg: ''
    };
	var settings = $.extend(defaults, options);
	
	items.on("blur change",function(index){
		return validator.check(jQuery(this));
	});
	form.submit(function(){
		var isValid = true;
		var errIndex= new Array();
		var n=0;
		var obj = this || event.srcElement;
		var count = obj.elements.length;

		//修正ajax内容验证20150706
		for(var i=0;i<count;i++){
			if(validator.check($(obj.elements[i]))==false){
			//alert($(obj.elements[i]).attr('datatype'));
			isValid  = false;
			errIndex[n++]=i;
			}
		};	

		if(isValid==false){
			items.eq(errIndex[0]).focus().select();
			return false;
		}
		//增加验证确认提示支持20150707
		if(options){
			 if(confirm(settings.msg)==false){
				 return false;
			 }
		}

		return isValid;
	});
}