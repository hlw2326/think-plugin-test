<?php
declare(strict_types=1);

namespace plugin\test\controller\api\v1;

use plugin\test\model\TestMp;
use think\admin\Controller;

/**
 * 接口基础 API
 * @class Base
 * @package plugin\test\controller\api\v1
 */
class Base extends Controller
{
    protected string $appid = '';

    protected TestMp $mp;

    protected array $device = [];

    protected function initialize(): void
    {
        parent::initialize();

        [$state, $msg, $data] = verify_sig($this->request);
        if (!$state) {
            $this->error($msg, $data);
        }

        $this->appid = $this->request->header('X-Appid', '') ?: $this->request->get('appid', '');
        if ($this->appid === '') {
            $this->error('缺少 appid 参数');
        }

        $mp = TestMp::mk()->where(['appid' => $this->appid, 'status' => 1])->findOrEmpty();
        if ($mp->isEmpty()) {
            $this->error('无效的 appid');
        }
        $this->mp = $mp;

        $this->device = [
            'app_name' => $this->request->get('app_name', ''),
            'app_version' => $this->request->get('app_version', ''),
            'app_version_code' => $this->request->get('app_version_code', ''),
            'app_channel' => $this->request->get('app_channel', ''),
            'device_brand' => $this->request->get('device_brand', ''),
            'device_model' => $this->request->get('device_model', ''),
            'device_id' => $this->request->get('device_id', ''),
            'device_type' => $this->request->get('device_type', ''),
            'device_orientation' => $this->request->get('device_orientation', ''),
            'device_system' => $this->request->get('system', ''),
            'os' => $this->request->get('os', ''),
            'screen_width' => intval($this->request->get('screen_width', 0)),
            'screen_height' => intval($this->request->get('screen_height', 0)),
            'window_width' => intval($this->request->get('window_width', 0)),
            'window_height' => intval($this->request->get('window_height', 0)),
            'pixel_ratio' => floatval($this->request->get('pixel_ratio', 0)),
            'status_bar_height' => intval($this->request->get('status_bar_height', 0)),
            'sdk_version' => $this->request->get('sdk_version', ''),
            'host_name' => $this->request->get('host_name', ''),
            'host_version' => $this->request->get('host_version', ''),
            'platform' => $this->request->get('platform', ''),
            'language' => $this->request->get('language', ''),
            'brand' => $this->request->get('brand', ''),
            'model' => $this->request->get('model', ''),
        ];
    }
}


