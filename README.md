オンライン学習サービスの課題で作成しました。
## これは何か
- コマンドラインのWikipediaのログ解析ツール
- [対象のWikipediaログ 2021年12月1日のもの](https://dumps.wikimedia.org/other/pageviews/2021/2021-12/)
  - [データの全体像の説明](https://dumps.wikimedia.org/other/pageviews/readme.html)
  - [データのテーブル定義](https://wikitech.wikimedia.org/wiki/Analytics/Data_Lake/Traffic/Pageviews)
## 機能
1. 最もビュー数の多い記事を、指定した記事数分だけビュー数が多い順にソートし、ドメインコードとページタイトル、ビュー数を表示する
2. 指定したドメインコードに対して、人気順にソートし、ドメインコード名と合計ビュー数を提示する

## 例
#### 1.指定した記事数を引数で受け取り、結果を表示する
$ php <実行ファイル> 4  
"en.m", "Main_Page", 122058  
"en", "Main_Page", 69181  
"en", "Special:Search", 26630  
"de", "Wikipedia:Hauptseite", 20739  

#### 2.指定したドメインコードもしくはドメインコードのリストを引数で受け取り、結果を表示する
$ php <実行ファイル> en ja de aa  
"en", 973146  
"ja", 141363  
"de", 134395  
"aa", 3  
## 学んだこと
- 既存のデータをMySQLにインポートする方法
- いわゆるN+1問題　ループの中でSQL文を毎回発行しないようなクエリを考える
- githubのREADME.mdを初めて書き、記法について学習になった
- 英語のWebサイトを読み、理解する経験をした
