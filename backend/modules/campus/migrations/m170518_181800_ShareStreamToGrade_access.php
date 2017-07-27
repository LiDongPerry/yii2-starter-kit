<?php

use yii\db\Migration;

class m170518_181800_ShareStreamToGrade_access extends Migration
{
    /**
     * @var array controller all actions
     */
    public $permisions = [
        "index" => [
            "name" => "campus_share-stream-to-grade_index",
            "description" => "campus/share-stream-to-grade/index"
        ],
        "view" => [
            "name" => "campus_share-stream-to-grade_view",
            "description" => "campus/share-stream-to-grade/view"
        ],
        "create" => [
            "name" => "campus_share-stream-to-grade_create",
            "description" => "campus/share-stream-to-grade/create"
        ],
        "update" => [
            "name" => "campus_share-stream-to-grade_update",
            "description" => "campus/share-stream-to-grade/update"
        ],
        "delete" => [
            "name" => "campus_share-stream-to-grade_delete",
            "description" => "campus/share-stream-to-grade/delete"
        ]
    ];
    
    /**
     * @var array roles and maping to actions/permisions
     */
    public $roles = [
        "CampusShareStreamToGradeFull" => [
            "index",
            "view",
            "create",
            "update",
            "delete"
        ],
        "CampusShareStreamToGradeView" => [
            "index",
            "view"
        ],
        "CampusShareStreamToGradeEdit" => [
            "update",
            "create",
            "delete"
        ]
    ];
    
    public function up()
    {
        
        $permisions = [];
        $auth = \Yii::$app->authManager;

        /**
         * create permisions for each controller action
         */
        foreach ($this->permisions as $action => $permission) {
            $permisions[$action] = $auth->createPermission($permission['name']);
            $permisions[$action]->description = $permission['description'];
            $auth->add($permisions[$action]);
        }

        /**
         *  create roles
         */
        foreach ($this->roles as $roleName => $actions) {
            $role = $auth->createRole($roleName);
            $auth->add($role);

            /**
             *  to role assign permissions
             */
            foreach ($actions as $action) {
                $auth->addChild($role, $permisions[$action]);
            }
        }
    }

    public function down() {
        $auth = Yii::$app->authManager;

        foreach ($this->roles as $roleName => $actions) {
            $role = $auth->createRole($roleName);
            $auth->remove($role);
        }

        foreach ($this->permisions as $permission) {
            $authItem = $auth->createPermission($permission['name']);
            $auth->remove($authItem);
        }
    }
}
