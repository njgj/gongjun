//config的设置是全局的
/*layui.config({
  base: '/tp5/public/static/' //假设这是你存放拓展模块的根目录
}).extend({ //设定模块别名
  dialog: 'manage/dialog'
});*/

// 主入口方法
layui.use(['element','jquery','layer'], function(){
    var device = layui.device() 
	,element = layui.element
	,$ = layui.jquery
	,layer = layui.layer
	,side = $('.my-side')
	,body = $('.my-body')
    ,footer = $('.my-footer');
		
	//阻止IE7以下访问
	//alert(device.ie);
    if (device.ie && device.ie < 8) {
        layer.alert('如果您非得使用ie浏览，那么请使用ie8+');
    }
    // 监听导航(side-main)点击切换页面
    element.on('nav(side-main)', function (elem) {
        // 添加tab方法
        window.addTab(elem);
    });
    // 监听顶部左侧导航
    element.on('nav(side-top-left)', function (elem) {
        // 添加tab方法
        window.addTab(elem);
    });
    // 监听顶部右侧导航
    element.on('nav(side-top-right)', function (elem) {
        // 修改skin
        var data_skin=elem.parent().attr('data-skin'); //20181108
        if (data_skin) {
            localStorage.skin = data_skin;
            
            skin();
        } else {
            // 添加tab方法
            window.addTab(elem);
        }
    });	
    // 监听右侧导航
    element.on('nav(side-menu)', function (elem) {
        // 添加tab方法
        window.addTab(elem);
    });

    // 添加TAB选项卡
    window.addTab = function(elem, tit, url) {
        var card = 'card';                                              // 选项卡对象
        var title = tit ? tit : elem.html();              // 导航栏text
        var src = url ? url : elem.attr('data-url');      // 导航栏跳转URL
        var id = new Date().getTime();                                  // ID
        var flag = getTitleId(card, title);
        //this.alert(card+title+src+id+flag);
        // 大于0就是有该选项卡了
        if (flag > 0) {
            id = flag;
        } else {
            if (src) {
                //新增
                element.tabAdd(card, {
                    title: '<span>' + title + '</span>'
                    , content: '<iframe src="' + src + '" data-id="'+id+'" frameborder="0"></iframe>'
                    , id: id
                });
                // 关闭弹窗
                //layer.closeAll();
            }
        }
        // 切换相应的ID tab
        element.tabChange(card, id);
        // 提示信息
        // layer.msg(title);
    };
    // 删除选项卡
    window.delTab = function (layId) {
        // 删除
        element.tabDelete('card', layId);
    };
    // 删除其它/所有选项卡（ID：0）
    window.delAllTab = function (ID) {
        // 选项卡对象
        layui.each($('.my-body .layui-tab-title > li'), function (k, v) {
            var layId = $(v).attr('lay-id');
            if (layId > 1 && layId!=ID) {
                // 删除
                element.tabDelete('card', layId);
            }
        });
    };	

    // 根据导航栏text获取lay-id
    function getTitleId(card, title) {
        var id = -1,tabTitle='';
        $(document).find(".layui-tab[lay-filter=" + card + "] ul li").each(function () {
            //排除图标
            tabTitle=$(this).find("span").html();

            if (title === tabTitle) {
                id = $(this).attr('lay-id');
                return false; //return false终止循环
            }
        });
        //alert(title+'|'+tabTitle+'|'+id);
        return id;
    }

    // 选项卡右键事件阻止
    $(document).on("contextmenu", '.my-body .layui-tab-card > .layui-tab-title li', function () {
        return false;
    });
    // 选项卡右键事件
    $(document).on("mousedown", '.my-body .layui-tab-card > .layui-tab-title li', function (e) {
        // 判断是右键点击事件并且不是欢迎页面选项卡
        if (3 == e.which && $(this).index() > 0) {
            // 赋值
            cardIdx = $(this).index();
            cardLayId = $(this).attr('lay-id');
            
			//console.log('lay-id:' + cardLayId);
            // 选择框
            layer.tips($('.my-dblclick-box').html(), $(this), {
                skin: 'dblclick-tips-box',
                tips: 3,
                time: false
            });
        }
    });
   // 点击body关闭tips
    $(document).on('click', 'html', function () {
        layer.closeAll('tips');
    });
    // 右键提示框菜单操作-刷新页面
    $(document).on('click', '.card-refresh', function () {
        // 窗体对象
        var ifr = $(document).find('.my-body .layui-tab-content .layui-tab-item iframe').eq(cardIdx);
        // 刷新当前页
        ifr.attr('src', ifr.attr('src'));
        // 切换到当前选项卡
        element.tabChange('card', cardLayId);
    });
	
	//手动刷新按钮
	$('#reload-btn').click(function(){
		
		var cardIdx = $('.my-body .layui-tab-card > .layui-tab-title li.layui-this').index();
        // 窗体对象
        var ifr = $(document).find('.my-body .layui-tab-content .layui-tab-item iframe').eq(cardIdx);
        // 刷新当前页
        ifr.attr('src', ifr.attr('src'));
	});
	
	
    // 右键提示框菜单操作-关闭页面
    $(document).on('click', '.card-close', function () {
        // 删除
        window.delTab(cardLayId);
    });
    // 右键提示框菜单操作-关闭其它页面
    $(document).on('click', '.card-close-other', function () {
        // 删除
        //alert(cardLayId);
        window.delAllTab(cardLayId);
    });  
    // 右键提示框菜单操作-关闭所有页面
    $(document).on('click', '.card-close-all', function () {
        // 删除
        window.delAllTab(0);
    });	
	
	
    // 皮肤
	//alert(localStorage.skin);
    function skin() {
        var skin = localStorage.skin ? localStorage.skin : 0;
        var body = $('body');
        body.removeClass('skin-0');
        body.removeClass('skin-1');
        body.removeClass('skin-2');
        body.addClass('skin-' + skin);
    }

    // 导航栏收缩
    function navHide(t) {
        var time = t ? t : 50;
        side.animate({'left': -200}, time);
        body.animate({'left': 0}, time);
        footer.animate({'left': 0}, time);
    }
    // 导航栏展开
    function navShow(t) {
        var time = t ? t : 50;
        side.animate({'left': 0}, time);
        body.animate({'left': 200}, time);
        footer.animate({'left': 200}, time);
    }
    // 监听导航栏收缩
    $('.btn-nav').on('click',function(){
	    var left=side.css('left');
		//alert(left);
		if(left=='0px'){
			navHide(50);
		}else{
			navShow(50);
		}
	});

    // 自适应
    $(window).on('resize', function () {
        // if ($(window).width() > 800) {
        //     navShow(10);
        // } else {
        //     navHide(10);
        // }
		iframe_auto();
    });
	
	//iframe高度自适应
	function iframe_auto(){
        // 选项卡高度
        cardTitleHeight = $(document).find(".layui-tab[lay-filter='card'] ul.layui-tab-title").height();
        // 需要减去的高度
        height = $(window).height() - $('.layui-header').height() - cardTitleHeight - $('.layui-footer').height();
		//alert(height);
        // 设置高度
        $(document).find(".layui-tab[lay-filter='card'] div.layui-tab-content").height(height - 2);
	}
	
    // 监听控制content高度
    function init() {
        // skin
        skin();

		//判断resize
        //$(window).trigger('resize');
        iframe_auto();
    }

    // 初始化
    init();		
});
