# 環境構築

1. Dockerを起動する

2. プロジェクト直下で、以下のコマンドを実行する

```
make init
```

※Makefileは実行するコマンドを省略することができる便利な設定ファイルです。コマンドの入力を効率的に行えるようになります。<br>

## メール認証
mailfogというツールを使用しています。<br>
https://localhost:8025　<br>

上記アドレスにアクセスし基本的操作を行ってください。　

## テーブル仕様
### adminsテーブル
| カラム名 | 型 | primary key | unique key | not null | foreign key |
| --- | --- | --- | --- | --- | --- |
| id | bigint | ◯ |  | ◯ |  |
| name | varchar(255) |  |  | ◯ |  |
| email | varchar(255) |  | ◯ | ◯ |  |
| password | varchar(255) |  |  | ◯ |  |
| created_at | timestamp |  |  |  |  |
| updated_at | timestamp |  |  |  |  |

### staffテーブル
| カラム名 | 型 | primary key | unique key | not null | foreign key |
| --- | --- | --- | --- | --- | --- |
| id | bigint | ◯ |  | ◯ |  |
| name | varchar(255) |  |  | ◯ |  |
| email | varchar(255) |  | ◯ | ◯ |  |
| email_verfied_at | timestamp |  |  |  |  |
| remember_token | varchar(100) |  |  |  |  |
| password | varchar(255) |  |  | ◯ |  |
| created_at | timestamp |  |  |  |  |
| updated_at | timestamp |  |  |  |  |

### attendancesテーブル
| カラム名 | 型 | primary key | unique key | not null | foreign key |
| --- | --- | --- | --- | --- | --- |
| id | bigint | ◯ |  | ◯ |  |
| staff_id | bigint |  |  | ◯ | staff(id) |
| work_date | date |  |  | ◯ |  |
| clock_in | time |  |  |  |  |
| clock_out | time |  |  |  |  |
| total_work_minutes | varchar(255) |  |  |  |  |
| created_at | timestamp |  |  |  |  |
| updated_at | timestamp |  |  |  |  |

### applicationsテーブル
| カラム名 | 型 | primary key | unique key | not null | foreign key |
| --- | --- | --- | --- | --- | --- |
| id | bigint | ◯ |  | ◯ |  |
| staff_id | bigint |  |  | ◯ | staff(id) |
| attendance_id | bigint |  |  | ◯ | attendances(id) |
| new_clock_in | time |  |  |  |  |
| new_clock_out | time |  |  |  |  |
| reason | varchar(255) |  |  |  |  |
| status | enum |  |  |  | ◯ |
| created_at | timestamp |  |  |  |  |
| updated_at | timestamp |  |  |  |  |

### work_breaksテーブル
| カラム名 | 型 | primary key | unique key | not null | foreign key |
| --- | --- | --- | --- | --- | --- |
| id | bigint | ◯ |  | ◯ |  |
| attendance_id | bigint |  |  | ◯ | attendances(id) |
| break1_start | time |  |  |  |  |
| break1_end | time |  |  |  |  |
| break2_start | time |  |  |  |  |
| break1_end | time |  |  |  |  |
| created_at | timestamp |  |  |  |  |
| updated_at | timestamp |  |  |  |  |

### application_breaksテーブル
| カラム名 | 型 | primary key | unique key | not null | foreign key |
| --- | --- | --- | --- | --- | --- |
| id | bigint | ◯ |  | ◯ |  |
| application_id | bigint |  |  | ◯ | applications(id) |
| new_break1_start | time |  |  |  |  |
| new_break1_end | time |  |  |  |  |
| new_break2_start | time |  |  |  |  |
| new_break1_end | time |  |  |  |  |
| created_at | created_at |  |  |  |  |
| updated_at | updated_at |  |  |  |  |

## テストアカウント
name: 一般ユーザ  
email: aaa@gmail.com  
password: test1234  
-------------------------
name: 管理者  
email: admin@gmail.com  
password: pass1234  
-------------------------



