<?php
declare(strict_types=1);

namespace plugin\test\controller\api\v1;

use plugin\test\model\TestHelp;

/**
 * 帮助中心 API
 * @class Help
 * @package plugin\test\controller\api\v1
 */
class Help extends Base
{
    public function list(): void
    {
        $steps = preg_split('/\r\n|\r|\n/', (string) (sysconf('test.help_steps') ?: "复制短视频分享链接\n回到首页粘贴链接并点击一键解析\n解析完成后复制或保存结果"));
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
                'question' => (string) ($row['question'] ?? ''),
                'answer' => (string) ($row['answer'] ?? ''),
            ];
        }

        $this->success('获取成功', [
            'steps' => array_values(array_filter($steps)),
            'faqs' => $faqs,
        ]);
    }
}

