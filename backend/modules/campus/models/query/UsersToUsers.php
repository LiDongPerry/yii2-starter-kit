<?php

namespace backend\modules\campus\models\query;

/**
 * This is the ActiveQuery class for [[\backend\modules\campus\models\UsersToUsers]].
 *
 * @see \backend\modules\campus\models\UsersToUsers
 */
class UsersToUsers extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return \backend\modules\campus\models\UsersToUsers[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \backend\modules\campus\models\UsersToUsers|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
