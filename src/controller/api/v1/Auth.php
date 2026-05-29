<?php
declare(strict_types=1);

namespace plugin\test\controller\api\v1;

use plugin\test\model\TestUser;

/**
 * 登录验证 API
 * @class Auth
 * @package plugin\test\controller\api\v1
 */
class Auth extends Base
{
    protected TestUser $user;

    protected string $userId = '';

    protected function initialize(): void
    {
        parent::initialize();

        $token = $this->request->header('X-Token', '');
        if ($token === '') {
            $this->error('请先登录', [], 401);
        }

        $user = TestUser::mk()->where(['token' => $token, 'deleted' => 0])->findOrEmpty();
        if ($user->isEmpty()) {
            $this->error('登录已过期，请重新登录', [], 401);
        }
        if (intval($user->status) !== 1) {
            $this->error('账号已被禁用', [], 403);
        }

        $this->userId = (string) $user->id;
        $this->user = $user;
    }
}

