# ThinkAdmin v6 测试后台及小程序管理插件

`hlw2326/think-plugin-test` 是基于 ThinkAdmin v6 框架开发的高性能小程序辅助插件，提供小程序跳转列表管理、FAQ 帮助中心、广告控制、开发者工具库及多小程序租户用户登录管理等功能。

---

## 1. 核心架构与设计规范

* **命名空间**：`plugin\test`
* **表名前缀**：`test_`
* **接口前缀**：`/plugin-test/api.v1.{controller}/{action}`
* **后台管理地址**：`/admin.html#/plugin-test/{controller}/index`
* **配置前缀**：应用动态配置项储存在 `test.*` 命名空间中。

---

## 2. 目录结构说明

```text
think-plugin-test/
├── src/                                  # 核心源码目录
│   ├── controller/                       # 控制器
│   │   ├── api/v1/                       # 面向小程序的 v1 接口控制器
│   │   ├── config/                       # 后台全局及广告参数配置
│   │   ├── help/                         # 后台帮助中心管理
│   │   ├── main/                         # 后台默认主页
│   │   ├── mp/                           # 后台跳转小程序管理
│   │   ├── reply/                        # 后台小程序快捷回复管理
│   │   ├── tools/                        # 后台工具箱管理
│   │   └── user/                         # 后台注册用户管理
│   ├── model/                            # 数据表对应模型 (TP 模型)
│   ├── service/                          # 系统核心业务层 (如 UserService 等)
│   ├── exception/                        # 自定义异常捕获
│   ├── Service.php                       # 插件服务注册入口
│   └── helper.php                        # 插件辅助函数
├── stc/                                  # 静态资源与安装元数据
│   └── database/                         # 数据库结构与初始化安装迁移脚本
└── README.md                             # 插件调用与开发文档
```

---

## 3. 数据库设计与实体说明

所有数据表均使用 `test_` 作为前缀，以下为主要实体表的设计与描述：

### 1) 注册用户表 (`test_user`)
存储所有小程序的授权用户信息，已全面升级为多租户架构，支持区分不同小程序来源：
* 核心字段包括 `id`, `openid`, `unionid`, `nickname`, `avatar`, `phone`, `status`, `create_at`。
* **`appid`**：已新增该字段，用于记录和关联用户的具体授权小程序来源，支持多小程序统一后台运营管理。

### 2) 工具箱管理表 (`test_tools`)
管理小程序中推荐的各类实用工具链接或功能卡片：
* 包含字段 `id`, `title`, `icon`, `url`, `sort`, `status`, `create_at`。
* **`click_count`**：已新增该字段，用于实时追踪记录该工具在前端小程序被用户点击访问的次数。

### 3) 帮助中心表 (`test_help`)
管理小程序常见问题解答（FAQ）：
* 包含字段 `id`, `title`, `content`, `sort`, `status`, `create_at`。

### 4) 小程序跳转管理表 (`test_mp`)
维护友情跳转小程序列表：
* 包含字段 `id`, `name`, `appid`, `path`, `logo`, `sort`, `status`, `create_at`。

### 5) 自动回复表 (`test_mp_reply`)
配置小程序的客服快捷消息自动回复：
* 包含字段 `id`, `keys`, `type`, `content`, `sort`, `status`, `create_at`。

---

## 4. 小程序 v1 接口列表 (API Surface)

所有接口统一采用 JSON 交互，前缀为 `/plugin-test/api.v1.`。

| 控制器 (Controller) | 接口操作 (Action) | 请求方式 | 说明 |
| :--- | :--- | :--- | :--- |
| **Login** | `index` | POST | 微信授权一键登录。支持传入 `code`, `nickname`, `avatar` 等，并动态关联 **`appid`** 完成注册。 |
| **User** | `get` | GET | 获取当前登录用户的详细个人资料及账户状态。 |
| **User** | `sync` | POST | 同步/更新用户的微信昵称、头像或其它附加元数据。 |
| **Ad** | `index` | GET | 动态拉取配置的广告参数（如 Banner 广告、插屏广告、激励视频等 ID 与开启状态）。 |
| **Config** | `index` | GET | 获取系统公共环境与动态配置。 |
| **Custom** | `index` | GET | 用于自定义广告位与高级 fallback 广告 ID 查询。 |
| **Help** | `index` | GET | 分页获取 FAQ 列表以渲染帮助与客服中心页面。 |
| **Tools** | `index` | GET | 获取激活状态的工具箱列表（按 `sort` 排序）。 |
| **Tools** | `click` | POST | 增加对应工具卡片的 **`click_count`** 访问计数器。 |
| **Upload** | `image` / `file` | POST | 统一的多媒体/静态文件上传接口，支持本地或云存储。 |

---

## 5. 近期重大更新亮点 (Recent Updates)

1. **多小程序租户隔离 (`appid` 引入)**：
   * 在数据库 `test_user` 中增加了 `appid` 字段，并在登录注册逻辑（`Login::index`）与用户资料同步流程中实现动态追踪，使同一个插件系统完美支持多小程序用户的隔离与统计。
2. **工具点击热度数据分析 (`click_count` 引入)**：
   * 工具表 `test_tools` 新增了 `click_count` 字段。API 新增 `Tools::click` 行为动作，实时统计并分析各模块受用户欢迎的程度，供后台可视化数据报表使用。
3. **后台管理搜索 UX 升级**：
   * 重新优化了快捷回复（`reply`）等管理后台的搜索排版，将搜索筛选器归纳至极具质感的 `<fieldset>` 样式块中，规范了界面一致性并防范页面错位。
4. **源码合规性清理 (BOM 剥离)**：
   * 全面清理了该插件下所有 17 个后台 HTML 模板视图文件中的 UTF-8 BOM 字节标志，消除了 Web 渲染中可能产生的空白间隙与解析异常隐患。
