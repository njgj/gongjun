<?php
namespace app\manage\controller;
use think\Db;

class Form extends Base
{
    public function index($tb)
    {
		$res=Db::query("select COLUMN_NAME,COLUMN_COMMENT,DATA_TYPE from information_schema.COLUMNS where TABLE_SCHEMA='".config('database.database')."' and table_name = '$tb'");
		$this->assign([
			'res'=>$res,
		]);
        //dump($res);
		return $this->fetch();
    }
    public function table($tb)
    {
        $res=Db::query("select COLUMN_NAME,COLUMN_COMMENT,DATA_TYPE from information_schema.COLUMNS where TABLE_SCHEMA='".config('database.database')."' and table_name = '$tb'");
        $this->assign([
            'res'=>$res,
        ]);
        //dump($res);
        return $this->fetch();
    }
}