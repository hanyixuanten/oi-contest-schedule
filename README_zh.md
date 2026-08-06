# OI Contest Schedule

[English](README.md)

OI Contest Schedule 是一款轻量级 WordPress 插件，可在 WordPress 仪表盘、文章或页面中展示即将开始的程序设计竞赛。

比赛数据由 [OI-contest-fetch](https://github.com/hanyixuanten/OI-contest-fetch) 提供，时间会根据访客设备的时区显示。

## 功能特性

- 在 WordPress 仪表盘中添加“即将到来的 OI 赛事”小组件。
- 提供 `[oi_contest_schedule]` 短代码，可插入文章或页面。
- 展示比赛平台、标题、开始和结束时间，以及实时倒计时。
- 自动标记正在进行的比赛。
- 支持响应式布局和紧凑布局。
- 使用 WordPress Transients API 缓存远程比赛数据五分钟。
- 内置英文和简体中文翻译。

## 环境要求

- WordPress 6.4 或更高版本
- PHP 7.4 或更高版本
- 服务器能够通过 HTTPS 访问 `raw.githubusercontent.com`

## 安装

### 安装发行包

1. 从仓库的 [Releases](https://github.com/hanyixuanten/oi-contest-schedule/releases) 页面下载最新的插件 ZIP 包。
2. 在 WordPress 后台进入 **插件 > 安装插件 > 上传插件**。
3. 选择 ZIP 文件，然后安装并启用插件。

### 从源码安装

1. 将本仓库复制或克隆到 `wp-content/plugins/oi-contest-schedule`。
2. 在 WordPress 后台的 **插件** 页面启用 **OI Contest Schedule**。

启用后，赛事小组件会自动显示在 **仪表盘 > 首页** 中。

## 短代码

在文章或页面中添加“短代码”区块，然后输入：

```text
[oi_contest_schedule]
```

短代码支持以下属性：

| 属性 | 默认值 | 说明 |
| --- | --- | --- |
| `limit` | `10` | 显示的比赛数量，取值范围限制为 1-50。 |
| `compact` | `false` | 设置为 `true` 时使用紧凑布局。 |

示例：

```text
[oi_contest_schedule limit="20" compact="true"]
```

## 数据与缓存

插件从 [OI-contest-fetch](https://github.com/hanyixuanten/OI-contest-fetch) 维护的公开 JSON 数据源获取比赛信息，仅展示链接有效且正在进行或尚未开始的比赛，并按照开始时间排序。

规范化后的数据会缓存五分钟，卸载插件时会删除该缓存。站点开发者可以通过 `oics_contest_data_url` 过滤器替换数据源地址：

```php
add_filter(
    'oics_contest_data_url',
    static function () {
        return 'https://example.com/contests.json';
    }
);
```

替换后的接口需要返回与默认数据源相同的 JSON 结构。

## 开发与构建

本插件不依赖 Composer 或 npm。构建安装包前，请确保系统中已安装 `grep`、`sed`、GNU gettext 的 `msgfmt`、`zip` 和 `unzip`，然后运行：

```bash
./build.sh build
```

该命令会检查翻译和安装包内容，并在仓库根目录生成 `oi-contest-schedule-<版本>.zip`。清理本地构建产物可运行：

```bash
./build.sh clean
```

## 项目结构

```text
oi-contest-schedule.php   插件入口与元数据
includes/                 数据客户端、渲染器和插件集成
assets/                   前端样式与倒计时脚本
languages/                翻译模板与中文翻译
uninstall.php             卸载时清理缓存
build.sh                  发布包构建脚本
```

## 许可证

本项目基于 [GNU General Public License v3.0](LICENSE) 发布。
