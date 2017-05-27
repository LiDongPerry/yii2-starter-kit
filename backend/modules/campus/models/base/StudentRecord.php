<?php
// This class was automatically generated by a giiant build task
// You should not change it manually as it will be overwritten on next build

namespace backend\modules\campus\models\base;

use Yii;
use yii\behaviors\TimestampBehavior;
use backend\modules\campus\models\School;
use backend\modules\campus\models\Grade;
use backend\modules\campus\models\SignIn;
use yii\helpers\ArrayHelper;

/**
 * This is the base-model class for table "student_record".
 *
 * @property integer $student_record_id
 * @property integer $user_id
 * @property integer $school_id
 * @property integer $grade_id
 * @property string $title
 * @property integer $status
 * @property integer $sort
 * @property integer $updated_at
 * @property integer $created_at
 * @property string $aliasModel
 */
abstract class StudentRecord extends \yii\db\ActiveRecord
{
    const STUDEN_RECORD_STATUS_VALID = 1;//正常
    const STUDEN_RECORD_STATUS_DELECT = 0;//删除

    public static  function optsStatus(){
        return [
            self::STUDEN_RECORD_STATUS_VALID => "正常",
            self::STUDEN_RECORD_STATUS_DELECT => "删除",
        ];
    }

    public static function getStatusLabel($value){
        $labels = self::optsStatus();
        if(isset($labels[$value])){
            return $labels[$value];
        }
        return $value;
    }

     /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        // return \Yii::$app->modules['campus']->get('campus');
        return Yii::$app->get('campus');
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'student_record';
    }


    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'school_id', 'grade_id', 'title'], 'required'],
            [['user_id', 'school_id', 'grade_id', 'status', 'sort'], 'integer'],
            [['title'], 'string', 'max' => 512]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'student_record_id' => Yii::t('common', '自增ID'),
            'user_id'           => Yii::t('common', '用户ID'),
            'school_id'         => Yii::t('common', '学校ID'),
            'grade_id'          => Yii::t('common', '班级ID'),
            'course_id'         => Yii::t('common', '课程ID'),
            'title'             => Yii::t('common', '标题'),
            'status'            => Yii::t('common', '状态'),
            'sort'              => Yii::t('common', '排序'),
            'updated_at'        => Yii::t('common', '更新时间'),
            'created_at'        => Yii::t('common', '创建时间'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeHints()
    {
        return array_merge(parent::attributeHints(), [
            'student_record_id' => Yii::t('common', '自增ID'),
            'user_id'           => Yii::t('common', '用户ID'),
            'school_id'         => Yii::t('common', '学校ID'),
            'grade_id'          => Yii::t('common', '班级ID'),
            'course_id'         => Yii::t('common', '课程ID'),
            'title'             => Yii::t('common', '标题'),
            'status'            => Yii::t('common', '状态'),
            'sort'              => Yii::t('common', '排序'),
        ]);
    }

    public function getlist($type_id,$id =false){
        if($type_id == 1){
            $school = School::find()->where(['status'=>School::SCHOOL_STATUS_OPEN])->asArray()->all();
            return ArrayHelper::map($school,'school_id','school_title');
        }
        if($type_id == 2){
            $grade = Grade::find()->where(['status'=>Grade::GRADE_STATUS_OPEN, 'school_id'=>$id])->asArray()->all();
            //var_dump($grade);exit;
            return ArrayHelper::map($grade,'grade_id','grade_name');
        }

        if($type_id == 3){
            $course = Course::find()->where(['grade_id'=>$id,'status'=>Course::COURSE_STATUS_OPEN])->asArray()->all();
            return ArrayHelper::map($course,'course_id','title');
        }
        if($type_id == 4){
            $user = SignIn::find()->where(['course_id' => $id,'status'=> SignIn::TYPE_STATUS_MORMAL ])->asArray()->all();
            //var_dump($user);exit;
            $users = [];
            foreach ($user as $key => $value) {
                $users[$key]['user_id'] = $value['student_id'];
                $users[$key]['username'] = SignIn::getUserName($value['student_id']);
            }
            //var_dump($users);exit;
            return ArrayHelper::map($users,'user_id','username');
        }
        return false;
    }
    
    public function getSchool(){
        return $this->hasOne(\backend\modules\campus\models\School::className(),['school_id'=>'school_id']);
    }
    public function getGrade(){
        return $this->hasOne(\backend\modules\campus\models\Grade::className(),['grade_id'=>'grade_id']);
    }
    public function getCourse(){
         return $this->hasOne(\backend\modules\campus\models\Course::className(),['course_id'=>'course_id']);
    }
    public function getUser(){
        return $this->hasOne(\common\models\User::className(),['id'=>'user_id']);
    }

    public function getStudentRecordValue(){
        return $this->hasMany(\backend\modules\campus\models\StudentRecordValue::className(),['student_record_id'=>'student_record_id']);
    }
    /**
     * @inheritdoc
     * @return \backend\modules\campus\models\query\StudentRecordQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \backend\modules\campus\models\query\StudentRecordQuery(get_called_class());
    }


}
