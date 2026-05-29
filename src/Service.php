<?php

declare(strict_types=1);

namespace plugin\test;

use plugin\test\exception\ApiExceptionHandle;
use think\admin\Plugin;
use think\exception\Handle;

/**
 * 测试通用插件
 */
class Service extends Plugin
{
    protected $appName = '测试插件';

    protected $package = 'hlw2326/think-plugin-test';

    public function register(): void
    {
        $this->app->bind(Handle::class, ApiExceptionHandle::class);
    }

    public function boot(): void
    {
        if (class_exists(\WeChat\Contracts\Tools::class)) {
            \WeChat\Contracts\Tools::$cache_path = $this->app->getRuntimePath() . 'wechat' . DIRECTORY_SEPARATOR;
        }
    }

    public static function menu(): array
    {
        $code = app(static::class)->appCode;
        return [
            ['name' => '系统统计', 'icon' => 'layui-icon layui-icon-chart-screen', 'node' => "{$code}/main.index/index"],
            ['name' => '系统参数', 'icon' => 'layui-icon layui-icon-set', 'node' => "{$code}/config.index/index"],
            ['name' => '用户列表', 'icon' => 'layui-icon layui-icon-user', 'node' => "{$code}/user.index/index"],
            ['name' => '微信程序', 'icon' => 'layui-icon layui-icon-app', 'node' => "{$code}/mp.index/index"],
            ['name' => '客服回复', 'icon' => 'layui-icon layui-icon-dialogue', 'node' => "{$code}/mp.reply/index"],
            ['name' => '工具列表', 'icon' => 'layui-icon layui-icon-release', 'node' => "{$code}/tools.index/index"],
            ['name' => '帮助列表', 'icon' => 'layui-icon layui-icon-help', 'node' => "{$code}/help.index/index"],
        ];
    }
}
