<?php
namespace app\manage\controller;
use think\Controller;

class Upload extends Controller
{

    public function index()
    {
        $type=input('post.type/s');
        switch ($type) {
            case 'images':
                $ext = 'jpg,png,gif';
                break;
            case 'file':
                $ext = 'doc,docx,xls,xlsx,ppt,pptx,pdf';
                break;
            default:
                return json(array('code' => 1, 'msg' => $type.'格式错误'));
        }

        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('file');
        // 移动到框架应用根目录/static/uploadfile/ 目录下
        $savepath = ROOT_PATH . '/public/static/uploadfile/' . $type;

        if ($file) {
            $info = $file->validate(['size' => 2000 * 1000, 'ext' => $ext])->rule('date')->move($savepath);
            if ($info) {            
                $data = [
                    'savename'=>$info->getSaveName()  // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
                    ,'filename'=>$file->getInfo()['name']   //1.xls
                    ,'filesize'=>round($file->getInfo()['size']/1000)  //kb
                ];
                // 成功上传后 返回上传信息
                return json(array('code' => 0, 'msg' => '上传成功', 'data' => $data));
            } else {
                // 上传失败返回错误信息
                return json(array('code' => 1, 'msg' => $file->getError()));
            }
        }
    }
}
