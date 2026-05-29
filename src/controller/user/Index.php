<?php
declare(strict_types=1);

namespace plugin\test\controller\user;

use plugin\test\model\TestUser;
use think\admin\Controller;
use think\admin\helper\QueryHelper;

/**
 * 用户列表
 * @class Index
 * @package plugin\test\controller\user
 */
class Index extends Controller
{
    /**
     * 用户列表
     * @auth true
     * @menu true
     */
    public function index(): void
    {
        TestUser::mQuery()->layTable(function () {
            $this->title = '用户列表';
        }, function (QueryHelper $query) {
            $query->equal('id');
            $query->like('nickname')->like('phone')->like('openid');
            $query->equal('status,appid');
            $query->dateBetween('create_at');
            $query->where(['deleted' => 0]);
        });
    }

    /**
     * 修改状态
     * @auth true
     */
    public function state(): void
    {
        TestUser::mSave($this->_vali([
            'status.in:0,1' => '状态值范围异常！',
            'status.require' => '状态值不能为空！',
        ]));
    }

    /**
     * 退出登录
     * @auth true
     */
    public function logout(): void
    {
        $id = intval($this->request->post('id', $this->request->get('id', 0)));
        if ($id <= 0) {
            $this->error('用户 ID 不能为空！');
        }

        TestUser::mk()->where(['id' => $id, 'deleted' => 0])->update(['token' => '']);
        $this->success('已退出登录！');
    }
}

