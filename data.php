<?php

use QL\Ext\PhantomJs;
use QL\QueryList;

require_once 'function.php';
require_once 'vendor/autoload.php';

date_default_timezone_set('Asia/Shanghai');



//使用PhantomJS
// $ql = QueryList::use(PhantomJs::class, 'D:\小插件\phantomjs-2.1.1-windows\bin\phantomjs.exe');
// $html = $ql->browser('https://m.toutiao.com')->getHtml();
// print_r($html);
// exit();
//
// // 采集今日头条手机版
// $data = $ql->browser('https://m.toutiao.com')->find('title')->texts();
// print_r($data->all());die;
//
// echo $ql->browser('http://quote.eastmoney.com/center/hszs.html')->find('#forNewBrowser_Pic .head_type')->html();
//
// var_dump($ql->browser('http://quote.eastmoney.com/center/hszs.html')->find('#forNewBrowser_Pic .head_type')->eq(1)->text());
//
// $zhishu = $ql->browser('http://quote.eastmoney.com/center')->find('#forNewBrowser_Pic .head_type')->map(function($container){
//     echo $container->html();die;
//     $container->find('div.zdp.mt10')->map(function($num){
//         var_dump($num->text()) . PHP_EOL;
//     });
// });

//http://data.eastmoney.com/zjlx/dpzjlx.html 大盘资金流向

// 使用HTTP代理
// $ql->browser('https://m.toutiao.com',false,[
//     '--proxy' => '192.168.1.42:8080',
//     '--proxy-type' => 'http'
// ]);

// js动态更新数据滴，待研究
// $dataCenterUrl = 'http://quote.eastmoney.com/center/hszs.html';
// $filename = date('Y-m-d') . '行情中心.html';
// download($dataCenterUrl, $filename);
// echo file_get_contents($filename);
// exit();

// $query = QueryList::get('http://quote.eastmoney.com/center/hszs.html');
//
// echo $query->find('title')->text();
//
// exit('东方财富网');

// $zhishu = $query->find('#forNewBrowser_Pic .head_type')->map(function($container){
//     $container->find('div.zdp.mt10')->map(function($num){
//         var_dump($num->text()) . PHP_EOL;
//     });
// });


function getResult($url)
{
    $res = file_get_contents($url);
    $return = [];
    foreach(explode(';', $res) as $val) {
        preg_match_all('/.*"(.*)".*/', $val, $out);
        if ($out[1])
            $return[] = explode(',', $out[1][0]);
    }
    return $return;
}

