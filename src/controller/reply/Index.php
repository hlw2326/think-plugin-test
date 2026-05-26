<?php
declare(strict_types=1);

namespace plugin\test\controller\reply;

use plugin\test\model\TestMp;
use plugin\test\model\TestMpReply;
use think\admin\Controller;
use think\admin\helper\QueryHelper;
use think\admin\service\SystemService;

/**
 * 小程序客服回复规则
 * @class Index
 * @package plugin\test\controller\reply
 */
class Index extends Controller
{
    /**
     * 回复类型
     * @var array<string,string>
     */
    public array $types = [
        'text'  => '文字',
        'image' => '图片',
    ];

    /**
     * 匹配方式
     * @var array<string,string>
     */
    public array $matchTypes = [
        'exact'    => '完全匹配',
        'contains' => '包含匹配',
        'default'  => '默认回复',
    ];

    /**
     * 客服回复规则列表
     * @auth true
     * @menu true
     */
    public function index(): void
    {
        $this->appid = (string)($this->get['appid'] ?? '');
        TestMpReply::mQuery()->layTable(function () {
            $this->title = '客服回复规则';
            $this->mps = $this->mps();
        }, function (QueryHelper $query) {
            $query->like('keyword|content#keys')->equal('reply_type#mtype,match_type,status')->dateBetween('create_at');
            $query->where(['appid' => $this->appid]);
        });
    }

    /**
     * 列表数据处理
     * @param array $data
     */
    protected function _index_page_filter(array &$data): void
    {
        foreach ($data as &$vo) {
            $vo['type'] = $this->types[$vo['reply_type']] ?? $vo['reply_type'];
            $vo['match_name'] = $this->matchTypes[$vo['match_type']] ?? $vo['match_type'];
            $vo['keys'] = $vo['match_type'] === 'default' ? '默认回复' : ($vo['keyword'] ?: '-');
            $vo['appid'] = $vo['appid'] ?: '通用回复';
        }
        unset($vo);
    }

    /**
     * 保存排序
     * @auth true
     */
    public function sort(): void
    {
        TestMpReply::mSave($this->_vali([
            'sort.require' => '排序值不能为空！',
            'sort.number'  => '排序值格式异常！',
        ]));
    }

    /**
     * 默认回复
     * @auth true
     */
    public function defaults(): void
    {
        $this->_applyFormToken();
        $appid = (string)($this->get['appid'] ?? '');
        $data = ['appid' => $appid, 'match_type' => 'default', 'msg_type' => 'all', 'status' => 1, 'sort' => 0];
        $vo = TestMpReply::mk()->where(['appid' => $appid, 'match_type' => 'default'])->findOrEmpty()->toArray();
        if (!empty($vo)) {
            $data = array_merge($data, $vo);
        }
        TestMpReply::mForm('form', 'id', [], $data);
    }

    /**
     * 客服回调地址
     * @return string
     */
    protected function customerUrl(): string
    {
        return sprintf('%s/plugin-test/api.v1.custom/index?appid=小程序AppID', $this->request->domain());
    }

    /**
     * 小程序列表
     * @return array
     */
    protected function mps(): array
    {
        return TestMp::mk()->where(['status' => 1])->order('sort desc,id asc')->select()->toArray();
    }

    /**
     * 表单视图变量
     */
    protected function assignFormVars(): void
    {
        $this->mps = $this->mps();
        $this->defaultImage = SystemService::uri('/static/theme/img/image.png', '__FULL__');
        $this->customerUrl = $this->customerUrl();
    }

    /**
     * 表单数据校验
     * @param array $data
     */
    protected function checkFormData(array &$data): void
    {
        $data['content'] = trim(strip_tags((string)($data['content'] ?? '')));
        $data['image_url'] = trim((string)($data['image_url'] ?? ''));
        $data['appid'] = trim((string)($data['appid'] ?? ''));
        $data['msg_type'] = 'all';
        if (($data['match_type'] ?? '') === 'default') {
            $data['keyword'] = '';
            $query = TestMpReply::mk()->where(['appid' => $data['appid'] ?? '', 'match_type' => 'default']);
            if (!empty($data['id'])) {
                $query->where('id', '<>', $data['id']);
            }
            if ($query->count() > 0) {
                $this->error('该小程序已存在默认回复');
            }
        } elseif (trim((string)($data['keyword'] ?? '')) === '') {
            $this->error('请输入匹配关键词');
        }
        if (($data['reply_type'] ?? '') === 'image' && $data['image_url'] === '') {
            $this->error('请上传回复图片');
        }
        if (($data['reply_type'] ?? 'text') === 'text' && $data['content'] === '') {
            $this->error('请输入文本回复内容');
        }
        if (($data['reply_type'] ?? 'text') !== 'image') {
            $data['image_url'] = '';
        }
        if (($data['reply_type'] ?? 'text') !== 'text') {
            $data['content'] = '';
        }
    }

    /**
     * 添加规则
     * @auth true
     */
    public function add(): void
    {
        $this->_applyFormToken();
        TestMpReply::mForm('form');
    }

    /**
     * 编辑规则
     * @auth true
     */
    public function edit(): void
    {
        $this->_applyFormToken();
        TestMpReply::mForm('form');
    }

    /**
     * 表单数据处理
     * @param array $data
     */
    protected function _form_filter(array &$data): void
    {
        if ($this->request->isGet()) {
            $this->assignFormVars();
            if (empty($data['appid']) && !empty($this->get['appid'])) {
                $data['appid'] = (string)$this->get['appid'];
            }
            return;
        }
        $this->checkFormData($data);
    }

    /**
     * 修改状态
     * @auth true
     */
    public function state(): void
    {
        TestMpReply::mSave($this->_vali([
            'status.in:0,1' => '状态值范围异常！',
            'status.require' => '状态值不能为空！',
        ]));
    }

    /**
     * 删除规则
     * @auth true
     */
    public function remove(): void
    {
        TestMpReply::mDelete();
    }
}

