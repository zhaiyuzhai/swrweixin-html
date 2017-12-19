<?php
require_once "jssdk.php";
$jssdk = new JSSDK("wx066c52aa969fca5d", "fea9651ccbd34c7797edb7d2b6d0cd6c");
$signPackage = $jssdk->GetSignPackage();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <title>Swr</title>
    <style>
        /*取消移动端的点击事件的高亮背景色*/
        *{
            -webkit-tap-highlight-color: transparent;
        }
        @font-face {
            font-family: "font-Awesome";
            src: url("font/fontawesome-webfont.ttf");
        }
        body,p{
            margin:0px;
        }
        span{
            -webkit-tap-highlight-color: transparent;
        }
        .container{
            position:absolute;
            top:0px;
            left:0px;
            right:0px;
            bottom:0px;
            background-color: #75D1F8;
        }
        .temInside{
            position: relative;
            text-align: center;
            margin:0px auto;
            margin-top:15%;
            width:55%;
            /*height:55%;*/
            border-radius: 50%;
            background-color: #fff;
            box-shadow: 0 0 0 5px #75D1F8,0 0 0 10px #DCF3FD,0 0 0 15px #75D1F8,0 0 0 18px #9ADDF9;
        }
        .title{
            line-height: 4rem;
            text-align: center;
            font-size: 2rem;
            color:#8A8A8A;
        }
        .temShow{
            font-size: 350%;
            color:#faed00;
            display: block;
            position:absolute;
            top:50%;
            left:50%;
            transform: translate(-50%,-50%);
        }
        .icon{
            color:black;
            position: absolute;
            font-family: "font-Awesome";
            display: block;
            color:#091012;
        }
        .icon_tem{
            bottom:20px;
            left:23%;
            font-size: 30px;
        }
        .icon_tem:before {
            content: "\f2c9";
        }
        .icon_search{
            top: 10px;
            right: 20px;
            font-size: 30px;
            color:#fff;
        }
        .icon_search:before{
            content: "\f002";
        }
        .icon_chart{
            bottom:20px;
            right:23%;
            font-size: 30px;
        }
        .icon_chart:before {
            content: "\f201";
        }
        .icon_refresh{
            left:90%;
            top:40%;
            font-size: 30px;
            color: #e4393c;
        }
        .icon_refresh:before{
            content: "\f021";
        }
        /*主要绘图区的容器*/
        #charts{
            visibility: visible;
            margin:0 auto;
            margin-top:10%;
            width:100%;
            height:45%;
            /*background-color: #0BB20C;*/
        }
        /*温度计的图片*/
        #img{
            position: absolute;
            top:40%;
            left:50%;
            transform: translate(-50%,0%);
            width:73px;
            height:300px;
            background: url("img/tem.png") no-repeat;
            background-size: 100% 100%;
        }
        .well{
            border-radius: 4px;
            width:40%;
            height:0px;
            /*min-height: 20px;*/
            position: absolute;
            right:10px;
            top:50px;
            box-shadow: 0 0 10px 0px #8A8A8A;
            background-color: #ffffff;
            overflow: hidden;
            transition: all .3s linear .5s;
        }
    </style>

</head>
<body>
    <div class="container">
        <div class="temInside">
            <p class="title"></p>
            <p class="temShow"><span id="temShow_Num"></span><span id="temShow_unit"></span></p>
        </div>
        <div id="charts"></div>
<!--        <div id="img"></div>-->
        <span class="icon icon_search" id="search"></span>
        <span class="icon icon_refresh" id="refresh"></span>
<!--        <span class="icon icon_tem" id="btn_img"></span>-->
<!--        <span class="icon icon_chart" id="btn_charts"></span>-->
        <div class="well"></div>
    </div>
    <script src="js/zepto.min.js"></script>
    <script src="js/echarts.common.min.js"></script>
    <!--/*引入jssdk来实现功能*/-->
    <script src="http://res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>
    <!--引入base64来对接收的数据进行解码-->
