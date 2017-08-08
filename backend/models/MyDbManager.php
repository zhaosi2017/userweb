<?php
namespace backend\models;
use yii\rbac\DbManager;
use yii\db\Query;
class MyDbManager extends DbManager
{
    /**
     * 修改某个用户为某个角色时，使用
     */
    public function updateAssignment($role,$user_id)
    {
        $this->db->createCommand()
            ->update($this->assignmentTable, ['item_name' => $role], ['user_id'=>$user_id])
            ->execute();
        return true;
    }
    /**
     * 删除某个角色时，检查该角色下是否已经有用户了,有的话，返回总的数
     */
    public function checkAssignment($roleName)
    {
        if (empty($roleName)) {
            return false;
        }

        $row = (new Query)->from($this->assignmentTable)
            ->where(['item_name' => $roleName])
            ->count();
        return $row;

    }

}