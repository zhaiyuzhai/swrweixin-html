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
            visibility: hidden;
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
    </style>
    <script src="js/zepto.min.js"></script>
</head>
<body>
    <div class="container">
        <div class="temInside">
            <p class="title">温度</p>
            <p class="temShow"><span id="temShow_Num">30.0</span>℃</p>
        </div>
        <div id="charts"></div>
        <div id="img"></div>
        <span class="icon icon_refresh" id="refresh"></span>
        <span class="icon icon_tem" id="btn_img"></span>
        <span class="icon icon_chart" id="btn_charts"></span>
    </div>

    <script src="js/echarts.common.min.js"></script>
    <!--    将页面上方图设置为正方形，然后才可以加圆角-->
    <script>
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
        $("#btn_img").on("click",function () {
            $("#charts").css({
                "visibility":"hidden"
            })
            $("#img").css({
                "visibility":"visible"
            });
        });
        $("#btn_charts").on("click",function () {

            $("#charts").css({
                "visibility":"visible"
            })
            $("#img").css({
                "visibility":"hidden"
            })
        });
    </script>
<!--    绘制charts-->
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
//        setInterval(function(){
//            myChart.hideLoading();
//            xTime+=200;
//            chartData.push([xTime,22]);
//            myChart.setOption(option);
//        },1000);
    </script>
    <!--/*引入jssdk来实现功能*/-->
    <script src="http://res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>
    <!--引入base64来对接收的数据进行解码-->
    <script src="js/base64.min.js"></script>
    <script>
        var deviceId=[];
//        微信配置信息
        wx.config({
            beta:true,
            debug: true,
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
                        for (i = 0; i < len; i++) {
                            if (res.deviceInfos[i].state === "connected") {
                                deviceId.push(res.deviceInfos[i].deviceId);
                            }
                        }
                        //                    alert(deviceId.length);
                        //  3、 暂定发送的频率为200ms每次
                        sendData([0xA8, 0x07]);
                        //      自动开始，退出页面结束
                        setTimeout(function () {
                            sendData([0xA2]);
                        },200)
                    }
                });
            });
        //  当设备接收到数据的时候，返回给HTML页面，jsapi。
            wx.on('onReceiveDataFromWXDevice',function(res){
                myChart.hideLoading();
                xTime+=200;
//                alert(res.base64Data);
//                alert(getNumFromRaw(res.base64Data));
                var getNum=getNumFromRaw(res.base64Data);
                $("#temShow_Num").html(getNum);
//                echarts绘图
                chartData.push([xTime,getNum]);
                myChart.setOption(option);
            });
        });
        /*发送数据到硬件设备,直接输入,写成数组形式*/
        function sendData(arr) {
            /*封装了处理十六字节数组转换为base64的方法*/
            function orderAraay(arr){
                //FE 01 00 0F 75 31 00 00 0A 00 12 01 57 18 00
                var Bytes=new Array();
                for(var i=0;i<arr.length;i++){
                    Bytes[i]=arr[i];
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
            var base64Data=arrayToBase64(orderAraay(arr));
            for(var i=0;i<deviceId.length;i++){
                wx.invoke('sendDataToWXDevice', {'deviceId':deviceId[i], 'base64Data':base64Data}, function(res){
//                    alert(res.err_msg);
                });
            }
        }
        /*******解码成10进制的数据*/
        function baseToString_10(msg) {
            var str='';
            var wx=Base64.decode(msg);
            for(var i=0;i<wx.length;i++){
                str+=wx.charCodeAt(i)+";";
            }
            return str;
        }
        /*将收到的数据进行解析，得到可用实际值*/
        function getNumFromRaw(msg) {
        //    需要获取到接受的数据的第2,3字节的数据来进行转码
            var str='';
            var wx=Base64.decode(msg);
            if(wx.length<=2){
                return "";
            } else{
                for(var i=0;i<wx.length;i++){
                    str+=wx.charCodeAt(i)+";";
                }
                var newArray=str.split(";");
                var num=newArray[2]*256+parseInt(newArray[1]);
                return (num/100).toFixed(1);
            }
        }
    </script>
</body>
</html>