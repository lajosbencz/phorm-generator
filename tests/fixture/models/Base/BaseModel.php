<?php

namespace PhormTest\Models\Base;


use Phalcon\Mvc\Model;

class BaseModel extends Model
{
    public function initialize()
    {
        $this->belongsTo(['parent_id'], 'RobotCategory', ['id'], [
            'alias' => 'Parent',
        ]);
        $this->hasOne(['primary_id'],'RobotPart',['id'],[
            'alias' => 'PrimaryPart',
        ]);
        $this->hasMany(['id'],'RobotPart',['category_id'],[
            'alias' => 'RobotPart',
        ]);
    }
}