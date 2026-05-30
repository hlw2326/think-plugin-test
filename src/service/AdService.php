<?php
declare(strict_types=1);

namespace plugin\base\service;

use plugin\base\model\BaseMp;
use plugin\base\model\BaseUser;

/**
 * 广告服务
 */
class AdService
{
    public static function mpConfig(BaseMp $mp): array
    {
        $globalOn = self::enabled($mp, 'ad_global_enabled');

        $unit = static function (string $type) use ($mp, $globalOn): string {
            if (!$globalOn) {
                return '';
            }
            if (!self::enabled($mp, "ad_enabled_{$type}")) {
                return '';
            }
            return (string) ($mp->{"{$type}_unit_id"} ?? '');
        };

        return [
            'ad_global_enabled' => $globalOn ? 1 : 0,
            'ad_enabled_banner' => self::enabled($mp, 'ad_enabled_banner') ? 1 : 0,
            'ad_enabled_grid' => self::enabled($mp, 'ad_enabled_grid') ? 1 : 0,
            'ad_enabled_custom' => self::enabled($mp, 'ad_enabled_custom') ? 1 : 0,
            'ad_enabled_video' => self::enabled($mp, 'ad_enabled_video') ? 1 : 0,
            'ad_enabled_reward' => self::enabled($mp, 'ad_enabled_reward') ? 1 : 0,
            'ad_enabled_popup' => self::enabled($mp, 'ad_enabled_popup') ? 1 : 0,
            'banner_unit_id' => $unit('banner'),
            'grid_unit_id' => $unit('grid'),
            'custom_unit_id' => $unit('custom'),
            'video_unit_id' => $unit('video'),
            'reward_unit_id' => $unit('reward'),
            'popup_unit_id' => $unit('popup'),
            'vip_no_ad' => (int) ($mp->vip_no_ad ?? 0),
        ];
    }

    public static function grant(int $userId): array
    {
        $user = BaseUser::mk()->where('id', $userId)->find();

        if (empty($user)) {
            return ['state' => false, 'msg' => '发放失败'];
        }

        return [
            'state' => true,
            'msg' => '观看广告奖励已发放',
            'data' => [
                'reward' => 1,
                'balance' => 0,
            ],
        ];
    }

    private static function enabled(BaseMp $mp, string $field): bool
    {
        $value = $mp->{$field} ?? 1;
        if ($value === '' || $value === null) {
            return true;
        }
        return intval($value) === 1;
    }
}
