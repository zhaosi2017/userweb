/**
 * Created by Administrator on 15-12-9.
 */
$(document).ready(function(){

    // 提醒
    (function(){
        var updateInterval = 30000; //Fetch data ever x milliseconds
        var realtime = "on"; //If == to on then fetch data every x seconds. else stop fetching

        function update(){
            $.ajax({
                url: infoUrl,
                dataType: "json",
                success: function(result){
                    if(result.status == true){
                        var favionNum = result.count;
                        var content = result.content;
                        if(favionNum>0){
                            var iN = new iNotify().init({
                                effect: 'flash',
                                interval: 500,
                                message:"您有未处理信息",
                                audio:{
                                    file: '/data/ring.mp3'//可以使用数组传多种格式的声音文件
                                },
                                notification:{
                                    title:"未处理信息",
                                    body:content
                                }
                            }).setFavicon(favionNum).setTitle('您有未处理信息').player();

                            iN.notify({
                                title:"您有未处理信息",
                                body:content
                            });
                        }
                    }else{
                        toastr["error"](data.content, "系统提示");
                    }
                    if(realtime == "on"){
                        setTimeout(update, updateInterval)
                    }
                }
            })
        };
        update();
    })();

})
