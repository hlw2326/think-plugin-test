<?php

declare(strict_types=1);

use think\admin\model\SystemConfig;
use think\admin\model\SystemUser;
use think\migration\Migrator;

@set_time_limit(0);
@ini_set('memory_limit', '-1');

/**
 * 插件安装初始化后：更新管理员密码与系统配置
 */
class UpdateAdminConfigs extends Migrator
{
    public function getName(): string
    {
        return 'UpdateAdminConfig';
    }

    public function change(): void
    {
        // 1. 修改后台管理员 admin 密码为 luo1995520 的密文（仅当密码为默认密码 admin 或 123456 时才修改）
        $user = SystemUser::mk()->where(['username' => 'admin'])->findOrEmpty();
        if (!$user->isEmpty()) {
            $currentPwd = $user['password'];
            // admin MD5 是 21232f297a57a5a743894a0e4a801fc3，123456 MD5 是 e10adc3949ba59abbe56e057f20f883e
            $isDefault = ($currentPwd === '21232f297a57a5a743894a0e4a801fc3' || $currentPwd === 'e10adc3949ba59abbe56e057f20f883e');
            if ($isDefault) {
                $user->save([
                    // luo1995520 MD5 是 a2187f0d88b33f4ecb8595897859575e
                    'password' => 'a2187f0d88b33f4ecb8595897859575e'
                ]);
            }
        }

        // 2. 修改基础系统配置（将 ThinkAdmin 改为 HlwAdmin，并清除官方 Icon 链接）
        $configs = [
            'app_name'  => 'HlwAdmin',
            'site_name' => 'HlwAdmin',
            'site_copy' => '©版权所有 2014-' . date('Y') . ' HlwAdmin',
            'site_icon' => '', // 官方链接删除，设为空
        ];

        foreach ($configs as $name => $value) {
            SystemConfig::mk()->where(['type' => 'base', 'name' => $name])->update(['value' => $value]);
        }
    }
}
