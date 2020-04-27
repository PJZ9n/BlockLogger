# BlockLogger

[![](https://img.shields.io/badge/license-GNU%20General%20Public%20License%20v3.0-yellow)](https://www.gnu.org/licenses/gpl-3.0.html)

## Overview
Language:
  - [日本語](#日本語)
  - [English](#english)

## 日本語
シンプルで高パフォーマンスなブロックロガープラグイン

### 特徴
- データベースは非同期で処理されます
- 単純で分かりやすいシステム
- 複数言語に対応

### サポートされている機能
- [x] ブロックの破壊/設置ログを記録
- [x] ブロックの破壊/設置ログを取得
- [ ] MySQLを使用(現時点では、SQLiteのみ使用できます)
- [ ] Form UIベースでの管理
- [ ] 強化された絞り込み機能

### 使い方
#### ログを取得する
以下のコマンドを実行して、ログ取得モードをオンにする

`/checklog on [limit]`

ログを確認したい場所のブロックを破壊するか、ログを確認したい場所にブロックを設置する。
確認が完了したら以下のコマンドを実行してログ取得モードをオフにする

`/checklog off`

#### ログに記録する
単にブロックを破壊もしくは設置するだけです！

もちろん、イベントがキャンセルされた場合はログに記録されません。

### コマンド
| コマンド名 | 説明 | 使い方 | 権限 | エイリアス | プレイヤーのみ |
| --- | --- | --- | --- | --- | --- |
| checklog | ブロックのログを確認する | /checklog on [limit=1] または /checklog off | blocklogger.command.checklog | cl | はい |

### 権限

| 権限 | 説明 | デフォルト |
| --- | --- | --- |
| blocklogger.command.checklog | /checklog コマンド用の権限 | OP |

### 構成ファイル (config.yml)
例:
```yaml
# 使用する言語(使用可能な言語は[plugin_data]/locale/ディレクトリを参照)
language: jpn

# 起動時にアップデートを確認するか
# 起動時に外部との通信が発生するため、不必要な場合は無効にしてください。
check-update: true

# 言語ファイルを自動でアップデートするか
# この機能が有効になっていると、プラグインの起動機に言語ファイルが上書きされます。
# 言語ファイルの開発者ではない限り、有効にしてください。
language-update: true

# データベースの設定
# この設定は、自分が何をしているのか理解できる場合のみ変更してください。
database:
  type: sqlite
  sqlite:
    # データベースのファイル名
    file: log.db
  worker-limit: 1
```
豆知識: データベースの設定は [ここ](https://github.com/poggit/libasynql/tree/a1bd607263d8c933668ec3b74dff204b49b7cabf#configuration) も参考にしてください。

## English
Simple and high performance block logger plugin

### Features
- Database is processed asynchronously
- Simple and easy-to-understand system
- Supports multiple languages

### Supported features
- [x] Record block break / place log
- [x] Get block break / place log
- [ ] Use MySQL (only SQLite is available at this time)
- [ ] Form UI based management
- [ ] Enhanced narrowing function

### How to use
#### Get the log

Turn on the log acquisition mode by executing the following command

`/checklog on [limit]`


Destroy the block where you want to check the log, or install the block where you want to check the log.
After the confirmation is completed, execute the following command to turn off the log acquisition mode.

`/checklog off`

#### Record the log
Simply break or place blocks!

Of course, if the event is canceled it will not be logged.

### Command
| label | description | usage | permission | alias | only player |
| --- | --- | --- | --- | --- | --- |
| checklog | Check the block log | /checklog on [limit=1] or /checklog off | blocklogger.command.checklog | cl | yes |

### Permission

| Permission | Description | Default |
| --- | --- | --- |
| blocklogger.command.checklog | Permission for /checklog command | OP |

### Configuration file (config.yml)
Example:
```yaml
# Language to use (see [plugin_data]/locale/ directory for available languages)
language: eng

# Check for updates on startup
# Since communication with the outside occurs at startup, disable it if unnecessary.
check-update: true

# Whether to update language files automatically
# If this feature is enabled, the language file will be overwritten on the plugin launcher.
# Unless you are the developer of the language file, enable it.
language-update: true

# Database settings
# Only change this setting if you understand what you are doing.
database:
  type: sqlite
  sqlite:
    # Database file name
    file: log.db
  worker-limit: 1
```
Tips: For database settings, please refer to [here](https://github.com/poggit/libasynql/tree/a1bd607263d8c933668ec3b74dff204b49b7cabf#configuration) as well.