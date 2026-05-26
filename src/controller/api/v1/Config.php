<?php
declare(strict_types=1);

namespace plugin\test\controller\api\v1;

use plugin\test\service\AdService;

/**
 * 小程序端配置 API
 * @class Config
 * @package plugin\test\controller\api\v1
 */
class Config extends Base
{
    public function index(): void
    {
        $this->success('获取成功', [
            'share' => [
                'title' => (string) sysconf('test.share_title'),
                'path' => (string) (sysconf('test.share_path') ?: '/pages/index/index'),
                'image_url' => (string) sysconf('test.share_image'),
            ],
            'contact' => [
                'send_message_title' => (string) sysconf('test.contact_send_message_title'),
                'send_message_path' => (string) sysconf('test.contact_send_message_path'),
                'send_message_img' => (string) sysconf('test.contact_send_message_img'),
                'show_message_card' => (int) (sysconf('test.contact_show_message_card') ?: 0) === 1,
                'official_qrcode' => (string) sysconf('test.contact_official_qrcode'),
            ],
            'ad' => AdService::mpConfig($this->mp),
        ]);
    }
}
