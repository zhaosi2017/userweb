<?php

namespace backend\models\logreader;

use yii\base\Model;

class LogLine extends Model
{
    public $date;
    public $ip;
    public $user_id;
    public $session_id;
    public $level;
    public $category;
    public $text;
    public $index;
    public $firstLine;

    public function attributeLabels()
    {
        return [
            'date'       => '日期',
            'ip'         => 'Ip',
            'user_id'    => '用户ID',
            'session_id' => '会话ID',
            'level'      => '等级',
            'category'   => '目录',
            'text'       => '内容',
            'index'      => '索引',
            'firstLine'=>'第一行',
        ];
    }

    public function rules()
    {
        return [
            ['date', 'date'],
            [['index', 'user_id'], 'integer'],
            [['ip', 'session_id', 'level', 'category', 'text', 'firstLine'], 'string'],
        ];
    }

    public function highlight($text)
    {
        return preg_replace([
//            "|'(.+)'|U",
            '| (\/.+)([ $])|U',
            '|php:(\d+)[ $]?|',
            '|(\n)|',
            '|( {4})|'
        ], [
            ' <span class="text-primary">$1</span>$2',
            'php:<span class="label label-danger">$1</span>',
            '<br>',
            "&nbsp;&nbsp;&nbsp;&nbsp;"
        ], $text);
    }
}