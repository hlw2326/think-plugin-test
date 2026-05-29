<?php
declare(strict_types=1);

namespace plugin\test\service;

use plugin\test\model\TestMp;
use plugin\test\model\TestMpReply;
use think\admin\Storage;
use WeChat\Contracts\Tools;
use WeMini\Custom;
use WeMini\Media;

/**
 * 小程序客服消息服务
 * @class CustomService
 * @package plugin\test\service
 */
class CustomService
{
    /**
     * 匹配客服回复规则
     * @param TestMp $mp
     * @param array $message
     * @return null|TestMpReply
     */
    public static function match(TestMp $mp, array $message): ?TestMpReply
    {
        $msgType = strtolower((string)($message['MsgType'] ?? $message['msgtype'] ?? ''));
        $content = trim((string)($message['Content'] ?? $message['content'] ?? ''));
        $event = trim((string)($message['Event'] ?? $message['event'] ?? ''));
        $target = $msgType === 'event' ? $event : $content;
        foreach ([(string)$mp->appid, ''] as $appid) {
            $rule = self::matchByAppid($appid, $msgType, $target);
            if ($rule) {
                return $rule;
            }
        }
        return null;
    }

    /**
     * 按 AppID 匹配客服回复规则，空 AppID 表示通用回复
     * @param string $appid
     * @param string $msgType
     * @param string $target
     * @return null|TestMpReply
     */
    private static function matchByAppid(string $appid, string $msgType, string $target): ?TestMpReply
    {
        $default = null;
        foreach (TestMpReply::mk()->where(['appid' => $appid, 'status' => 1])->order('sort desc,id asc')->cursor() as $rule) {
            $ruleMsgType = strtolower((string)$rule->msg_type);
            if ($ruleMsgType !== 'all' && $ruleMsgType !== $msgType) {
                continue;
            }

            $matchType = strtolower((string)$rule->match_type);
            $keyword = trim((string)$rule->keyword);
            if ($matchType === 'default') {
                $default ??= $rule;
                continue;
            }
            if ($target === '' || $keyword === '') {
                continue;
            }
            if ($matchType === 'exact' && $target === $keyword) {
                return $rule;
            }
            if ($matchType === 'contains' && stripos($target, $keyword) !== false) {
                return $rule;
            }
        }
        return $default;
    }

    /**
     * 发送文本客服消息
     * @param TestMp $mp
     * @param string $openid
     * @param string $content
     * @return array
     */
    public static function sendText(TestMp $mp, string $openid, string $content): array
    {
        if ($openid === '' || trim($content) === '') {
            return ['errcode' => 0, 'errmsg' => 'empty message'];
        }

        return self::send($mp, [
            'touser' => $openid,
            'msgtype' => 'text',
            'text' => ['content' => $content],
        ]);
    }

    /**
     * 发送图片客服消息
     * @param TestMp $mp
     * @param string $openid
     * @param string $imageUrl
     * @return array
     */
    public static function sendImage(TestMp $mp, string $openid, string $imageUrl): array
    {
        if ($openid === '' || trim($imageUrl) === '') {
            return ['errcode' => 0, 'errmsg' => 'empty image'];
        }

        $upload = Media::instance(self::config($mp))->upload(self::localFile($imageUrl));
        if (empty($upload['media_id'])) {
            return ['errcode' => -1, 'errmsg' => $upload['errmsg'] ?? 'upload image failed'];
        }

        return self::send($mp, [
            'touser' => $openid,
            'msgtype' => 'image',
            'image' => ['media_id' => $upload['media_id']],
        ]);
    }

    /**
     * 按规则发送客服消息
     * @param TestMp $mp
     * @param string $openid
     * @param TestMpReply $rule
     * @return array
     */
    public static function sendRule(TestMp $mp, string $openid, TestMpReply $rule): array
    {
        $res = match (strtolower((string)$rule->reply_type)) {
            'image' => self::sendImage($mp, $openid, (string)$rule->image_url),
            default => self::sendText($mp, $openid, (string)$rule->content),
        };
        if (isset($res['errcode']) && $res['errcode'] === 0) {
            $rule->inc('reply_count')->save();
        }
        return $res;
    }

    /**
     * 发送小程序客服消息
     * @param TestMp $mp
     * @param array $payload
     * @return array
     */
    private static function send(TestMp $mp, array $payload): array
    {
        $accessToken = Custom::instance(self::config($mp))->getAccessToken();
        $url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token={$accessToken}";
        return Tools::json2arr(Tools::post($url, Tools::arr2json($payload), ['headers' => ['Content-Type: application/json']]));
    }

    /**
     * 获取图片本地文件
     * @param string $imageUrl
     * @return string
     */
    private static function localFile(string $imageUrl): string
    {
        if (is_file($imageUrl)) {
            return $imageUrl;
        }
        $path = parse_url($imageUrl, PHP_URL_PATH) ?: $imageUrl;
        $publicFile = syspath('public' . DIRECTORY_SEPARATOR . ltrim(str_replace('/', DIRECTORY_SEPARATOR, $path), DIRECTORY_SEPARATOR));
        if (is_file($publicFile)) {
            return $publicFile;
        }
        return Storage::down($imageUrl)['file'];
    }

    /**
     * 小程序微信库配置
     * @param TestMp $mp
     * @return array
     */
    public static function config(TestMp $mp): array
    {
        return [
            'appid' => (string)$mp->appid,
            'appsecret' => (string)$mp->appsecret,
            'token' => (string)$mp->token,
            'encodingaeskey' => (string)$mp->encodingaeskey,
            'cache_path' => syspath('runtime/wechat'),
        ];
    }
}

