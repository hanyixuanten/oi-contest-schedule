# OI Contest Schedule WordPress 插件

该子项目将 OI 赛程数据封装为 WordPress 插件。目录和发布方式参考 `wp-translate`：可安装插件位于同名 slug 目录中，`build.sh` 在 `build/` 下生成带版本号的 ZIP 包。

## 功能

- 在 WordPress 仪表盘添加“即将到来的 OI 赛事”小组件。
- 提供 `[oi_contest_schedule]` 短代码，可插入文章或页面。
- 根据访客浏览器时区显示比赛时间。
- 显示实时倒计时，并标记正在进行的比赛。
- 使用 WordPress transient 将 GitHub 上的公开 JSON 数据缓存五分钟。

## 环境要求

- WordPress 6.4 或更新版本
- PHP 7.4 或更新版本

## 使用方式

启用插件后，在页面中添加“短代码”区块并输入：

```text
[oi_contest_schedule]
```

`limit` 可设置 1 到 50 条比赛，`compact` 可启用紧凑布局：

```text
[oi_contest_schedule limit="20" compact="true"]
```

## 构建

在本目录运行：

```bash
chmod +x build.sh
./build.sh build
```

安装包将生成到 `build/oi-contest-schedule-<版本>.zip`。