//提示：模块也可以依赖其它模块，如：layui.define('layer', callback);
layui.define(['layer','upload'],function(exports){
    var $ = layui.jquery
        ,layer=layui.layer
        ,upload=layui.upload;

    var obj = {
            //普通图片上传
        upfile:function(options) {
            var defaults ={
                id:'#upload',
                type:'images',
                input:'#imgurl',
                img:'#img',
                size:'#filesize',
                oldname:'#oldname',
                url:'__MAN__/upload',
                dir:'__STATIC__/uploadfile'
            };
            var options=$.extend(defaults ,options);
            upload.render({
                elem: options.id
                , url: options.url
                , accept: options.type
                , data: {type: options.type}
                , before: function (obj) {
                    //预读本地文件示例，不支持ie8
/*                    obj.preview(function (index, file, result) {
                        $(options.img).attr('src', result); //图片链接
                    });*/
                }
                , done: function (res) {
                    //alert(res.code);
                    //如果上传失败
                    if (res.code == 0) {

                        if(options.type=='images'){
                            $(options.img).attr('src', options.dir+'/'+options.type+'/'+res.data.savename); //图片链接
                        }
                        if(options.type=='file'){
                            $(options.img).attr('style', '');
                            $(options.img).attr('href', options.dir+'/'+options.type+'/'+res.data.savename); //文件链接
                        }  
                                                
                        $(options.input).val(res.data.savename);
                        $(options.oldname).val(res.data.filename);
                        $(options.size).val(res.data.filesize);
                        
                    }
                    layer.msg(res.msg);
                }
            });
        }
    };

    //输出test接口
    exports('mymod', obj);
});
