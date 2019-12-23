<h1 align="center"> biaoQingBao </h1>

<p align="center">根据关键字爬取斗图啦网站的表情包</p>


## Installing

```shell
$ composer require xiaobinqt/biao-qing-bao -vvv
```

## Usage
+ 使用
    + $guzzleOptions 非必须参数，是请求时需要的一些配置，比如我公司的网走了代理，就需要配置一个 proxy。
```
$keyWorks = "我艹";
$guzzleOptions = array(
    'proxy' => [
        'http'  => '127.0.0.1:12639',
        'https' => '127.0.0.1:12639'
    ]
);
$bqb = new \Xiaobinqt\BiaoQingBao\BiaoQingBao($keyWorks, $guzzleOptions);
$rs = $bqb->getEmojiList();
echo $rs;
```
+ 返回结构
    + 出错
        ```
        {
          "error": -1,
          "msg": "cURL error 7: Failed to connect to www.doutula.com port 443: Timed out (see https:\/\/curl.haxx.se\/libcurl\/c\/libcurl-errors.html)",
          "data": []
        }
        ```
    + 正确
        ```
           {
             "error": -1,
             "msg": "cURL error 7: Failed to connect to www.doutula.com port 443: Timed out (see https:\/\/curl.haxx.se\/libcurl\/c\/libcurl-errors.html)",
             "data": [
               "http://ww1.sinaimg.cn/bmiddle/9150e4e5gy1g2x4gj710cj209q09p0sr.jpg",
               "http://ww2.sinaimg.cn/bmiddle/9150e4e5gy1g59e69khv7j209c05jaa0.jpg",
               "http://ww1.sinaimg.cn/bmiddle/9150e4e5gy1g62dk501cxj206o06oaa4.jpg",
               "http://ww4.sinaimg.cn/bmiddle/9150e4e5ly1fsozdquwofj20qo0eyadd.jpg"
             ]
           }
        ```  



## Contributing

You can contribute in one of three ways:

1. File bug reports using the [issue tracker](https://github.com/xiaobinqt/biaoQingBao/issues).
2. Answer questions or fix bugs on the [issue tracker](https://github.com/xiaobinqt/biaoQingBao/issues).
3. Contribute new features or update the wiki.

_The code contribution process is not very formal. You just need to make sure that you follow the PSR-0, PSR-1, and PSR-2 coding guidelines. Any new code contributions must be accompanied by unit tests where applicable._

## License

MIT