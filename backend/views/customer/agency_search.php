<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\modules\task\models\ProductSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '产品选择';
$this->params['breadcrumbs'][] = $this->title;
$actionId = Yii::$app->requestedAction->id;

?>
<div class="product-index">

    <p class="btn-group hidden-xs">
          </p>

    <?php  echo $this->render('_agency', ['model' => $searchModel]); ?>

    <?php Pjax::begin(); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
//        'filterModel' => $searchModel,
        'pager'=>[
            //'options'=>['class'=>'hidden']//关闭自带分页
            'firstPageLabel'=>"首页",
            'prevPageLabel'=>'上一页',
            'nextPageLabel'=>'下一页',
            'lastPageLabel'=>'末页',
            'maxButtonCount' => 9,
        ],

        'columns' => [
            ['class' => 'yii\grid\SerialColumn','header' => '序号'],
            [
                'class' => 'yii\grid\DataColumn',
                'header' => '产品名称',
//                'headerOptions' => ['width' => '120'] ,
                'attribute' => 'name',
                'value'  => function ($data)
                {
                    return $data->name ;
                },
            ],






//            [
//                'class' => 'yii\grid\ActionColumn',
//                'header' => '操作',
//                'template' => '{choose}',
//                'buttons' => [
//                    'choose' => function($url, $model)
//                    {
//                        $product_price_html = '<tr><td>货币</td><td>购买价格</td><td>成交价格</td></tr>';
//                        $grade_price = [
//                            1 => 'a_grade_price',
//                            2 => 'b_grade_price',
//                            3 => 'c_grade_price',
//                            4 => 'd_grade_price',
//                        ];
//
//                        foreach ($model->purchasePrice as $k=>$v){
//                            $product_price_html .= '<tr>'
//                                .'<td>'.$model->money[$v->money_id].'</td>'
//                                .'<td><input title="" readonly="readonly" class="no-borders" name="PurchasePrice['.$v->money_id.']" value="'.$v[$grade_price[$model->grade]].'" ></td>'
//                                .'<td><input title="" required class="form-control" step="0.01" min="'.$v[$grade_price[$model->grade]].'" name="TaskDealPrice['.$v->money_id.']" type="number" value="'.$v[$grade_price[$model->grade]].'"></td>'
//                                .'</tr>';
//                        }
//
//                        $btn_link = Html::button('选择',
//                            [
//                                'class' => 'btn btn-primary',
//                                'onclick' => '
//                                    var url = \''.$url.'\';
//                                    var $body = parent.$("body");
//                                    var requirement = \''.$model->description.'\';
//                                    $body.find("#task-product_id").val(\''.$model->id.'\');
//                                    $body.find("#product-name-number").html(\''.$model->name.'\' + \'('.$model->number.')\');
//                                    $body.find("#product-company").removeClass("hide").find("input").val(\''.$model['company']['name'].'\');
//                                    $body.find("#product-requirement").removeClass("hide").find("#product_requirement").html(requirement);
//                                    $body.find("#product_price_table").removeClass("hide").find("table").html(\''.$product_price_html.'\');
//
//                                    var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
//                                    parent.layer.close(index); //再执行关闭
//                                ',
//                            ]);
//                        return $btn_link;
//                    },
//
//                ],
//            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?></div>
