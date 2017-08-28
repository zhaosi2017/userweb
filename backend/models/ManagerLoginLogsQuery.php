<?php

namespace backend\models;

/**
 * This is the ActiveQuery class for [[ManagerLoginLogs]].
 *
 * @see ManagerLoginLogs
 */
class ManagerLoginLogsQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return ManagerLoginLogs[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return ManagerLoginLogs|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