function getTodayData()
{
    $dataDir = 'data/';
    if (!is_dir($dataDir)) mkdir($dataDir, 0777, true);
    $file = date('Y-m-d');
    $filename = $dataDir.$file.'.json';
    // if (file_exists($filename)) {
    //     return json_decode(file_get_contents($filename), true);
    // }
    // $sh = 'http://hq.sinajs.cn/list=s_sh000001';    //上证
    // $sz = 'http://hq.sinajs.cn/list=s_sz399001';    //深证
    // $cyb = 'http://hq.sinajs.cn/list=s_sz399006';  //创业板
    // $hs300 = 'http://hq.sinajs.cn/list=s_sh000300';  //沪深300
    $inland = 'http://hq.sinajs.cn/list=s_sh000001,s_sz399001,s_sz399006';
    // 港股
    // http://hq.sinajs.cn/list=hkHSI,hkHSCEI,hkHSCCI #恒生指数，恒生国企指数，恒生红筹指数
    // $zxc = '';  //中小创
    // $hszs = ''; //恒生指数
    $hk = 'http://hq.sinajs.cn/list=hkHSI,hkHSCEI,hkHSCCI';
    //美国
    $usa = 'http://hq.sinajs.cn/list=int_nasdaq,int_dji,int_sp500';
    // 富时A50
    $a50Url = 'https://hq.sinajs.cn/?list=hf_CHA50CFD';


    // 沪深、创业板指数
    $result = getResult($inland);
    // array (
    //     0 =>
    //         array (
    //             0 => '��ָ֤��',   //名称
    //             1 => '2875.9636',    //点数
    //             2 => '9.4539',   //点数变动
    //             3 => '0.33', //涨跌幅百分比
    //             4 => '3094796',  //成交手数
    //             5 => '36238416', //成交量（万）
    //         ),
    //     1 =>
    //         array (
    //             0 => '��֤��ָ',
    //             1 => '10611.55',
    //             2 => '10.209',
    //             3 => '0.10',
    //             4 => '489619557',
    //             5 => '56720099',
    //         ),
    //     2 =>
    //         array (
    //             0 => '��ҵ��ָ',
    //             1 => '2015.80',
    //             2 => '3.551',
    //             3 => '0.18',
    //             4 => '43371292',
    //             5 => '8553588',
    //         ),
    // )

    $names = [
        'shanghai' => '上证指数',
        'shenzhen' => '深证指数',
        'chuangyeban' => '创业板指数',
        'nasdaq' => '纳斯达克指数',
        'daoqiongsi' => '道琼斯指数',
        'sp500' => '标普500指数',
        'a50' => '富时A50指数',
        'hengsheng' => '恒生指数',
        'hsguoqi' => '恒生国企指数',
        'hshongchou' => '恒生红筹指数',
    ];

    $rows = [];
    list($shanghai, $shenzhen, $chuangyeban) = $result;

    $inlandData = [];
    $inlandData['shanghai'] = formatData([$shanghai[1], $shanghai[3], $shanghai[5]/10000]);
    $inlandData['shenzhen'] = formatData([$shenzhen[1], $shenzhen[3], $shenzhen[5]/10000]);
    $inlandData['chuangyeban'] = formatData([$chuangyeban[1], $chuangyeban[3], $chuangyeban[5]/10000]);

    // 美国纳斯达克、道琼斯、标普
    $result = getResult($usa);
    $usaData = [];
    // array (
    //     0 =>
    //         array (
    //             0 => '��˹���',
    //             1 => '9520.51',
    //             2 => '-51.64',
    //             3 => '-0.54',
    //         ),
    //     1 =>
    //         array (
    //             0 => '����˹',
    //             1 => '29102.51',
    //             2 => '-277.26',
    //             3 => '-0.94',
    //         ),
    //     2 =>
    //         array (
    //             0 => '����ָ��',
    //             1 => '3327.71',
    //             2 => '-18.07',
    //             3 => '-0.54',
    //         ),
    // )
    list($nasdaq, $daoqiongsi, $sp500) = $result;
    $usaData['nasdaq'] = formatData([$nasdaq[1], $nasdaq[3]]);
    $usaData['daoqiongsi'] = formatData([$daoqiongsi[1], $daoqiongsi[3]]);
    $usaData['sp500'] = formatData([$sp500[1], $sp500[3]]);

    // 港指
    $result = getResult($hk);
    // array (
    //     0 =>
    //         array (
    //             0 => 'Hang Seng Index',
    //             1 => '����ָ��',
    //             2 => '27356.28',
    //             3 => '27493.70',
    //             4 => '27410.58',
    //             5 => '27224.12',
    //             6 => '27404.27',
    //             7 => '-89.43',
    //             8 => '-0.33',
    //             9 => '0.000',
    //             10 => '0.000',
    //             11 => '93575038',
    //             12 => '0',
    //             13 => '0.000',
    //             14 => '0.00',
    //             15 => '30280.12',
    //             16 => '24899.93',
    //             17 => '2020/02/07',
    //             18 => '16:08',
    //         ),
    //     1 =>
    //         array (
    //             0 => 'Hang Seng China Enterprises Index',
    //             1 => '�����й���ҵָ��',
    //             2 => '10676.14',
    //             3 => '10764.34',
    //             4 => '10713.16',
    //             5 => '10625.78',
    //             6 => '10705.17',
    //             7 => '-59.17',
    //             8 => '-0.55',
    //             9 => '0.000',
    //             10 => '0.000',
    //             11 => '26106907',
    //             12 => '0',
    //             13 => '0.000',
    //             14 => '0.00',
    //             15 => '11881.68',
    //             16 => '9731.89',
    //             17 => '2020/02/07',
    //             18 => '16:08',
    //         ),
    //     2 =>
    //         array (
    //             0 => 'Hang Seng China-Affiliated Corporations Index',
    //             1 => '�������������ҵָ��',
    //             2 => '4350.25',
    //             3 => '4369.36',
    //             4 => '4350.25',
    //             5 => '4297.15',
    //             6 => '4326.04',
    //             7 => '-43.32',
    //             8 => '-0.99',
    //             9 => '0.000',
    //             10 => '0.000',
    //             11 => '6242485',
    //             12 => '0',
    //             13 => '0.000',
    //             14 => '0.00',
    //             15 => '4788.57',
    //             16 => '3941.07',
    //             17 => '2020/02/07',
    //             18 => '16:08',
    //         ),
    // )
    list($hszs, $gqzs, $hczs) = $result;
    $hkData = [];
    $hkData['hengsheng'] = formatData([$hszs[6], $hszs[8]]);
    $hkData['hsguoqi'] = formatData([$gqzs[6], $gqzs[8]]);
    $hkData['hshongchou'] = formatData([$hczs[6], $hczs[8]]);

    //富时A50
    $a50 = getResult($a50Url)[0];
    // array (
    //     0 => '13203.000',
    //     1 => '',
    //     2 => '13202.500',
    //     3 => '13207.500',
    //     4 => '13370.000',
    //     5 => '13185.000',
    //     6 => '05:14:29',
    //     7 => '13380.000',
    //     8 => '13370.000',
    //     9 => '667783.000',
    //     10 => '8',
    //     11 => '2',
    //     12 => '2020-02-08',
    //     13 => '��ʱ�й�A50ָ��',
    // )
    $a50Data = [];
    $a50Data['a50'] = formatData([$a50[0],($a50[0]-$a50[7])/$a50[4]*100]);

    $data = [];
    $data['date'] = date('Y-m-d');
    $data['data'] = [
        'inland' => $inlandData,
        'usa' => $usaData,
        'hk' => $hkData,
        'a50' => $a50Data,
    ];
    foreach ($data['data'] as $k => &$val) {
        foreach ($val as $kk => &$vv) {
            $vv['name'] = $names[$kk];
        }
    }
    unset($vv, $val);

    file_put_contents($filename, json_encode($data));
    return $data;
}

