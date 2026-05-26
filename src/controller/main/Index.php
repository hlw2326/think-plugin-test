<?php
declare(strict_types=1);

namespace plugin\test\controller\main;

use plugin\test\model\TestUser;
use plugin\test\model\TestTools;
use plugin\test\model\TestHelp;
use plugin\test\model\TestMpReply;
use think\admin\Controller;

/**
 * 系统统计
 * @class Index
 * @package plugin\test\controller\main
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
        $this->user_count = TestUser::mk()->cache(true, 10)->count();
        $this->day_user_count = TestUser::mk()->whereDay('create_at')->cache(true, 10)->count();
        
        $this->tools_count = TestTools::mk()->cache(true, 10)->count();
        $this->tools_click_count = TestTools::mk()->cache(true, 10)->sum('click_count');
        
        $this->help_count = TestHelp::mk()->cache(true, 10)->count();
        $this->reply_count = TestMpReply::mk()->cache(true, 10)->count();

        // 近半月新增用户趋势
        $this->days = $this->app->cache->get('test_portals_days', []);
        if (empty($this->days)) {
            for ($i = 15; $i >= 0; $i--) {
                $date = date('Y-m-d', strtotime("-{$i}days"));
                $this->days[] = [
                    '当天日期' => date('m-d', strtotime("-{$i}days")),
                    '新增用户' => TestUser::mk()->whereLike('create_at', "{$date}%")->count(),
                ];
            }
            $this->app->cache->set('test_portals_days', $this->days, 60);
        }

        // 工具点击排行统计数据
        $this->tools_list = TestTools::mk()->order('click_count desc')->limit(10)->select()->toArray();
        
        $this->fetch();
    }
}
