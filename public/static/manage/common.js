//config的设置是全局的
var __ROOT__='/gongjun';

layui.config({
    base: __ROOT__+'/public/static/manage/' //假设这是你存放拓展模块的根目录
}).extend({ //设定模块别名
    mymod: 'mymod' //如果 mymod.js 是在根目录，也可以不用设定别名
});

//通用方法
layui.use(['layer','table','form','laydate'], function(){
    var $ = layui.jquery
        ,layer = layui.layer
		,table = layui.table
        ,laydate = layui.laydate
		,form = layui.form;
	
		//自定义添加tab
    $(".my-tab-btn").on("click",function(){
        top.addTab($(this), '<i class="layui-icon layui-icon-file"></i> '+$(this).attr('title'), $(this).attr('data-url'));
    })    
		
    //添加验证规则
    form.verify({
		//自定义用户名验证
		username: function (value, item) { //value：表单的值、item：表单的DOM对象
            if (!new RegExp("^[a-zA-Z0-9\u4e00-\u9fa5\\s·]+$").test(value)) {
                
                var attr = $(item).attr("lay-verify-msg-username");
                if (typeof attr !== typeof undefined && attr !== false) {
                    return attr;
                }
                
                attr = $(item).attr("lay-verify-msg");
                if (typeof attr !== typeof undefined && attr !== false) {
                    return attr;
                }
                item.focus();//自动聚焦到验证不通过的输入框或字段
                return "不匹配用户名规则";
            }
        }	
		//value：表单的值、item：表单的DOM对象
        ,password : function(value, item){
            if(value.length < 6){
                return "密码长度不能小于6位";
            }
	        if(value.length >20){
                return "密码长度最多20位";
            }	
        }
        ,confirmPwd : function(value, item){
            if(!new RegExp($("#userpwd").val()).test(value)){
                return "两次输入密码不一致，请重新输入！";
            }
		}
		//我们既支持上述函数式的方式，也支持下述数组的形式
		//数组的两个值分别代表：[正则匹配、匹配不符时的提示文字]
		,limit: [
			/^[\w\W]{1,200}$/
			,'200字以内'
		]

		,mustradio: function (value, item) { //单选按钮必选
            var va = $(item).find("input[type='radio']:checked").val();
            if (typeof (va) == "undefined") {
				return $(item).attr("lay-verify-msg");
            }
        }
        ,mustcheck: function (value, item) { //复选框必选  
            var va = $(item).find("input[type='checkbox']:checked").val();
            if (typeof (va) == "undefined") {
				return $(item).attr("lay-verify-msg");
            }
		}
		,required: function (value, item) { //value：表单的值、item：表单的DOM对象
            if (!(/[\S]+/.test(value))) {

                var attr = $(item).attr("lay-verify-msg-required");
                if (typeof attr !== typeof undefined && attr !== false && attr !== '') {
                    return attr;
                }

                attr = $(item).attr("lay-verify-msg");
                if (typeof attr !== typeof undefined && attr !== false && attr !== '') {
                    return attr;
                }
                return '必填项不能为空';
            }
		}
		,number: function (value, item) {
            if (!value || isNaN(value)) {
                var attr = $(item).attr("lay-verify-msg-number");
                if (typeof attr !== typeof undefined && attr !== false && attr !== '') {
                    return attr;
                }

                attr = $(item).attr("lay-verify-msg");
                if (typeof attr !== typeof undefined && attr !== false && attr !== '') {
                    return attr;
                }
                return '只能填写数字';
            }
        }		
    })	
	
	
	form.on('checkbox(chkAll)', function(data){
		 /* console.log(data.elem); //得到checkbox原始DOM对象
		  console.log(data.elem.checked); //是否被选中，true或者false
		  console.log(data.value); //复选框value值，也可以通过data.elem.value得到
		  console.log(data.othis); //得到美化后的DOM对象*/
		  //alert(data.elem.checked);
		  $("input[name='id[]']:checkbox").each(function(){ 
			  $(this).prop("checked", data.elem.checked); 
			  
		  });
		  form.render('checkbox'); 
	}); 
	
	form.on('select(chkstates)', function(data){
		  //alert($(data.elem).attr('data-id'));
		   var v = data.value;
		   if(v!=''){
			   $.ajax({
					type: "POST",
					url: 'chk',
					data: {id:$(data.elem).attr('data-id'),states:v},
					success: function(data){
						//alert(data);
						if(data>0){
							window.location.reload();	
						}else{
							layer.msg('审核重复或失败');
						}
					}
				});			   
		   }
	}); 
	
	
	window.del=function(obj,msg,url,id){
			var tr=$(obj).parent().parent();
			//alert(tr.text());
			layer.confirm('确定要删除“'+msg+'”吗？',{icon:3}, function(index){
				$.ajax({
					type: "POST",
					url: url,
					data: {id:id},
					success: function(data){
						//alert(data);
						if(data>0){
							if(data==1){
								tr.remove(); 
							}else{
								window.location.reload();
							}
						}else{
							layer.msg('删除失败');
						}
						layer.close(index); 
					}
				});

			});	
	}
	
     window.delAll=function(url) {
		  var ids =[];//定义一个数组  
		  $("input[name='id[]']:checked").each(function(){
		     ids.push($(this).val());
		  });
		  if(ids.length==0){
			  layer.msg('请先选中要删除的行');
			  return false;
		  }else{
			  ids=ids.join(",");//转字符串
		  }
		  //alert(ids);
			layer.confirm('确定要批量删除吗？',{icon:3}, function(index){
				$.ajax({
					type: "POST",
					url: url,
					data: {id:ids},
					success: function(data){
						//alert(data);
						if(data>0){
							window.location.reload();
						}else{
							layer.msg('删除失败');
						}
						layer.close(index); 
					}
				});

			});	
      }

	//formname 表单名称 | textid 文本框ID | codeid 返回ID | ID 分类ID 0:不限 | isdx 是否多选:1 | flag 是否限制选择(单选):1
	window.opentree=function(options){
		var defaults={
			tablename:'nclass',
			textid:'classname',
			codeid:'classid',
			id:0,
			isdx:0,
			flag:0,
			iscode:0
		};
		var options=$.extend(defaults ,options);
		window.layer.open({
			type: 2,
			skin: 'layui-layer-lan',
			title:'请选择...',
			area: ['260px', '450px'],
			maxmin: false,
			content:__ROOT__+'/manage/tree?tb='+options.tablename+'&textid='+options.textid+'&codeid='+options.codeid+'&id='+options.id+'&isdx='+options.isdx+'&flag='+options.flag+'&iscode='+options.iscode+'&tmp='+new Date().getTime()
		});
	}

    //执行多laydate实例
    $(".layui-date").each(function () {
        laydate.render({
            elem: this //指定元素
        });
	});
    //执行多laydate实例
    $(".layui-datetime").each(function () {
        laydate.render({
			elem: this //指定元素
			,type: 'datetime'
        });
    });	
});


