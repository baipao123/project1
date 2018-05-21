<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-04-12
 * Time: 15:23:34
 */

namespace console\controllers;

use common\models\Admin;
use common\models\Company;
use common\models\District;
use common\models\Job;
use common\tools\Tool;
use common\tools\WxApp;
use Yii;
use yii\console\Controller;

class TestController extends Controller
{

    public function actionDecrypt(){
        $appid = 'wx4f4bc4dec97d474b';
        $sessionKey = 'tiihtNczf5v6AKRyjwEUhQ==';

        $encryptedData="CiyLU1Aw2KjvrjMdj8YKliAjtP4gsMZM
                QmRzooG2xrDcvSnxIMXFufNstNGTyaGS
                9uT5geRa0W4oTOb1WT7fJlAC+oNPdbB+
                3hVbJSRgv+4lGOETKUQz6OYStslQ142d
                NCuabNPGBzlooOmB231qMM85d2/fV6Ch
                evvXvQP8Hkue1poOFtnEtpyxVLW1zAo6
                /1Xx1COxFvrc2d7UL/lmHInNlxuacJXw
                u0fjpXfz/YqYzBIBzD6WUfTIF9GRHpOn
                /Hz7saL8xz+W//FRAUid1OksQaQx4CMs
                8LOddcQhULW4ucetDf96JcR3g0gfRK4P
                C7E/r7Z6xNrXd2UIeorGj5Ef7b1pJAYB
                6Y5anaHqZ9J6nKEBvB4DnNLIVWSgARns
                /8wR2SiRS7MNACwTyrGvt9ts8p12PKFd
                lqYTopNHR1Vf7XjfhQlVsAJdNiKdYmYV
                oKlaRv85IfVunYzO0IKXsyl7JCUjCpoG
                20f0a04COwfneQAGGwd5oa+T8yO5hzuy
                Db/XcxxmK01EpqOyuxINew==";

        $iv = 'r7BXXKkLb8qrSNn05n0qiA==';

        $data = [];
        WxApp::decryptData($encryptedData,$iv,$sessionKey,$data);
        echo $data;
    }

    public function actionIndex(){
      $company = Company::findOne(1);
      echo var_export($company->dailyRecords(),true);
    }

    public function actionDistrict(){
        $arr = [
            "南京"  => "玄武区 秦淮区 建邺区 鼓楼区 浦口区 栖霞区 雨花台区 江宁区 六合区 溧水区 高淳区",
            "无锡"  => "锡山区 惠山区 滨湖区 梁溪区 新吴区 江阴市 宜兴市",
            "徐州"  => "鼓楼区 云龙区 贾汪区 泉山区 铜山区 丰县 沛县 睢宁县 新沂市 邳州市",
            "常州"  => "天宁区 钟楼区 新北区 武进区 溧阳市 金坛区",
            "苏州"  => "虎丘区 吴中区 相城区 姑苏区 吴江区 常熟市 张家港市 昆山市 太仓市",
            "南通"  => "崇川区 港闸区 通州区 海安县 如东市 启东市 如皋市 海门市",
            "连云港" => "连云区 海州区 赣榆区 东海县 灌云县 灌南县",
            "淮安"  => "淮安区 淮阴区 清江浦区 涟水县 洪泽区 盱眙县 金湖县",
            "盐城"  => "亭湖区 盐都区 大丰区 响水县 滨海县 阜宁县 射阳县 建湖县 东台市",
            "扬州"  => "广陵区 邗江区 江都区 宝应县 仪征市 高邮市",
            "镇江"  => "京口区 润州区 丹徒区 丹阳市 扬中市 句容市",
            "泰州"  => "海陵区 高港区 兴化市 靖江市 泰兴市 姜堰区",
            "宿迁"  => "宿城区 宿豫区 沭阳县 泗阳县 泗洪县"
        ];
        foreach ($arr as $city=>$areas){
            $district = new District;
            $district->pid = 1;
            $district->name = $city."市";
            $district->status = District::ON;
            $district->created_at = time();
            $district->save();
            $cid = $district->attributes['id'];
            foreach (explode(" ",$areas) as $area){
                $d=  new District;
                $d->pid = 1;
                $d->cid = $cid;
                $d->name = $area;
                $d->status = District::ON;
                $d->created_at = time();
                $d->save();
            }
        }
        echo 1;
    }
}