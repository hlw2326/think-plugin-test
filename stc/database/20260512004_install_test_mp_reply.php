<?php

declare(strict_types=1);

use think\admin\extend\PhinxExtend;
use think\migration\Migrator;

@set_time_limit(0);
@ini_set('memory_limit', '-1');

/**
 * 创建表：test_mp_reply（去水印-小程序客服回复规则）
 */
class InstallTestMpReply extends Migrator
{
    public function getName(): string
    {
        return 'InstallTestMpReply';
    }

    public function change(): void
    {
        $table = $this->table('test_mp_reply', [
            'engine' => 'InnoDB',
            'collation' => 'utf8mb4_general_ci',
            'comment' => '去水印-回复规则',
        ]);
        PhinxExtend::upgrade($table, [
            ['appid', 'string', ['limit' => 50, 'default' => '', 'null' => false, 'comment' => '小程序AppID']],
            ['msg_type', 'string', ['limit' => 20, 'default' => 'text', 'null' => false, 'comment' => '消息类型(text,event,all)']],
            ['match_type', 'string', ['limit' => 20, 'default' => 'exact', 'null' => false, 'comment' => '匹配方式(exact,contains,default)']],
            ['keyword', 'string', ['limit' => 200, 'default' => '', 'null' => true, 'comment' => '匹配关键词']],
            ['reply_type', 'string', ['limit' => 20, 'default' => 'text', 'null' => false, 'comment' => '回复类型(text,image)']],
            ['content', 'text', ['default' => null, 'null' => true, 'comment' => '回复内容']],
            ['image_url', 'string', ['limit' => 255, 'default' => '', 'null' => true, 'comment' => '回复图片']],
            ['sort', 'biginteger', ['limit' => 20, 'default' => 0, 'null' => true, 'comment' => '排序权重']],
            ['status', 'integer', ['limit' => 1, 'default' => 1, 'null' => true, 'comment' => '状态']],
            ['create_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'null' => false, 'comment' => '创建时间']],
            ['update_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'null' => false, 'comment' => '更新时间']],
        ], [
            'appid', 'msg_type', 'match_type', 'status',
        ]);
    }
}

