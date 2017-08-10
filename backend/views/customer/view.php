<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\Customer */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => '客户', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="customer-view">

    <p>
        <?= Html::a('返回列表', ['index'], ['class' => 'btn btn-primary']) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'code',
            'name',
            'number',
            'aide_name',
            [                      // the owner name of the model
              'label' => 'group_id',
             'value' => $model->agency->name,
            ],
            [                      // the owner name of the model
                'label' => 'level',
                'value' => \backend\models\Customer::$levelArr[$model->level],
            ],
            [                      // the owner name of the model
                'label' => 'type',
                'value' => \backend\models\Customer::$customerType[$model->type],
            ],
            'company',
            [                      // the owner name of the model
                'label' => 'admin_id',
                'value' => $model->admin->account,
            ],
            [                      // the owner name of the model
                'label' => 'create_at',
                'value' => date('Y-m-d H:i:s',$model->create_at),
            ],
            [                      // the owner name of the model
                'label' => 'update_at',
                'value' => date('Y-m-d H:i:s',$model->update_at),
            ],
        ],
    ]) ?>

</div>
