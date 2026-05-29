<?php
declare(strict_types=1);

namespace plugin\test\controller\api\v1;

use plugin\test\model\TestTools;

/**
 * 工具列表 API
 * @class Tools
 * @package plugin\test\controller\api\v1
 */
class Tools extends Base
{
    public function list(): void
    {
        $rows = TestTools::mk()
            ->field('id,title,desc,logo,appid,path,click_count,sort,status')
            ->where('status', 1)
            ->order('sort desc, id asc')
            ->select()
            ->toArray();

        $list = array_map(static fn(array $row): array => [
            'id' => intval($row['id']),
            'title' => (string) $row['title'],
            'desc' => (string) $row['desc'],
            'logo' => (string) $row['logo'],
            'appid' => (string) $row['appid'],
            'path' => (string) $row['path'],
            'clickCount' => intval($row['click_count']),
            'sort' => intval($row['sort']),
            'status' => intval($row['status']),
        ], $rows);

        $this->success('获取成功', ['list' => $list]);
    }

    public function click(): void
    {
        $id = intval($this->request->get('id', $this->request->post('id', 0)));
        if ($id <= 0) {
            $this->error('工具 ID 不能为空');
        }

        TestTools::mk()->where(['id' => $id, 'status' => 1])->inc('click_count')->update();
        $this->success('记录成功');
    }
}