<!--    <script src="js/base64.min.js"></script>-->
    <script>
        var base64={
            // 将字节数组转换为base64字符串
            arrayToBase64:function (array) {
                if(!(array instanceof Array)){
                    throw new Error("please enter Array like [0x11,0x22]");
                }
                // 首先判断字符串的长度，如果为零就返回
                if (array.length == 0) {
                    return "";
                }
                // base64的可打印字符串，共有64个字符
                var b64Chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/';
                var result = "";
                //根据base64的规则，每三个字节一转；
                // 给末尾添加的字符,先计算出后面的字符
                var d3 = array.length % 3;
                var endChar = "";
                if (d3 == 1) {
                    var value = array[array.length - 1];
                    endChar = b64Chars.charAt(value >> 2);
                    endChar += b64Chars.charAt((value << 4) & 0x3F);
                    endChar += "==";
                } else if (d3 == 2) {
                    var value1 = array[array.length - 2];
                    var value2 = array[array.length - 1];
                    endChar = b64Chars.charAt(value1 >> 2);
                    endChar += b64Chars.charAt(((value1 << 4) & 0x3F) + (value2 >> 4));
                    endChar += b64Chars.charAt((value2 << 2) & 0x3F);
                    endChar += "=";
                }
                var times = array.length / 3;
                var startIndex = 0;
                // 开始计算
                for (var i = 0; i < times - (d3 == 0 ? 0 : 1); i++) {
                    startIndex = i * 3;
                    var S1 = array[startIndex + 0];
                    var S2 = array[startIndex + 1];
                    var S3 = array[startIndex + 2];

                    var s1 = b64Chars.charAt(S1 >> 2);
                    var s2 = b64Chars.charAt(((S1 << 4) & 0x3F) + (S2 >> 4));
                    var s3 = b64Chars.charAt(((S2 & 0xF) << 2) + (S3 >> 6));
                    var s4 = b64Chars.charAt(S3 & 0x3F);
                    // 添加到结果字符串中
                    result += (s1 + s2 + s3 + s4);
                }
                return result + endChar;
            },
            base64ToString:function (str) {
                var base64DecodeChars = [-1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
                    -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
                    -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 62, -1, -1, -1, 63,
                    52, 53, 54, 55, 56, 57, 58, 59, 60, 61, -1, -1, -1, -1, -1, -1,
                    -1,  0,  1,  2,  3,  4,  5,  6,  7,  8,  9, 10, 11, 12, 13, 14,
                    15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, -1, -1, -1, -1, -1,
                    -1, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40,
                    41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, -1, -1, -1, -1, -1];
                var c1, c2, c3, c4;
                var i, len, out;
                len = str.length;
                i = 0;
                out = "";
                while(i < len) {

                    do {
                        c1 = base64DecodeChars[str.charCodeAt(i++) & 0xff];
                    } while(i < len && c1 == -1);
                    if(c1 == -1)
                        break;

                    do {
                        c2 = base64DecodeChars[str.charCodeAt(i++) & 0xff];
                    } while(i < len && c2 == -1);
                    if(c2 == -1)
                        break;
                    out += String.fromCharCode((c1 << 2) | ((c2 & 0x30) >> 4));

                    do {
                        c3 = str.charCodeAt(i++) & 0xff;
                        if(c3 == 61)
                            return out;
                        c3 = base64DecodeChars[c3];
                    } while(i < len && c3 == -1);
                    if(c3 == -1)
                        break;
                    out += String.fromCharCode(((c2 & 0XF) << 4) | ((c3 & 0x3C) >> 2));

                    do {
                        c4 = str.charCodeAt(i++) & 0xff;
                        if(c4 == 61)
                            return out;
                        c4 = base64DecodeChars[c4];
                    } while(i < len && c4 == -1);
                    if(c4 == -1)
                        break;
                    out += String.fromCharCode(((c3 & 0x03) << 6) | c4);
                }
                return out;
            },
            stringToArray:function (str) {
                var len=str.length;
                var buffer=[];
                for(var i=0;i<len;i++){
                    buffer.push(str[i].charCodeAt());
                }
                return buffer;
            },
            base64ToArray:function (str) {
                var res=this.base64ToString(str);
                return this.stringToArray(res);
            }
        }
    </script>
    <!--    将页面上方图设置为正方形，然后才可以加圆角-->
    <script>
        /*****************************************/
        /*发送数据到硬件设备,直接输入,写成数组形式*/
        function sendData(arr,bol) {
            /*封装了处理十六字节数组转换为base64的方法*/
            function orderArray(arr) {
                //FE 01 00 0F 75 31 00 00 0A 00 12 01 57 18 00
                var Bytes = new Array();
                for (var i = 0; i < arr.length; i++) {
                    Bytes[i] = arr[i];
                }
                return Bytes;
            }

            /*根据微信官方文档说明，发送的指令数据必须是base64编码，所以还必须有个转换方法。
            *  Byte数组转Base64字符,原理同上
            * @Param [0x00,0x00]
            * @return Base64字符串
            **/
            function arrayToBase64(array) {
                if (array.length == 0) {
                    return "";
                }
                var b64Chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/';
                var result = "";
                // 给末尾添加的字符,先计算出后面的字符
                var d3 = array.length % 3;
                var endChar = "";
                if (d3 == 1) {
                    var value = array[array.length - 1];
                    endChar = b64Chars.charAt(value >> 2);
                    endChar += b64Chars.charAt((value << 4) & 0x3F);
                    endChar += "==";
                } else if (d3 == 2) {
                    var value1 = array[array.length - 2];
                    var value2 = array[array.length - 1];
                    endChar = b64Chars.charAt(value1 >> 2);
                    endChar += b64Chars.charAt(((value1 << 4) & 0x3F) + (value2 >> 4));
                    endChar += b64Chars.charAt((value2 << 2) & 0x3F);
                    endChar += "=";
                }
                var times = array.length / 3;
                var startIndex = 0;
                // 开始计算
                for (var i = 0; i < times - (d3 == 0 ? 0 : 1); i++) {
                    startIndex = i * 3;
                    var S1 = array[startIndex + 0];
                    var S2 = array[startIndex + 1];
                    var S3 = array[startIndex + 2];

                    var s1 = b64Chars.charAt(S1 >> 2);
                    var s2 = b64Chars.charAt(((S1 << 4) & 0x3F) + (S2 >> 4));
                    var s3 = b64Chars.charAt(((S2 & 0xF) << 2) + (S3 >> 6));
                    var s4 = b64Chars.charAt(S3 & 0x3F);
                    // 添加到结果字符串中
                    result += (s1 + s2 + s3 + s4);
                }
                return result + endChar;
            }

            var base64Data = arrayToBase64(orderArray(arr));
            if (bol) {
                for (var i = 0; i < deviceId.length; i++) {
                    wx.invoke('sendDataToWXDevice', {
                        'deviceId': deviceId[i],
                        'base64Data': base64Data
                    }, function (res) {
                    });
                }
            } else {
                wx.invoke('sendDataToWXDevice', {'deviceId': deviceId[0], 'base64Data': base64Data}, function (res) {
                });
            }
        }
        /*/!*
        *对接收来的数据进行解析
        *传入的buffer参数必须为数组
        *!/
        function analysis(buffer) {
            var cmd=parseInt(buffer[0]);
            switch (cmd){
                case 0xB2:
                    //目前只考虑单通道，单AD值得方式
                    var sensorId=parseInt(buffer[4]);
                    var bufferLength=parseInt(buffer[5]);
                    var dataBuffer=buffer.slice(6,6+bufferLength);
                    var dataPool=[];
                    for(var i=0;i<bufferLength;i+=2){
                        dataPool.push(parseInt(dataBuffer[i+1])*256+parseInt(dataBuffer[i]));
                    }
                    return {
                        type:0xB2,
                        dataPool:dataPool,
                        sensorId:sensorId
                    };
                    break;
                case 0xBD:
                    var info=buffer.slice(6,8).map(function (val) {
                        return val.toString(16).toUpperCase()
                    }).join('');
                    info=info.length==4?info:'0'+info;
                    //返回的是一串字符串
                    return {
                        type:0xBD,
                        //deviceID在微信客户端目前只提供最后4位的数字。
                        deviceID:info
                    };
                    break;
            }
        }
        //采用闭包的方式来让saveArr作为存储的媒介
        var spliceBuffer=function() {
            var saveArr=saveArr?saveArr:[];
            return function (res) {
                var arr=base64.base64ToArray(res.base64Data);//获取到的是数字字符串的数组
//                alert('arr:'+arr);
                if(saveArr.length==0){
                    var length=parseInt(arr[1]);//获取到此条数据的长度
                }
                if(length==arr.length){
                    var data = analysis(arr);
                    return data;
                }else{
                    saveArr=saveArr.concat(arr);
//                    alert(saveArr)
                    if(length==saveArr.length){
                        var data=analysis(saveArr);
                        saveArr =null;
                        return data;
                    }else if(saveArr.length>length){
                        saveArr=arr;
                    }
                }
            }
        }();*/

        // 定义了一些工具方法
        var util={
            freList:{
                "1ms":0x14,
                "2ms":0x19,
                "5ms":0x02,
                "10ms":0x03,
                "20ms":0x04,
                "50ms":0x05,
                "100ms":0x06,
                "200ms":0x07,
                "500ms":0x08,
                "1s":0x09,
                "2s":0x0A,
                "5s":0x0B,
                "10s":0x0C,
                "20s":0x0D,
                "1min":0x0E,
                "2min":0x0F,
                "5min":0x10,
                "10min":0x11,
                "20min":0x12,
                "1hour":0x13
            },
            bufferToArray(buffer){
                return Array.from(buffer.values());
            },
            addZero(str){
                // 只接受字符串
                str=String(str);
                return str.length==2?str:"0"+str;
            },
            arrayToUpCaseString(arr){
                return 	arr.map(function ( value ) {
                    return this.addZero(value.toString(16)).toUpperCase();
                }).join("");
            },
            /*在electron里面的
        发送数据的方法*/
            bufferData(str) {
                return new Buffer(str,"hex");
            },
            parse2Bytes(arr){
                return (arr[1]<<8)+arr[0];
            },
            parse3Bytes(arr){
                var num=(arr[2]<<16)+(arr[1]<<8)+arr[0];
                return num>=0x800000?(num-0x800000):-(num);
            },
            parse4Bytes(arr){
                return (arr[3]<<24)+(arr[2]<<16)+(arr[1]<<8)+arr[0];
            },
            getTime(){
                var year,mon,date,hour,min,sec,str;
                year=(new Date().getFullYear()-2000).toString(16);
                mon=(new Date().getMonth()+1).toString(16);
                date=(new Date().getDate()).toString(16);
                hour=(new Date().getHours()).toString(16);
                min=(new Date().getMinutes()).toString(16);
                sec=(new Date().getSeconds()).toString(16);
                str="AE08"+this.addZero(year)+this.addZero(mon)+this.addZero(date)+this.addZero(hour)+this.addZero(min)+this.addZero(sec);
                return this.bufferData(str);
            },
            setOfflineTask(delay,lastTime,fre){
                var {delayH,delayM,delayS}=delay;
                var {lastH,lastM,lastS}=lastTime;
                delayH=delayH?delayH:0;
                delayM=delayM?delayM:0;
                delayS=delayS?delayS:0;
                lastH=lastH?lastH:0;
                lastM=lastM?lastM:0;
                lastS=lastS?lastS:0;
                var year,mon,date,hour,min,sec,str;
                year=(new Date().getFullYear()-2000).toString(16);
                mon=(new Date().getMonth()+1).toString(16);
                date=(new Date().getDate()).toString(16);
                hour=(new Date().getHours()+delayH).toString(16);
                min=(new Date().getMinutes()+delayM).toString(16);
                sec=(new Date().getSeconds()+delayS).toString(16);
                str="CA0C"+this.addZero(year)+this.addZero(mon)+this.addZero(date)+this.addZero(hour)+this.addZero(min)+this.addZero(sec)+this.addZero(lastH)+this.addZero(lastM)+this.addZero(lastS)+this.addZero(fre);
                // console.log(str)
                return this.bufferData(str);
            }
        }

        // 存储了一些传感器的信息
        var map={};

        /*定义了一些解析的函数方法
        包在对象里面*/
        var analysis={
            table:{
                // 用来存储读取dat数据的东西
            },
            sensorTypeList:{
                // sensorID:type
                1:{
                    type:"K",
                    unit:"Kpa",
                    en:"Pressure"
                },
                3:{
                    type:"K",
                    unit:"mT",
                    en:"Magnetic Field"
                },
                18:{
                    type:"K",
                    unit:"mA",
                    en:"Current"
                },
                20:{
                    type:"K",
                    unit:"",
                    en:"pH"
                },
                30:{
                    type:"Value-100",
                    unit:"℃",
                    en:"Temperature"
                },
                31:{
                    type:"Value-10",
                    unit:"%",
                    en:"Humidity"
                },
                60:{
                    type:"K",
                    unit:"V",
                    en:"Voltage"
                },
                93:{
                    // 溶解氧
                    type:"Table",
                    unit:"",
                    en:""
                }
            },
            flag:false,
            // 进行拼接的函数
            spliceBuffer:function() {
                var saveArr=saveArr?saveArr:[];
                var length;
                return function (arr) {

                    // 假如saveArr有长度的话
                    if(saveArr.length==0){
                        length=parseInt(arr[1]);//获取到此条数据的长度
                    }
                    /*分为两种情况：
                    1、长度等于实际长度的情况
                    2、长度不等于实际长度的情况*/
                    if(length==arr.length){
                        return arr;
                    }else{
                        saveArr=saveArr.concat(arr);
                        if(length==saveArr.length){
                            return saveArr.splice(0);
                        }else if(saveArr.length>length){
                            saveArr.splice(0);
                            return arguments.callee(arr)
                        }
                    }
                }
            }(),
            // 经过拼接后的数组再进行解析
            bufferToCategory:function (arr) {
                var cmd=parseInt(arr[0]),
                    output={};
                /*根据不同的下位机的命令
                来进行解析*/
                switch (cmd){
                    // 读取设备的信息
                    case 0xBD:
                        /*{
                            mac:"",
                            id:"",
                            firmware:"",
                            battery:"",
                            memoryLeft:"",
                            freList:""
                        }*/
                        output.type="deviceInfo";
                        output.mac=util.arrayToUpCaseString(arr.slice(2,8));
                        output.id=util.arrayToUpCaseString(arr.slice(8,16));
                        output.firmware=util.arrayToUpCaseString(arr.slice(16,20).reverse());
                        output.battery=arr.slice(20,21)[0];
                        output.memoryLeft=arr.slice(21,22)[0];
                        output.freList=arr.slice(22);
                        // 采集的频率需要查表
                        return output;
                        break;
                    // 连续采集的返回的数据
                    case 0xB2:
                    case 0xD3:
                        output.type=cmd===0xD3?"offlineData":"continusData";
                        // 每个输出的量为两个字节

                        /*{
                            index:1,
                            channelNum:2,
                            data:{
                                channel1:{},
                                channel2:{}
                            }
                        }*/
                        /*	单条B2的数据的话，解析
                            1、头部是信息，加总长度
                            2、倒数的4个字节是数据的下标
                            3、去头去尾后数据区*/
                        var dataBuffer,
                            channelLen,
                            channelList,//通道的数组列表
                            dataList=[],//二维数组
                            indexBuffer;
                        indexBuffer=arr.slice(arr.length-4);
                        /*包含如下信息：
                        1、通道数量
                        2、具体通道列表
                        3、各个通道数据区域
                        */
                        dataBuffer=arr.slice(2,arr.length-4);
                        output.index=(indexBuffer[3]<<24)+(indexBuffer[2]<<16)+(indexBuffer[1]<<8)+(indexBuffer[0]);
                        output.channelNum=dataBuffer.splice(0,1)[0];
                        channelLen=output.channelNum;
                        channelList=dataBuffer.splice(0,channelLen);
                        while(dataBuffer.length){
                            dataList.push(dataBuffer.splice(0,2+dataBuffer[1]))
                        }
                        // 至此，dataBuffer已经为空
                        output.data={};
                        // 循环遍历通道
                        for(var i=0;i<channelLen;i++){
                            output.data["channel"+channelList[i]]={};
                            output.data["channel"+channelList[i]]["sensorID"]=dataList[i].splice(0,1)[0];
                            // 加入判断sensorID，然后进行解析
                            if(output.data["channel"+channelList[i]]["sensorID"]===93){
                                // 对溶解氧传感器的解析
                                for(let dataLen=dataList[i].splice(0,1)[0]/4,j=0;j<dataLen;j++){
                                    var cache=dataList[i].splice(0,4);
                                    output.data["channel"+channelList[i]].value?output.data["channel"+channelList[i]].value:output.data["channel"+channelList[i]].value=[];
                                    output.data["channel"+channelList[i]].value.push([(cache[1]<<8)+cache[0],(cache[3]<<8)+cache[2]]);
                                }
                            }else if(false){

                            }else{
                                /*根据传感器的sensorID来计算每次需要截取的字节长度
                                    目前都是2个字节计算一个value值
                                    只有光强是根据4个字节来计算一个 */
                                for(let dataLen=dataList[i].splice(0,1)[0]/2,j=0;j<dataLen;j++){
                                    var cache=dataList[i].splice(0,2);
                                    output.data["channel"+channelList[i]].value?output.data["channel"+channelList[i]].value:output.data["channel"+channelList[i]].value=[];
                                    output.data["channel"+channelList[i]].value.push((cache[1]<<8)+cache[0]);
                                }
                            }
                        }
                        return output;
                        break;
                    // 单次采集的返回数据
                    case 0xB3:
                        output.type="singleData";
                        var dataBuffer,
                            channelLen,
                            channelList,
                            dataList=[],//二维数组
                            indexBuffer;
                        indexBuffer=arr.slice(arr.length-4);
                        /*包含如下信息：
                        1、通道数量
                        2、具体通道列表
                        3、各个通道数据区域
                        */
                        dataBuffer=arr.slice(2);
                        output.channelNum=dataBuffer.splice(0,1)[0];
                        channelLen=output.channelNum;
                        channelList=dataBuffer.splice(0,channelLen);
                        while(dataBuffer.length){
                            dataList.push(dataBuffer.splice(0,2+dataBuffer[1]))
                        }
                        // 至此，dataBuffer已经为空
                        output.data={};
                        for(let i=0;i<channelLen;i++){
                            output.data["channel"+channelList[i]]={};
                            output.data["channel"+channelList[i]]["sensorID"]=dataList[i].splice(0,1)[0];
                            // output.data["channel"+channelList[i]]["length"]=dataList[i].splice(0,1)[0];
                            for(let dataLen=dataList[i].splice(0,1)[0]/2,j=0;j<dataLen;j++){
                                /*根据传感器的sensorID来计算每次需要截取的字节长度
                                目前都是2个字节计算一个value值
                                只有光强是根据4个字节来计算一个 */
                                var cache=dataList[i].splice(0,2);
                                output.data["channel"+channelList[i]]["value"+(j+1)]=(cache[1]<<8)+cache[0];
                            }
                        }
                        return output;
                        break;
                    // 返回传感器的信息
                    case 0xB5:
                        output.type="sensorInfo";
                        var dataBuffer=arr.slice(2);
                        output.channel=dataBuffer[0];
                        output.sensorID=dataBuffer[1];
                        output.decimal=dataBuffer[2];
                        output.offsetAD=util.parse2Bytes(dataBuffer.slice(3,5));
                        output.lowAD=util.parse2Bytes(dataBuffer.slice(5,7));
                        output.highAD=util.parse2Bytes(dataBuffer.slice(7,9));
                        output.lowValue=util.parse3Bytes(dataBuffer.slice(9,12));
                        output.highValue=util.parse3Bytes(dataBuffer.slice(12,15));
                        output.minValue=util.parse3Bytes(dataBuffer.slice(15,18))/Math.pow(10,dataBuffer[2]);
                        output.maxValue=util.parse3Bytes(dataBuffer.slice(18,21))/Math.pow(10,dataBuffer[2]);

                        var sen=dataBuffer[1];
                        console.log(this.sensorTypeList[sen].type)
                        // 根据传感器的sensorID的类型来判别是否需要计算K值还是其他处理
                        if(this.sensorTypeList[sen].type==="K"){
                            // 需要计算K值得情况
                            if(output.highAD==output.lowAD){
                                output.k=1;
                            }else{
                                output.K=(output.highValue-output.lowValue)/(output.highAD-output.lowAD);
                            }
                        }
                        map[dataBuffer[1]]=output;
                        break;
                    /*创建离线实验
                    在创建离线实验之前，首先发送时间信息给设备*/
                    case 0xDA:
                        output.type="setupOfflineData"
                        if(arr[2]==1){
                            output.result=true
                        }else if(arr[2]==2){
                            output.result=false;
                            output.msg="memory is not enough";
                        }
                        break;
                    // 读取已完成的离线实验列表
                    case 0xD2:
                        output.type="readOfflineList";
                        output.NO=arr[2];
                        output.startTime=arr.slice(3,9);
                        output.continueTime=arr.slice(9,12);
                        output.fre=arr[arr.length-1];
                        break;
                    // 标志着离线实验的开始,结束的标志
                    case 0xA0:
                        output.type="offlineStatus";
                        var offlineDataLength=arr.slice(4);
                        if(arr[3]==0x01){
                            output.offlineStatus="start";
                        }
                        if(arr[3]==0x00){
                            output.offlineStatus="finished"
                        }
                        output.dataLength=(offlineDataLength[3]<<24)+(offlineDataLength[2]<<16)+(offlineDataLength[1]<<8)+(offlineDataLength[0]);
                        break;
                    // 读取定时离线实验信息
                    case 0xD4:
                        output.type="readTimingOfflineInfo";
                        var timingOfflineInfo=arr.slice(2);
                        output.startTime=timingOfflineInfo.slice(0,6);
                        output.continueTime=timingOfflineInfo.slice(6,9);
                        output.fre=timingOfflineInfo[9];
                        break;
                    // 读取默认离线实验信息
                    case 0xD5:
                        output.type="readDefaultOfflineInfo"
                        var defaultOfflineInfo=arr.slice(2);
                        output.continueTime=defaultOfflineInfo.slice(0,3);
                        output.fre=defaultOfflineInfo[3];
                        break;
                    // 删除离线实验
                    case 0xDE:
                        output.type="deleteOffline";
                        output.result=true;
                        break;
                    // 删除全部离线实验
                    case 0xDF:
                        output.type="deleteAllOffline"
                        output.result=true;
                        break;
                }
                return output;
            },
            /*解析完成后的对象需要进行计算
            其函数主要针对的是B2类的信息*/
            categoryToData:function (output) {
                /*提取解析完成的数据然后进行解析
                找到里面的data数据
                {
                    channel1:{
                        sensorID:"",
                        value:[111,222,333]
                    }
                    channel2:{}
                }*/
                var dataReg=/^value$/;
                var {data}=output;
                Object.keys(data).forEach(function ( value ) { //此value的值是channel
                    var valueList=data[value].value;//valueList是每一个channel里面的value值
                    // 首先判断那个sensorID的计算方式
                    var sensorID=data[value].sensorID;
                    var type=analysis.sensorTypeList[sensorID].type;
                    // 能够进行计算的前提是必须要获取B5信息
                    if(type==="K"&& map.hasOwnProperty(sensorID)){
                        for(let index in valueList){
                            var rawData=((valueList[index]-map[sensorID].offsetAD)*map[sensorID].K+map[sensorID].lowValue)/Math.pow(10,map[sensorID].decimal);
                            valueList[index]=rawData.toFixed(map[sensorID].decimal)>map[sensorID].maxValue?map[sensorID].maxValue:rawData.toFixed(map[sensorID].decimal)<=map[sensorID].minValue?map[sensorID].minValue:rawData.toFixed(map[sensorID].decimal);
                        }
                    }
                    else if(type==="Value-100" && map.hasOwnProperty(sensorID)){
                        console.log("tem")
                        // 假如是直接计算类型的话，就用值除以100
                        for(let index in valueList){
                            var rawData=valueList[index]/100;
                            valueList[index]=rawData.toFixed(map[sensorID].decimal)>map[sensorID].maxValue?map[sensorID].maxValue:rawData.toFixed(map[sensorID].decimal)<=map[sensorID].minValue?map[sensorID].minValue:rawData.toFixed(map[sensorID].decimal);
                        }
                    }
                    else if(type==="Value-10" && map.hasOwnProperty(sensorID)){
                        for(let index in valueList){
                            var rawData=valueList[index]/10;
                            valueList[index]=rawData.toFixed(map[sensorID].decimal)>map[sensorID].maxValue?map[sensorID].maxValue:rawData.toFixed(map[sensorID].decimal)<=map[sensorID].minValue?map[sensorID].minValue:rawData.toFixed(map[sensorID].decimal);
                        }
                    }else if(type==="Table" && map.hasOwnProperty(sensorID)){
                        /*查表的操作需要参照写的USB的墨西哥协议来解释
                        引用fs的模块来读取dat的数据文件然后以换行符个回车符分割开来组成数组*/
                    }else{
                        output=undefined;
                    }

                })
                return output;
            }
        }
        /*****************************************/
        var wid=$(".temInside").width();
        $(".temInside").height(wid);