function formatData($data)
{
    return [
        'end_point' => $data[0],
        'ratio' => number_format($data[1], 2),
        'volume' => !empty($data[2]) ? number_format($data[2], 2) : 0,
    ];
}
/*
美股收盘时间

夏令时收盘时间：清晨4:00（北京时间）;
冬令时收盘时间：清晨5:00（北京时间）。
1、美国道琼斯和纳斯达克股市交易的时间(北京时间) 夏令时间 21:30-04:00 冬令时间 22:30-05:00。
2、美国股市的一天交易时间是不间断的，也就是说“中午不休息”另外美国周五收市之时是在中国周六清晨，在中国周一的晚上美国股市才开始新的一周的交易。

*/

// header('content-type: application/json');
echo json_encode(getTodayData());
exit();


print_r(getResult($usa));
//纳斯达克 道琼斯 标普500
//var hq_str_int_nasdaq="��˹���,9520.51,-51.64,-0.54"; var hq_str_int_dji="����˹,29102.51,-277.26,-0.94"; var hq_str_int_sp500="����ָ��,3327.71,-18.07,-0.54";
die;
print_r(getResult($inland));
$result = 'var hq_str_s_sh000001="��ָ֤��,2875.9636,9.4539,0.33,3094796,36238416"; var hq_str_s_sz399001="��֤��ָ,10611.55,10.209,0.10,489619557,56720099"; var hq_str_s_sz399006="��ҵ��ָ,2015.80,3.551,0.18,43371292,8553588";';
die;


$res = 'var hq_str_s_sz399001="��֤��ָ,10611.55,10.209,0.10,489619557,56720099";';
preg_match_all('/.*"(.*)".*/', $res, $out);
print_r($out);
$result = explode(',', $out[1][0]);
print_r($result);
die;
$data = [
    'sh' => [],
    'sz' => [],
    'cyb' => [],
    'zxb' => [],
    'hszs' => [],
];

$res = file_get_contents($sh);
print_r($res);
$res = file_get_contents($sz);
print_r($res);
die;