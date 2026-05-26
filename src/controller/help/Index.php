<?php
declare(strict_types=1);

namespace plugin\test\controller\help;

use plugin\test\model\TestHelp;
use think\admin\Controller;
use think\admin\helper\QueryHelper;

/**
 * 帮助中心
 * @class Index
 * @package plugin\test\controller\help
 */
class Index extends Controller
{
    /**
     * 帮助列表
     * @auth true
     * @menu true
     */
    public function index(): void
    {
        TestHelp::mQuery()->layTable(function () {
            $this->title = '帮助列表';
        }, function (QueryHelper $query) {
            $query->like('question')->like('answer');
            $query->where(['type' => 'faq'])->equal('status');
        });
    }

    /**
     * 添加帮助
     * @auth true
     */
    public function add(): void
    {
        $this->_applyFormToken();
        TestHelp::mForm('form');
    }

    /**
     * 编辑帮助
     * @auth true
     */
    public function edit(): void
    {
        $this->_applyFormToken();
        TestHelp::mForm('form');
    }

    /**
     * 修改状态
     * @auth true
     */
    public function state(): void
    {
        TestHelp::mSave($this->_vali([
            'status.in:0,1' => '状态值范围异常！',
            'status.require' => '状态值不能为空！',
        ]));
    }

    /**
     * 删除帮助
     * @auth true
     */
    public function remove(): void
    {
        TestHelp::mDelete();
    }
}

