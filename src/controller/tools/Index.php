<?php
declare(strict_types=1);

namespace plugin\test\controller\tools;

use plugin\test\model\TestTools;
use think\admin\Controller;
use think\admin\helper\QueryHelper;

/**
 * 工具列表
 * @class Index
 * @package plugin\test\controller\tools
 */
class Index extends Controller
{
    /**
     * 工具列表
     * @auth true
     * @menu true
     */
    public function index(): void
    {
        TestTools::mQuery()->layTable(function () {
            $this->title = '工具列表';
        }, function (QueryHelper $query) {
            $query->like('title')->like('desc')->like('appid');
            $query->equal('status');
        });
    }

    /**
     * 添加工具
     * @auth true
     */
    public function add(): void
    {
        $this->_applyFormToken();
        TestTools::mForm('form');
    }

    /**
     * 编辑工具
     * @auth true
     */
    public function edit(): void
    {
        $this->_applyFormToken();
        TestTools::mForm('form');
    }

    /**
     * 修改状态或排序
     * @auth true
     */
    public function state(): void
    {
        TestTools::mSave($this->_vali([
            'status.in:0,1' => '状态值范围异常！',
            'status.require' => '状态值不能为空！',
        ]));
    }

    /**
     * 删除小程序
     * @auth true
     */
    public function remove(): void
    {
        TestTools::mDelete();
    }
}

