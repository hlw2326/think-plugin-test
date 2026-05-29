<?php
declare(strict_types=1);

namespace plugin\test\controller\api\v1;

use plugin\test\model\TestMp;
use plugin\test\service\CustomService;
use think\admin\Controller;
use WeChat\Contracts\Tools;
use WeChat\Receive;

/**
 * 客服消息 API
 * @class Custom
 * @package plugin\test\controller\api\v1
 */
class Custom extends Controller
{
    /**
     * 微信小程序客服消息回调
     * @return string
     */
    public function index(): string
    {
        try {
            $mp = $this->mp();
            if (!$this->checkSignature((string)$mp->token)) {
                return '';
            }
            if ($this->request->isGet()) {
                return (string)$this->request->get('echostr', '');
            }
            if (empty($mp->custom_reply_enabled)) {
                return 'success';
            }
            $message = $this->receive($mp);
            $openid = (string)($message['FromUserName'] ?? $message['fromusername'] ?? '');
            if ($openid !== '' && ($rule = CustomService::match($mp, $message))) {
                CustomService::sendRule($mp, $openid, $rule);
            }
        } catch (\Throwable $exception) {
            $this->app->log->error("TEST mini custom reply failed: {$exception->getMessage()}");
        }
        return $this->request->isGet() ? '' : 'success';
    }

    /**
     * 获取小程序配置
     * @return TestMp
     */
    private function mp(): TestMp
    {
        $appid = (string)$this->request->get('appid', '');
        if ($appid === '') {
            throw new \RuntimeException('缺少 appid 参数');
        }
        $mp = TestMp::mk()->where(['appid' => $appid, 'status' => 1])->findOrEmpty();
        if ($mp->isEmpty()) {
            throw new \RuntimeException('无效的小程序 appid');
        }
        if (empty($mp->token)) {
            throw new \RuntimeException('小程序未配置消息校验 Token');
        }
        return $mp;
    }

    /**
     * 读取微信推送消息
     * @param TestMp $mp
     * @return array
     */
    private function receive(TestMp $mp): array
    {
        $raw = trim(Tools::getRawInput());
        if ($raw === '') {
            return [];
        }

        if (str_starts_with($raw, '{')) {
            return $this->arrayChangeKeyCase(json_decode($raw, true) ?: []);
        }

        if ($this->request->get('encrypt_type') === 'aes') {
            Tools::setRawInput($raw);
            $receive = new Receive(CustomService::config($mp), false);
            return $this->arrayChangeKeyCase($receive->getReceive());
        }

        return $this->arrayChangeKeyCase(Tools::xml2arr($raw));
    }

    /**
     * 验证微信签名
     * @param string $token
     * @return bool
     */
    private function checkSignature(string $token): bool
    {
        $nonce = (string)$this->request->get('nonce', '');
        $timestamp = (string)$this->request->get('timestamp', '');
        $signature = (string)($this->request->get('msg_signature', '') ?: $this->request->get('signature', ''));
        $tmpArr = [$token, $timestamp, $nonce, $this->request->get('msg_signature') ? $this->encryptPayload() : ''];
        sort($tmpArr, SORT_STRING);
        return sha1(implode($tmpArr)) === $signature;
    }

    /**
     * 获取加密消息体
     * @return string
     */
    private function encryptPayload(): string
    {
        $raw = trim(Tools::getRawInput());
        if ($raw === '') {
            return '';
        }
        $data = Tools::xml2arr($raw);
        return (string)($data['Encrypt'] ?? $data['encrypt'] ?? '');
    }

    /**
     * 数组键名兼容大小写
     * @param array $data
     * @return array
     */
    private function arrayChangeKeyCase(array $data): array
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $value = $this->arrayChangeKeyCase($value);
            }
            $data[strtolower((string)$key)] = $value;
            if (strtolower((string)$key) !== (string)$key) {
                unset($data[$key]);
            }
        }
        return $data;
    }
}
