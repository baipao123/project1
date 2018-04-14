        <style>
            .bp-qiniu-uploader{position: relative;z-index:99;}
            .bp-qiniu-uploader>input[type=file]{position: absolute;left: 0;top: 0;width: 100%;height: 100%;}
        </style>
        <div class="layui-btn bp-qiniu-uploader bp-qiniu-uploader-<?= $id ?>">
            <i class="layui-icon">&#xe67c;</i>上传图片
        </div>

        <script>
            $(document).ready(function () {
                if(typeof <?=$callBack?> !== 'function'){
                    function <?=$callBack?>(info) {
                        console.log(info);
                    }
                }

                layui.use('upload', function () {
                    var upload = layui.upload;
                    upload.render({
                        elem: '.bp-qiniu-uploader-<?= $id ?>', //绑定元素
                        multiple:<?= $isMulti ? "true" : "false" ?>,
                        auto: false,
                        choose: function (obj) {
                            var files = obj.pushFile();
                            var token = Bp123GetUpToken("<?=$tokenUrl?>");
                            var putExtra = {
                                mimeType:<?=$mineTypes?>,
                                params:<?=$params?>
                            };
                            var config = {
                                useCdnDomain: <?=$useCdn?>,
                                region: <?=empty($region) ? "null" : $region?>
                            };
                            var observer = {
                                next: function (res) {
                                },
                                error: function (res) {
                                    console.log(res);
                                },
                                complete:<?=$callBack?>
                            };
                            $.each(files, function (i, file) {
                                var observable = qiniu.upload(file, null, token, putExtra, config);
                                observable.subscribe(observer);
                            });
                        }
                    })
                });
            });
        </script>