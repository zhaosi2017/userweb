<?php

namespace backend\models\logreader;

use yii\base\Object;

class LogFile extends Object
{
    public $filename;
    public $path;
    protected $_mtime;

    public function getMtime()
    {
        if(empty($this->_mtime))
        {
            $this->_mtime = filemtime($this->path . '/' . $this->filename);
        }
        return $this->_mtime;
    }

    /**
     * Use uasort for sort models
     * @param LogFile $one
     * @param LogFile $two
     * @return int
     */
    public static function sort($one, $two)
    {
        if($one->mtime < $two->mtime) return -1;
        elseif($one->mtime > $two->mtime) return 1;
        else return 0;
    }


}