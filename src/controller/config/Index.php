<?php
declare(strict_types=1);

namespace plugin\base\controller\config;

use think\admin\Controller;

/**
 * 系统参数配置
 * @class Index
 * @package plugin\base\controller\config
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
            sysconf('base.help_steps', (string) ($data['help_steps'] ?? ''));
            $this->success('基础配置已保存');
        }

        $this->title = '基础配置';
        $this->current = 'index';
        $this->base = [
            'help_steps' => (string) (sysconf('base.help_steps') ?: "第一步：选择需要使用的功能工具\n第二步：按照提示填写或粘贴相关内容并提交\n第三步：等待处理完成，查看、复制或保存处理结果"),
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
            sysconf('base.share_title', (string) ($data['title'] ?? ''));
            sysconf('base.share_path', (string) ($data['path'] ?? '/pages/index/index'));
            sysconf('base.share_image', (string) ($data['image'] ?? ''));
            $this->success('分享配置已保存');
        }

        $this->title = '分享配置';
        $this->current = 'share';
        $this->share = [
            'title' => (string) sysconf('base.share_title'),
            'path' => (string) (sysconf('base.share_path') ?: '/pages/index/index'),
            'image' => (string) sysconf('base.share_image'),
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
            sysconf('base.contact_send_message_title', (string) ($data['send_message_title'] ?? ''));
            sysconf('base.contact_send_message_path', (string) ($data['send_message_path'] ?? ''));
            sysconf('base.contact_send_message_img', (string) ($data['send_message_img'] ?? ''));
            sysconf('base.contact_show_message_card', (string) ($data['show_message_card'] ?? '0'));
            sysconf('base.contact_official_qrcode', (string) ($data['official_qrcode'] ?? ''));
            $this->success('客服配置已保存');
        }

        $this->title = '客服配置';
        $this->current = 'contact';
        $this->contact = [
            'send_message_title' => (string) sysconf('base.contact_send_message_title'),
            'send_message_path' => (string) sysconf('base.contact_send_message_path'),
            'send_message_img' => (string) sysconf('base.contact_send_message_img'),
            'show_message_card' => (string) (sysconf('base.contact_show_message_card') ?: '0'),
            'official_qrcode' => (string) sysconf('base.contact_official_qrcode'),
        ];
        $this->fetch();
    }
}
