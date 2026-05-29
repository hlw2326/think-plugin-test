<?php
declare(strict_types=1);

namespace plugin\test\controller\api\v1;

use plugin\test\model\TestHelp;

/**
 * 帮助列表 API
 * @class Help
 * @package plugin\test\controller\api\v1
 */
class Help extends Base
{
    public function list(): void
    {
        $steps = preg_split('/\r\n|\r|\n/', (string) (sysconf('test.help_steps') ?: ""));
        $steps = array_values(array_filter(array_map(static fn(string $step): string => trim($step), $steps ?: [])));

        $rows = TestHelp::mk()
            ->field('id,type,question,answer,sort,status')
            ->where('status', 1)
            ->where('type', 'faq')
            ->order('sort desc, id asc')
            ->select()
            ->toArray();

        $faqs = [];

        foreach ($rows as $row) {
            $faqs[] = [
                'id' => (int) ($row['id'] ?? 0),
                'question' => (string) ($row['question'] ?? ''),
                'answer' => (string) ($row['answer'] ?? ''),
            ];
        }

        $this->success('获取成功', [
            'steps' => array_values(array_filter($steps)),
            'faqs' => $faqs,
        ]);
    }

    /**
     * 记录帮助点击次数
     * @return void
     */
    public function click(): void
    {
        $id = (int)$this->request->post('id', 0);
        if ($id > 0) {
            TestHelp::mk()->where(['id' => $id, 'status' => 1])->inc('click_count')->save();
        }
        $this->success('操作成功');
    }
}

