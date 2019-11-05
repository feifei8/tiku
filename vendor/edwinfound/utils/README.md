<h1 align="center"> PHP工具包</h1>
<p align="center">
<a href="https://packagist.org/packages/edwinfound/utils"><img src="https://poser.pugx.org/edwinfound/utils/v/stable.svg" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/edwinfound/utils"><img src="https://poser.pugx.org/edwinfound/utils/v/unstable.svg" alt="Latest Unstable Version"></a>
<a href="https://travis-ci.org/edwinfound/utils"><img src="https://travis-ci.org/edwinfound/utils.svg?branch=master" alt="Build Status"></a>
<a href="https://packagist.org/packages/edwinfound/utils"><img src="https://poser.pugx.org/edwinfound/utils/downloads" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/edwinfound/utils"><img src="https://poser.pugx.org/edwinfound/utils/license" alt="License"></a>
</p>

## 环境要求

1. PHP >= 5.5.9
2. **[composer](https://getcomposer.org/)**

## 安装

```shell
composer require "edwinfound/utils:dev-master" -vvv
```

> 说明：为了避免引入太多冗余包，部分安装包没有依赖，在实际使用具体Util时，可按需安装。

## 文档

### 过滤器|Filter

- save($file) : 序列化保存到文件中 
- restore($file) : 从文件中恢复
- add($key) : 添加一个Key
- has($key) : 检测是否包含一个Key

目前支持 `BloomFilter`、 `ArrayFilter`，需注意，[BloomFilter](https://en.wikipedia.org/wiki/Bloom_filter) 不是一个完全可靠的可去重算法。

示例：

```php
$filter = BloomFilter::build(10000);
// 或 $filter = ArrayFilter::build();
$filter->add('key1'); 
$filter->exists('key1'); // true
$filter->exists('key2'); // false
```

### 数组|Array

- `ArrayUtil::equal($arr1, $arr2, $keys = null, $strict = false)` : 判断两个一维数组是否完全相同
- `ArrayUtil::fetchSpecifiedKeyToArray(&$arr, $key)` : 用一个数组的某一个key重新生成一个一维数组
- `ArrayUtil::pickRandomOne($arr)` : 从数组中随机抽取一个

### 编码|Encode

- `EncodeUtil::expiredData($string, $key, $expireSeconds = 3600)` : 根据key生成一个会过期的加密字符串
- `EncodeUtil::expiredDataDecode($url, $key)` : 根据key解密一个加密的字符串

### 文件|File

- `FileUtil::mime($type)` : 获取mine信息
- `FileUtil::extension($pathname)` : 获取后缀信息

### 格式|Format

- `FormatUtil::telephone($number)` : 过滤方式获取中国手机/电话号码

### 图片|Image

- `ImageUtil::base64Src($imageContent, $type = 'png')` : 根据图片内容生成base64的src链接
- `ImageUtil::limitSizeAndDetectOrientation($path, $maxWidth = 1000, $maxHeight = 1000)` : 根据图片路径检测图片的旋转方向和限定像素大小

### 抽奖|Lottery

- `LotteryUtil::fetchPoll($pool)` : 根据给定的概率抽取奖品
- `LotteryUtil::randomMoneyInRange($min, $max)` : 生成随机金额

### 二维码|Qrcode

> 需要引入包 `composer require bacon/bacon-qr-code -vvv`

- `QrcodeUtil::png($content,$size=200)` : 生成二维码

### 文件|File

- `FileUtil::mime($type)` : 根据类型获取MIME
- `FileUtil::extension($type)` : 获取文件后缀（小写）

### 随机|Random

- `RandomUtil::number($length)` : 返回一个长度为Length的随机 `数字` 串
- `RandomUtil::string($length)` : 返回一个长度为Length的随机 `字符` 串
- `RandomUtil::hexString($length)` : 返回一个长度为Length的随机 `十六进制型字符` 串
- `RandomUtil::lowerString($length)` : 返回一个长度为Length的随机 `小写字符` 串
- `RandomUtil::upperString($length)` : 返回一个长度为Length的随机 `大写字符` 串
- `RandomUtil::lowerChar($length)` : 返回一个长度为Length的随机 `小写字母` 串
- `RandomUtil::upperChar($length)` : 返回一个长度为Length的随机 `大写字母` 串


### 字符串|Str

- `StrUtil::sn()` : 生成一个22位长度的订单号
- `StrUtil::mask($subject, $startIndex = null, $endIndex = null, $maskChar = '*')` : 对字符串掩码
- `StrUtil::passwordStrength($password)` : 计算密码强度
- `StrUtil::camelize($uncamelized_words, $separator = '_')` : 驼峰字符串变为下划线字符串
- `StrUtil::uncamelize($camelCaps, $separator = '_')` : 下划线字符串变为驼峰字符串

## 协议

MIT