<?php
declare(strict_types=1);

namespace plugin\base\controller\mp;

use plugin\base\model\BaseMp;
use think\admin\Controller;
use think\admin\helper\QueryHelper;

/**
 * 小程序管理
 * @class Index
 * @package plugin\base\controller\mp
 */
class Index extends Controller
{
    /**
     * 客服回调地址
     * @return string
     */
    protected function customerUrl(): string
    {
        return sprintf('%s/plugin-base/api.v1.custom/index?appid=小程序AppID', $this->request->domain());
    }

    /**
     * 小程序列表
     * @auth true
     * @menu true
     */
    public function index(): void
    {
        BaseMp::mQuery()->layTable(function () {
            $this->title = '小程序列表';
        }, function (QueryHelper $query) {
            $query->like('name')->like('appid');
            $query->equal('status');
        });
    }

    /**
     * 添加小程序
     * @auth true
     */
    public function add(): void
    {
        $this->_applyFormToken();
        BaseMp::mForm('form');
    }

    /**
     * 编辑小程序
     * @auth true
     */
    public function edit(): void
    {
        $this->_applyFormToken();
        BaseMp::mForm('form');
    }

    /**
     * 表单数据处理
     * @param array $data
     */
    protected function _form_filter(array &$data): void
    {
        if ($this->request->isGet()) {
            $this->customerUrl = $this->customerUrl();
            if (!isset($data['custom_reply_enabled'])) {
                $data['custom_reply_enabled'] = 1;
            }
        }
    }

    /**
     * 修改状态
     * @auth true
     */
    public function state(): void
    {
        BaseMp::mSave($this->_vali([
            'status.in:0,1' => '状态值范围异常！',
            'status.require' => '状态值不能为空！',
        ]));
    }

    /**
     * 修改客服消息状态
     * @auth true
     */
    public function custom(): void
    {
        BaseMp::mSave($this->_vali([
            'custom_reply_enabled.in:0,1' => '客服消息状态范围异常！',
            'custom_reply_enabled.require' => '客服消息状态不能为空！',
        ]));
    }

    /**
     * 删除小程序
     * @auth true
     */
    public function remove(): void
    {
        BaseMp::mDelete();
    }

    /**
     * 配置 pages.json
     * @auth true
     */
    public function pages(): void
    {
        $this->_applyFormToken();
        BaseMp::mForm();
    }

    /**
     * 广告配置
     * @auth true
     */
    public function ad(): void
    {
        $this->_applyFormToken();
        BaseMp::mForm();
    }
}

