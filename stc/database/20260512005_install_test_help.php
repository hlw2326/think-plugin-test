<?php

declare(strict_types=1);

use think\admin\extend\PhinxExtend;
use think\migration\Migrator;

@set_time_limit(0);
@ini_set('memory_limit', '-1');

/**
 * 创建表：test_help（测试-帮助中心）
 */
class InstallTestHelp extends Migrator
{
    public function getName(): string
    {
        return 'InstallTestHelp';
    }

    public function change(): void
    {
        $table = $this->table('test_help', [
            'engine' => 'InnoDB',
            'collation' => 'utf8mb4_general_ci',
            'comment' => '测试-帮助',
        ]);
        PhinxExtend::upgrade($table, [
            ['type', 'string', ['limit' => 20, 'default' => 'faq', 'null' => false, 'comment' => '类型：step/faq']],
            ['question', 'string', ['limit' => 255, 'default' => '', 'null' => true, 'comment' => '问题或步骤文案']],
            ['answer', 'text', ['default' => null, 'null' => true, 'comment' => '答案内容']],
            ['sort', 'biginteger', ['limit' => 20, 'default' => 0, 'null' => true, 'comment' => '排序权重']],
            ['status', 'integer', ['limit' => 1, 'default' => 1, 'null' => true, 'comment' => '状态']],
            ['create_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'null' => false, 'comment' => '创建时间']],
            ['update_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'null' => false, 'comment' => '更新时间']],
        ], [
            'type', 'status', 'sort',
        ]);

        if (!$this->fetchRow("SELECT id FROM test_help LIMIT 1")) {
            $this->table('test_help')->insert([
                [
                    'type' => 'faq',
                    'question' => '使用时遇到问题怎么办？',
                    'answer' => '可以在客服聊天窗口联系客服处理。',
                    'sort' => 200,
                    'status' => 1,
                ],
                [
                    'type' => 'faq',
                    'question' => '为什么建议开启剪贴板？',
                    'answer' => '开启后进入首页会自动读取剪贴板内容，使用更加便捷。',
                    'sort' => 180,
                    'status' => 1,
                ],
                [
                    'type' => 'faq',
                    'question' => '保存失败怎么办？',
                    'answer' => '请检查相册权限和网络状态，权限关闭时需要在微信设置里重新打开。',
                    'sort' => 170,
                    'status' => 1,
                ],
            ])->saveData();
        }
    }
}
