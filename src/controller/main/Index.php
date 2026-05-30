<?php
declare(strict_types=1);

namespace plugin\base\controller\main;

use plugin\base\model\BaseUser;
use plugin\base\model\BaseTools;
use plugin\base\model\BaseHelp;
use plugin\base\model\BaseMpReply;
use think\admin\Controller;

/**
 * 系统统计
 * @class Index
 * @package plugin\base\controller\main
 */
class Index extends Controller
{
    /**
     * 系统统计
     * @menu true
     * @auth true
     */
    public function index(): void
    {
        $this->title = '系统统计';
        
        // 核心统计指标
        $this->user_count = BaseUser::mk()->cache(true, 10)->count();
        $this->day_user_count = BaseUser::mk()->whereDay('create_at')->cache(true, 10)->count();
        
        $this->tools_count = BaseTools::mk()->cache(true, 10)->count();
        $this->tools_click_count = BaseTools::mk()->cache(true, 10)->sum('click_count');
        
        $this->help_count = BaseHelp::mk()->cache(true, 10)->count();
        $this->reply_count = BaseMpReply::mk()->cache(true, 10)->count();

        // 近半月新增用户趋势
        $this->days = $this->app->cache->get('base_portals_days', []);
        if (empty($this->days)) {
            for ($i = 15; $i >= 0; $i--) {
                $date = date('Y-m-d', strtotime("-{$i}days"));
                $this->days[] = [
                    '当天日期' => date('m-d', strtotime("-{$i}days")),
                    '新增用户' => BaseUser::mk()->whereLike('create_at', "{$date}%")->count(),
                ];
            }
            $this->app->cache->set('base_portals_days', $this->days, 60);
        }

        // 工具点击排行统计数据
        $this->tools_list = BaseTools::mk()->order('click_count desc')->limit(10)->select()->toArray();
        
        $this->fetch();
    }
}