//        按钮的逻辑实现
        $("#refresh").on("click",function () {
            chartData.splice(0);
            xTime=0;
            $("#refresh").css({
                "transition":"all .5s linear",
                "transform":"rotate(360deg)"
            })
            $("#refresh").on("transitionend",function () {
                $("#refresh").css({
                    "transition":"none",
                    "transform":"rotate(0deg)"
                })
            })
        });
    </script>
<!--    绘制Echarts-->
    <script>
        var chartData=[];
        var xTime=0;
        var myChart = echarts.init(document.getElementById('charts'));
        var option = {
//            tooltip:{
//                show:true,
//                trigger:"axis",
//                axisPointer:{
//                    type:"line",
//                    lineStyle:{
//                        color:"#fff"
//                    }
//                }
//            },
            gird:{
                containLabel:true,
                left:"10%",
                right:"3%"
            },
            dataZoom: [
                {
                    id: 'dataZoomX',
                    type: 'inside',
                    xAxisIndex: [0]
                }
            ],
            xAxis:{
//                axisLine:{onZero:false},
                type: 'value',
//                boundaryGap: [0, '50%'],
                name:"ms"
            },
            yAxis: {
//                axisLine:{onZero:false},
                type: 'value',
//                boundaryGap: [0, '90%'],
                name:"温度/℃"
            },
            series:{
                type: 'line',
                showSymbol: false,
                data:chartData
            }
        }
        myChart.showLoading();
    </script>
    <script>
        // 可用来存储微信的deviceID
        var deviceId=[];

        var sensorId=[];

        var sensorIdList={
            //温度，ID:30 单位：℃ 量程最小值：-40 量程最大值：125 小数位数：2位
            30:"°C",
            //ID:60 单位：V量程最小值：-30 量程最大值：30 小数位数：2位
            60:'V',
            //电流，ID:18 单位：mA 量程最小值：-1000 量程最大值：1000 小数位数：0位
            18:'mA',
            //ID:3 单位：mT 量程最小值：-64 量程最大值：64 小数位数：2位
            3:'mT',
            //湿度，ID:31单位：% 量程最小值：0 量程最大值：100 小数位数：1位
            31:'%',
            //ph值，ID:20 单位：无 量程最小值：0 量程最大值：14 小数位数：2位
            20:'',
            //压强，ID:1 单位：kPa 量程最小值：0 量程最大值：400 小数位数：1位
            1:'Kpa'
        }
        var titleList={
            30:"温度",
            60:'电压',
            18:'电流',
            3:'磁场',
            31:'湿度',
            20:'PH',
            1:'压强'
        }
