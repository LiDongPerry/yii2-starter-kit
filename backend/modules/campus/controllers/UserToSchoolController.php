<?php

namespace backend\modules\campus\controllers;

use Yii;
use common\models\User;
use backend\modules\campus\models\UserForm;
use backend\modules\campus\models\search\UserToSchoolSearch;
use common\components\Controller;
use yii\web\NotFoundHttpException;
use yii\helpers\ArrayHelper;
use yii\filters\VerbFilter;
use backend\modules\campus\models\UserToSchoolForm;
use backend\modules\campus\models\School;


/**
 * UserToSchoolController implements the CRUD actions for UserToSchool model.
 */
class UserToSchoolController extends \backend\modules\campus\controllers\base\UserToSchoolController
{

    /**
     * 导入新学员
     * @return [type] [description]
     */
    public function actionUserToSchoolForm(){
        $model = new UserToSchoolForm;
        $info = [];
        $schools =  Yii::$app->user->identity->schoolsInfo;
        $e_roles = [];
        $p_roles = [];
        if(Yii::$app->user->can('E_manager')){
            $e_roles =  ArrayHelper::map(Yii::$app->authManager->getChildRoles('E_administrator'),'name','description');
        }
//主任
        if(Yii::$app->user->can('P_director')){
            $p_roles =Yii::$app->authManager->getChildRoles('P_director');
        }
//校长
        if(Yii::$app->user->can('P_leader')){

            $p_roles = Yii::$app->authManager->getChildRoles('P_leader');
        }
//代理管理员
        if(Yii::$app->user->can('P_manager')){
            $p_roles = Yii::$app->authManager->getChildRoles('P_manager');
        }
//代理超级管理员
        if(Yii::$app->user->can('P_administrator') || Yii::$app->user->can('manager') || Yii::$app->user->can('E_manager')){
            $p_roles =Yii::$app->authManager->getChildRoles('P_administrator');
        }
        ArrayHelper::multisort($p_roles,['updatedAt'],[SORT_ASC]);
        ArrayHelper::multisort($e_roles,['updatedAt'],[SORT_ASC]);
        $p_roles = ArrayHelper::map($p_roles,'name','description');
        $roles = ArrayHelper::merge($e_roles,$p_roles);
//$schools = ArrayHelper::map($schools,'school_id','school_title');
        $schools = ArrayHelper::map($schools,'school_id','school_title');
        if($model->load($_POST)){
            $info = $model->batch_create($_POST);
// var_dump($info);exit;
            if(isset($info['error']) && !empty($info['error'])){
                 return $this->render('_user_to_school',
                    [
                    'model'=>$model,
                    'schools'=>$schools,
                    'rules'=>$roles,
                    'info'=>$info['error'],
                    ]);
            }else{
                return $this->redirect(['index']);
            }
        }

        return $this->render('_user_to_school',
            [
            'model'=>$model,
             'schools'=>$schools,
             //getRolesByUser
            'rules'=>$roles
            ]);
    }
    /**
     * 代理商修改用户信息
     * @return [type] [description]
     */
    public function actionAccount($user_id){
        $model = new UserForm();
        $e_roles = [];
        $p_roles = [];
        if(Yii::$app->user->can('E_manager')){
            $e_roles =  ArrayHelper::map(Yii::$app->authManager->getChildRoles('E_administrator'),'name','description');
        }
//主任
        if(Yii::$app->user->can('P_director')){
            $p_roles =Yii::$app->authManager->getChildRoles('P_director');
        }
//校长
        if(Yii::$app->user->can('P_leader')){

            $p_roles = Yii::$app->authManager->getChildRoles('P_leader');
        }
//代理管理员
        if(Yii::$app->user->can('P_manager')){
            $p_roles = Yii::$app->authManager->getChildRoles('P_manager');
        }
//代理超级管理员
        if(Yii::$app->user->can('P_administrator') || Yii::$app->user->can('manager') || Yii::$app->user->can('E_manager')){
            $p_roles =Yii::$app->authManager->getChildRoles('P_administrator');
        }
        ArrayHelper::multisort($p_roles,['updatedAt'],[SORT_ASC]);
        ArrayHelper::multisort($e_roles,['updatedAt'],[SORT_ASC]);
        $p_roles = ArrayHelper::map($p_roles,'name','description');
        $roles = ArrayHelper::merge($e_roles,$p_roles);
        $user = User::find()->where(['id' => $user_id])->one();
        if($user == NULL){
            throw new NotFoundHttpException('The requested page does not exist.');
        }
        $model->setModel($user);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }
        return $this->render('_account', [
            'model' => $model,
            'roles' => $roles
        ]);
    }
}
