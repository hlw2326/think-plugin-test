<?php
declare(strict_types=1);

namespace plugin\test\controller\config;

use think\admin\Controller;

/**
 * 系统参数配置
 * @class Index
 * @package plugin\test\controller\config
 */
class Index extends Controller
{
    /**
     * 基础配置
     * @auth true
     * @menu true
     */
    public function index(): void
    {
        if ($this->request->isPost()) {
            $data = $this->request->post();
            sysconf('test.help_steps', (string) ($data['help_steps'] ?? ''));
            $this->success('基础配置已保存');
        }

        $this->title = '基础配置';
        $this->current = 'index';
        $this->base = [
            'help_steps' => (string) (sysconf('test.help_steps') ?: "第一步：复制链接\n第二步：打开小程序\n第三步：点击开始使用"),
        ];
        $this->fetch();
    }

    /**
     * 分享配置
     * @auth true
     */
    public function share(): void
    {
        if ($this->request->isPost()) {
            $data = $this->request->post();
            sysconf('test.share_title', (string) ($data['title'] ?? ''));
            sysconf('test.share_path', (string) ($data['path'] ?? '/pages/index/index'));
            sysconf('test.share_image', (string) ($data['image'] ?? ''));
            $this->success('分享配置已保存');
        }

        $this->title = '分享配置';
        $this->current = 'share';
        $this->share = [
            'title' => (string) sysconf('test.share_title'),
            'path' => (string) (sysconf('test.share_path') ?: '/pages/index/index'),
            'image' => (string) sysconf('test.share_image'),
        ];
        $this->fetch();
    }

    /**
     * 客服配置
     * @auth true
     */
    public function contact(): void
    {
        if ($this->request->isPost()) {
            $data = $this->request->post();
            sysconf('test.contact_send_message_title', (string) ($data['send_message_title'] ?? ''));
            sysconf('test.contact_send_message_path', (string) ($data['send_message_path'] ?? ''));
            sysconf('test.contact_send_message_img', (string) ($data['send_message_img'] ?? ''));
            sysconf('test.contact_show_message_card', (string) ($data['show_message_card'] ?? '0'));
            sysconf('test.contact_official_qrcode', (string) ($data['official_qrcode'] ?? ''));
            $this->success('客服配置已保存');
        }

        $this->title = '客服配置';
        $this->current = 'contact';
        $this->contact = [
            'send_message_title' => (string) sysconf('test.contact_send_message_title'),
            'send_message_path' => (string) sysconf('test.contact_send_message_path'),
            'send_message_img' => (string) sysconf('test.contact_send_message_img'),
            'show_message_card' => (string) (sysconf('test.contact_show_message_card') ?: '0'),
            'official_qrcode' => (string) sysconf('test.contact_official_qrcode'),
        ];
        $this->fetch();
    }
}
