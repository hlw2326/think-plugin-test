<?php
declare(strict_types=1);

namespace plugin\base\controller\help;

use plugin\base\model\BaseHelp;
use think\admin\Controller;
use think\admin\helper\QueryHelper;

/**
 * 帮助列表
 * @class Index
 * @package plugin\base\controller\help
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
        BaseHelp::mQuery()->layTable(function () {
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
        BaseHelp::mForm('form');
    }

    /**
     * 编辑帮助
     * @auth true
     */
    public function edit(): void
    {
        $this->_applyFormToken();
        BaseHelp::mForm('form');
    }

    /**
     * 修改状态
     * @auth true
     */
    public function state(): void
    {
        BaseHelp::mSave($this->_vali([
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
        BaseHelp::mDelete();
    }
}

