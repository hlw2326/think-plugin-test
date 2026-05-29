<?php
declare(strict_types=1);

namespace plugin\test\service;

use plugin\test\model\TestUser;

/**
 * 用户服务
 */
class UserService
{
    public static function profile(TestUser $user): array
    {
        return [
            'id' => intval($user->id),
            'nickname' => (string) $user->nickname,
            'avatar_url' => (string) $user->avatar_url,
            'phone' => (string) $user->phone,
            'vip_time' => intval($user->vip_time),
            'gender' => intval($user->gender),
            'birthday' => (string) ($user->birthday ?? ''),
            'region' => (string) ($user->region ?? ''),
            'signature' => (string) ($user->signature ?? ''),
        ];
    }

    public static function sync(
        string $openid,
        string $unionid,
        array $profile,
        array $device,
        string $ip,
        string $inviteUid = '',
        string $appid = ''
    ): TestUser {
        $user = TestUser::mk()->where('openid', $openid)->findOrEmpty();

        if ($user->isEmpty()) {
            return static::register($openid, $unionid, $profile, $device, $ip, $inviteUid, $appid);
        }

        return static::refresh($user, $unionid, $device, $ip);
    }

    private static function register(
        string $openid,
        string $unionid,
        array $profile,
        array $device,
        string $ip,
        string $inviteUid,
        string $appid
    ): TestUser {
        $pid = 0;
        $inviteUserId = intval($inviteUid);
        if ($inviteUserId > 0) {
            $inviter = TestUser::mk()->where(['id' => $inviteUserId, 'deleted' => 0, 'status' => 1])->findOrEmpty();
            if ($inviter->isExists()) {
                $pid = intval($inviter->id);
            }
        }

        $user = TestUser::mk();
        $user->save([
            'openid' => $openid,
            'appid' => $appid,
            'pid' => $pid,
            'unionid' => $unionid,
            'nickname' => $profile['nickname'] ?? '',
            'avatar_url' => $profile['avatar_url'] ?? '',
            'device_model' => $device['device_model'] ?? '',
            'device_system' => $device['device_system'] ?? '',
            'screen_width' => intval($device['screen_width'] ?? 0),
            'screen_height' => intval($device['screen_height'] ?? 0),
            'sdk_version' => $device['sdk_version'] ?? '',
            'app_version' => $device['app_version'] ?? '',
            'app_channel' => $device['app_channel'] ?? '',
            'login_ip' => $ip,
            'login_at' => date('Y-m-d H:i:s'),
            'status' => 1,
        ]);

        return $user;
    }

    private static function refresh(TestUser $user, string $unionid, array $device, string $ip): TestUser
    {
        if (intval($user->status) !== 1) {
            throw new \RuntimeException('账号已被禁用');
        }

        $update = [
            'last_login_ip' => $user->login_ip,
            'last_login_at' => $user->login_at,
            'login_ip' => $ip,
            'login_at' => date('Y-m-d H:i:s'),
            'device_model' => $device['device_model'] ?? '',
            'device_system' => $device['device_system'] ?? '',
            'screen_width' => intval($device['screen_width'] ?? 0),
            'screen_height' => intval($device['screen_height'] ?? 0),
            'sdk_version' => $device['sdk_version'] ?? '',
            'app_version' => $device['app_version'] ?? '',
            'app_channel' => $device['app_channel'] ?? '',
        ];

        if ($unionid !== '' && empty($user->unionid)) {
            $update['unionid'] = $unionid;
        }

        $user->save($update);
        return $user;
    }
}
