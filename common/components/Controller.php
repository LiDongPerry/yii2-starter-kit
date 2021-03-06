<?php
namespace common\components;
use Yii;
use yii\helpers\ArrayHelper;
use backend\modules\campus\models\UserToGrade;
use backend\modules\campus\models\UserToSchool;
use backend\modules\campus\models\School;

class Controller extends \yii\web\Controller
{

    public $schoolCurrent   = [];
    public $schoolIdCurrent =  NULL;
    public $gradeCurrent    = [];
    public $gradeIdCurrent  = NULL;
    public function init() {
        parent::init();
        if(!Yii::$app->user->isGuest){
            $this->getInitSchoolAndGrade();
        }
    }

    public function getInitSchoolAndGrade(){
       $post = Yii::$app->request->post();
       $session = Yii::$app->session;
       $select_school_grade = isset($post['select_school_grade']) ? $post['select_school_grade'] : NULL;
        $flush = false;
        // 学校
        $schools = Yii::$app->user->identity->getSchool(
            Yii::$app->user->identity->id,
            $limit = 10,
            $flush
        );
        if(isset($post['school_id']) && !empty($post['school_id']) && !empty($select_school_grade))
        {
            //$school_id = 0;
            foreach ($schools as $key => $value) {
                if($value['school_id'] == $post['school_id']){
                    $this->schoolCurrent = $schools[$key];
                    $session->set('schools',$value['school_id']);
                    $this->schoolIdCurrent = (int)ArrayHelper::getValue($this->schoolCurrent, 'school_id');
                    break;
                }
            }
        }else{
            $session_school_id = $session->get('schools');
            if(!empty($session_school_id)){
                $schools = ArrayHelper::index($schools,'school_id');
                $this->schoolCurrent =isset($schools[$session_school_id]) ? $schools[$session_school_id] :  current($schools) ;
            }else{
                $this->schoolCurrent = current($schools) ;
            }
                  
                $this->schoolIdCurrent = (int)ArrayHelper::getValue($this->schoolCurrent, 'school_id');
        }

        Yii::$app->user->identity->setCurrentSchoolId($this->schoolIdCurrent);
        Yii::$app->user->identity->setCurrentSchool($this->schoolCurrent);
            //获取班级
            $grades = Yii::$app->user->identity->getGrades(
                Yii::$app->user->identity->id,
                $this->schoolIdCurrent,
                $limit = 100,
                $flush
            );
        if(isset($post['grade_id']) && !empty($post['grade_id']) && !empty($select_school_grade)){
            foreach ($grades as $key => $value) {
                if($value['grade_id'] == $post['grade_id']){
                    $this->gradeCurrent = $value;
                    $session->set('grades',$value);
                    $this->gradeIdCurrent = (int)ArrayHelper::getValue($this->gradeCurrent, 'grade_id');
                    break;
                }
            }

            if($this->gradeIdCurrent == NULL){
                $this->gradeCurrent = current($grades);
                if(!$this->gradeCurrent){
                    $this->gradeCurrent = [];
                }
                $this->gradeIdCurrent = (int)ArrayHelper::getValue($this->gradeCurrent, 'grade_id');
            }
        }else{
            $this->gradeCurrent = !empty($session->get('grades')) ? $session->get('grades') : current($grades);
            if(!$this->gradeCurrent){
                $this->gradeCurrent = [];
            }
            $this->gradeIdCurrent = (int)ArrayHelper::getValue($this->gradeCurrent, 'grade_id');
        }

        Yii::$app->user->identity->setCurrentGradeId($this->gradeIdCurrent);
        Yii::$app->user->identity->setCurrentGrade($this->gradeCurrent);
        // 初始化学校班级数据
        Yii::$app->user->identity->setSchoolsInfo($schools);
        Yii::$app->user->identity->setGradesInfo($grades);
    }

}
?>