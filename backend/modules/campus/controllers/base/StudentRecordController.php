<?php
// This class was automatically generated by a giiant build task
// You should not change it manually as it will be overwritten on next build

namespace backend\modules\campus\controllers\base;

use Yii;
use backend\modules\campus\models\StudentRecord;
use backend\modules\campus\models\Course;
use backend\modules\campus\models\search\StudentRecordSearch;
use yii\web\Controller;
use yii\web\HttpException;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\filters\AccessControl;
use dmstr\bootstrap\Tabs;

/**
* StudentRecordController implements the CRUD actions for StudentRecord model.
*/
class StudentRecordController extends \common\components\Controller
{

/**
* @var boolean whether to enable CSRF validation for the actions in this controller.
* CSRF validation is enabled only when both this property and [[Request::enableCsrfValidation]] are true.
*/
public $enableCsrfValidation = false;

    /**
    * @inheritdoc
    */
    public function behaviors()
    {
    return [
    'access' => [
    'class' => AccessControl::className(),
    'rules' => [
    [
    'allow' => true,
                        'actions' => ['index', 'view', 'create', 'update', 'delete'],
                        'roles' => ['CampusStudentRecordFull'],
                    ],
    [
    'allow' => true,
                        'actions' => ['index', 'view'],
                        'roles' => ['CampusStudentRecordView'],
                    ],
    [
    'allow' => true,
                        'actions' => ['update', 'create', 'delete'],
                        'roles' => ['CampusStudentRecordEdit'],
                    ],
    
                ],
            ],
    ];
    }

/**
* Lists all StudentRecord models.
* @return mixed
*/
public function actionIndex()
{
    $user_id = 0;
    if(Yii::$app->user->identity->id){
        $user_id = Yii::$app->user->identity->id;
    }
    $searchModel  = new StudentRecordSearch;
    $dataProvider = $searchModel->search($_GET);
    //var_dump($this->schoolIdCurrent,$this->gradeIdCurrent);exit;
    //获取老师已上过的课程
    $courseIds  = Course::getAboveCourse($user_id,$this->schoolIdCurrent,$this->gradeIdCurrent,Course::COURSE_STATUS_FINISH);
    //var_dump($courseIds);exit;
    $dataProvider->query->andWhere([
            'course_id'=>ArrayHelper::map($courseIds,'course_id','course_id')
        ]);
    $dataProvider->sort =[
            'defaultOrder'=>[
                'updated_at'=>SORT_DESC
            ]
    ];
    Tabs::clearLocalStorage();

    Url::remember();
    \Yii::$app->session['__crudReturnUrl'] = null;

    return $this->render('index', [
    'dataProvider' => $dataProvider,
        'searchModel' => $searchModel,
    ]);
}

/**
* Displays a single StudentRecord model.
* @param integer $student_record_id
*
* @return mixed
*/
public function actionView($student_record_id)
{
\Yii::$app->session['__crudReturnUrl'] = Url::previous();
Url::remember();
Tabs::rememberActiveState();

return $this->render('view', [
'model' => $this->findModel($student_record_id),
]);
}

/**
* Creates a new StudentRecord model.
* If creation is successful, the browser will be redirected to the 'view' page.
* @return mixed
*/
public function actionCreate()
{
    $model = new StudentRecord;
    if($_POST){
        $info = $model->create($_POST['StudentRecord']);
        if($info['errorno'] == 0 ){
            return $this->redirect(['student-record/index']);
        }else{
            //var_dumP($info);exit;
            \Yii::$app->getSession()->setFlash('alert', [
                    'body'=>"错误提示：".implode(',',$info['error']),
                    //'options'=>['class'=>'alert-danger']
                ]);
        }
    }
// try {
// if ($model->load($_POST) && $model->save()) {
// return $this->redirect(['view', 'student_record_id' => $model->student_record_id]);
// } elseif (!\Yii::$app->request->isPost) {
// $model->load($_GET);
// }
// } catch (\Exception $e) {
// $msg = (isset($e->errorInfo[2]))?$e->errorInfo[2]:$e->getMessage();
// $model->addError('_exception', $msg);
// }
return $this->render('create', ['model' => $model]);
}

/**
* Updates an existing StudentRecord model.
* If update is successful, the browser will be redirected to the 'view' page.
* @param integer $student_record_id
* @return mixed
*/
public function actionUpdate($student_record_id)
{
$model = $this->findModel($student_record_id);

if ($model->load($_POST) && $model->save()) {
return $this->redirect(Url::previous());
} else {
return $this->render('update', [
'model' => $model,
]);
}
}

/**
* Deletes an existing StudentRecord model.
* If deletion is successful, the browser will be redirected to the 'index' page.
* @param integer $student_record_id
* @return mixed
*/
public function actionDelete($student_record_id)
{
try {
$this->findModel($student_record_id)->delete();
} catch (\Exception $e) {
$msg = (isset($e->errorInfo[2]))?$e->errorInfo[2]:$e->getMessage();
\Yii::$app->getSession()->addFlash('error', $msg);
return $this->redirect(Url::previous());
}

// TODO: improve detection
$isPivot = strstr('$student_record_id',',');
if ($isPivot == true) {
return $this->redirect(Url::previous());
} elseif (isset(\Yii::$app->session['__crudReturnUrl']) && \Yii::$app->session['__crudReturnUrl'] != '/') {
Url::remember(null);
$url = \Yii::$app->session['__crudReturnUrl'];
\Yii::$app->session['__crudReturnUrl'] = null;

return $this->redirect($url);
} else {
return $this->redirect(['index']);
}
}

/**
* Finds the StudentRecord model based on its primary key value.
* If the model is not found, a 404 HTTP exception will be thrown.
* @param integer $student_record_id
* @return StudentRecord the loaded model
* @throws HttpException if the model cannot be found
*/
protected function findModel($student_record_id)
{
if (($model = StudentRecord::findOne($student_record_id)) !== null) {
return $model;
} else {
throw new HttpException(404, 'The requested page does not exist.');
}
}
}