//        微信配置信息
        wx.config({
            beta:true,
//            debug: true,
            appId: '<?php echo $signPackage["appId"];?>',
            timestamp: <?php echo $signPackage["timestamp"];?>,
            nonceStr: '<?php echo $signPackage["nonceStr"];?>',
            signature: '<?php echo $signPackage["signature"];?>',
            jsApiList: [
                // 所有要调用的 API 都要加到这个列表中
                // jsAPI硬件部分
                'openWXDeviceLib',
                'closeWXDeviceLib',
                'getWXDeviceInfos',
                'sendDataToWXDevice',
                'startScanWXDevice',
                'stopScanWXDevice',
                'connectWXDevice',
                'disconnectWXDevice',
                'getWXDeviceTicket',
                'onWXDeviceBindStateChange',
                'onWXDeviceStateChange',
                'onReceiveDataFromWXDevice'
            ]
        });
        //        微信配置完成后的执行函数，介于wx.config是异步的
        wx.ready(function () {
            // 在这里调用 API
        //  1、  打开设备的硬件功能
            wx.invoke('openWXDeviceLib',{'brandUserName':'gh_e09af6572b88'}, function(res){
//                alert("open")
                //这里是回调函数
                if(res.isSupportBLE=="no"){
                    alert("您的设备不支持此蓝牙设备");
                }
                if(res.bluetoothState=='unauthorized'){
                    alert("请您授权设备的蓝牙功能，并打开");
                }
//                获取设备的信息，deviceid值
                wx.invoke('getWXDeviceInfos', {}, function(res){
                    var len=res.deviceInfos.length;  //绑定设备总数量
                    if(len===0){
                        alert("您还没有绑定设备！")
                    }else {
                        for (var i = 0; i < len; i++) {
                            if (res.deviceInfos[i].state === "connected") {
                                deviceId.push(res.deviceInfos[i].deviceId);
                            }
                        }
                        //设定频率
                        sendData([0xA8,0x03,0x07]);
                        setTimeout(function () {
                            sendData([0xA5,0x02]);
                            setTimeout(function () {
                                //连续采集
                                sendData([0xA2,0x03,0x01]);
                            },200)
                        },200)
                    }
                });
            });
        //  当设备接收到数据的时候，返回给HTML页面，jsapi。
            wx.on('onReceiveDataFromWXDevice',function(res){
                /*
                处理逻辑的问题，一开始放在回调函数里面解决问题，后来单独放到外面的spliceBuffer函数里面
                 */
//                var saveArr=saveArr?saveArr:[];
                myChart.hideLoading();
                /*1、首先将传过来的base64的字符串准换成为arr数组形式
                2、开始拼接数组
                3、如果有返回值的话就解析
                4、解析完成后进行计算*/



                var arr=base64.base64ToArray(res.base64Data);
                var newArr=analysis.spliceBuffer(arr);
                if(newArr){
                    var next=analysis.bufferToCategory(newArray);
                    if(next.type==="continusData"){
                        // 得到最后的解析数据
                        var final=analysis.categoryToData(next);
                        var value=final.data.channel1.value,
                            sensorID=final.data.channel1.sensorID;
                    /*final={
                        channelNum:1
                        data:{
                            channel1:{
                                sensorID:1,
                                value:[102.1,103.2]
                            }
                        },
                        index:1,
                        type:"continusData"
                    }*/
                        $("#temShow_Num").html(value[0]);
                        $("#temShow_unit").html(sensorIdList[sensorID]);
                        $(".title").html(titleList[sensorID]);
                        for (let j = 0, len = value.length; j < len; j++, xTime += 200) {
                            chartData.push([xTime, value[j]]);
                        }
                        myChart.setOption(option);
                    }
                }


                /*
                if(data.type==0xB2){
                    var getNum = data.dataPool;
                    var sensorId = data.sensorId;
                    $("#temShow_Num").html((getNum[getNum.length - 1]/100).toFixed(1));
                    $("#temShow_unit").html(sensorIdList[sensorId]);
                    $(".title").html(titleList[sensorId]);
                    for (var j = 0, len = getNum.length; j < len; j++, xTime += 200) {
                        chartData.push([xTime, (getNum[j]/100).toFixed(1)]);
                    }
                    myChart.setOption(option);
                }*/
            });
        });
    </script>
</body>
</html>