function getEditor(t){
    var option={
		allowFileManager : false,
		urlType : 'domain',
		items : [
		'source', '|', 'undo', 'redo', '|', 'preview', 'print', 'template', 'code', 'cut', 'copy', 'paste',
		'plainpaste', 'wordpaste', '|', 'justifyleft', 'justifycenter', 'justifyright',
		'justifyfull', 'insertorderedlist', 'insertunorderedlist', 'indent', 'outdent', 'subscript',
		'superscript', 'clearhtml', 'quickformat', 'selectall', '|', 'fullscreen', '/',
		'formatblock', 'fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold',
		'italic', 'underline', 'strikethrough', 'lineheight', 'removeformat', '|', 'image', 'multiimage',
		'flash', 'media', 'insertfile', 'table', 'hr', 'emoticons', 'baidumap', 'pagebreak',
		'anchor', 'link', 'unlink', '|', 'about']
	};
	return KindEditor.create('textarea[name='+t+']',option);  
}
function getSampleEditor(t){
    var option={
		allowFileManager : false,
		urlType : 'domain',
		items : [
			'source', 'quickformat', 'fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold', 'italic', 'underline',
			'removeformat', '|', 'justifyleft', 'justifycenter', 'justifyright', 'insertorderedlist',
			'insertunorderedlist', '|', 'link', 'unlink', '|', 'image', 'fullscreen'
		]
	};
	return KindEditor.create('textarea[name='+t+']',option);  
}

function newdialog(title,url,w,h){

	var index=window.layer.open({
	//id:'layerwin',
	  type: 2,
	  skin: 'layui-layer-lan',
	  title:title,
	  area: [w+'px', '90%'],
	  maxmin: true,
	  //content: absurl+url+s+'timeStamp='+ new Date().getTime()
	  content: url
	});
	if(w==1024){
		layer.full(index);
	}	
}

//id div的id，isMultiple 是否多选，chkboxType 多选框类型{"Y": "ps", "N": "s"} 详细请看ztree官网
function initTree(id, isMultiple, chkboxType) {
    var setting = {
        view: {
            dblClickExpand: false,
            showLine: false
        },
        data: {
            simpleData: {
                enable: true
            }
        },
        check: {
            enable: false,
            chkboxType: {"Y": "ps", "N": "s"}
        },
        callback: {
            onClick: onClick,
            onCheck: onCheck
        }

    };
    if (isMultiple) {
        setting.check.enable = isMultiple;
    }
    if (chkboxType !== undefined && chkboxType != null) {
        setting.check.chkboxType = chkboxType;
    }

    return $.fn.zTree.init($("#" + id), setting, zNodes);
}

function onClick(event, treeId, treeNode) {
    var zTree = $.fn.zTree.getZTreeObj(treeId);
    if (zTree.setting.check.enable == true) {
        zTree.checkNode(treeNode, !treeNode.checked, false)
        assignment(zTree.getCheckedNodes());
    } else {
        assignment(zTree.getSelectedNodes());

    }
}

function onCheck(event, treeId, treeNode) {
    var zTree = $.fn.zTree.getZTreeObj(treeId);
    assignment(zTree.getCheckedNodes());
}


function assignment(nodes) {
    var names = "";
    var ids = "";
    var codes = "";
    for (var i = 0, l = nodes.length; i < l; i++) {
        names += nodes[i].name + ",";
        ids += nodes[i].id + ",";
        codes += nodes[i].code + ",";
    }
    if (names.length > 0) {
        names = names.substring(0, names.length - 1);
        ids = ids.substring(0, ids.length - 1);
        codes = codes.substring(0, codes.length - 1);
    }
    //alert(ids);
    $("#names").attr("value", names);
    $("#ids").attr("value", ids);
    $("#codes").attr("value", codes);
}