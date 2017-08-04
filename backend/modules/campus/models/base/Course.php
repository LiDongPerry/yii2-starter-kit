<?php
// This class was automatically generated by a giiant build task
// You should not change it manually as it will be overwritten on next build

namespace backend\modules\campus\models\base;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the base-model class for table "course".
 *
 * @property integer $course_id
 * @property integer $school_id
 * @property integer $grade_id
 * @property string $title
 * @property string $intro
 * @property integer $courseware_id
 * @property integer $creater_id
 * @property integer $start_time
 * @property integer $end_time
 * @property integer $status
 * @property integer $updated_at
 * @property integer $created_at
 * @property string $aliasModel
 */
abstract class Course extends \yii\db\ActiveRecord
{
    CONST SCENARIO_GEDU_COURSE = 'gedu_course';

    CONST PRICE_FREE_COURSE     = 2;  // 免费
    CONST PRICE_VIP_FREE_COURSE = 1;  // 仅会员免费
    CONST PRICE_NORMAL_COURSE   = 0;  // 非免费

    CONST COURSE_STATUS_OPEN   = 10;//正常
    CONST COURSE_STATUS_FINISH = 20; //结束
    CONST COURSE_STATUS_DELECT = 30;//关闭

    // public $category_id;        //课件的分类
    public $start_date;         //开始日期
    public $start_times;        //上课时间
    public $end_times;          //结束时间
    public $which_day;          //周几
    public $course_schedule_id;//已有课程
    /**
     * @inheritdoc
     */
    public static function optsStatus(){
        return [
            self::COURSE_STATUS_OPEN   => '正常',
            self::COURSE_STATUS_FINISH => '结束',
            self::COURSE_STATUS_DELECT => '无效'
        ];
    }

    public static function getStatusValueLabel($value){
        $labels = self::optsStatus();
        if(isset($labels[$value])){
            return $labels[$value];
        }
        return $value;
    }

    public static function optsPrice()
    {
        return [
            self::PRICE_FREE_COURSE   => '免费',
            self::PRICE_VIP_FREE_COURSE => '仅会员免费',
            self::PRICE_NORMAL_COURSE => '非免费'
        ];
    }

    public static function getPriceValueLabel($value){
        $labels = self::optsPrice();
        if(isset($labels[$value])){
            return $labels[$value];
        }
        return $value;
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'course';
    }

    /**
     * @inheritdoc
     */
    public static function getDb(){
       return \Yii::$app->get('campus');
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class'              => TimestampBehavior::className(),
                //'updatedAtAttribute' => true,
            ],
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_GEDU_COURSE] = ['school_id','courseware_id','parent_id','category_id','techer_id','original_price','present_price','vip_price','access_domain','course_counts','sort','banner_src','intro','title','creater_id','status'];
        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['school_id', 'grade_id', 'title', 'intro', 'courseware_id', 'start_time', 'end_time', 'teacher_id','status'], 'required'],
            ['creater_id','default','value'=>Yii::$app->user->isGuest ? 0 : Yii::$app->user->identity->id],
            [['school_id', 'grade_id', 'courseware_id', 'creater_id','status'], 'integer'],
            [['start_time','end_time'], 'filter', 'filter' => 'strtotime', 'skipOnEmpty' => true],
            [['title'], 'string', 'max' => 32],
            [['intro'], 'string', 'max' => 128],
            ['teacher_id','required','when'=>function($model,$attribute){
                    if($model->status == self::COURSE_STATUS_OPEN ){
                        $start_time = $model->start_time - 15*60;
                        $end_time   = $model->end_time + 15*60;
                        $models = self::find()
                                ->andwhere([
                                    'teacher_id'=> $model->teacher_id,
                                    'status'    => self::COURSE_STATUS_OPEN
                                ])->andWhere(['or',
                                    ['between','start_time' ,$start_time,$end_time ],
                                    ['between','end_time',$start_time,$end_time]
                                ]);
                        if(!$model->isNewRecord){
                            $models->andWhere(['not','course_id'=>$model->course_id]);
                        }
                        $models = $models->orderBy(['end_time'=>SORT_DESC])->one();
                        //var_dump();exit;
                        if($models){
                                $message = '所选时间段本老师有未上完的课程，课程名是'.$models->title.'请检查';
                                $model->addError($attribute,$message);
                        }
            }}],
            [
                'end_time','required',  'when' => function($model,$attribute){
                    if($model->start_time > $model->end_time){
                        $model->addError($attribute,'课程开始时间不能大于开始时间');
                    }
                }

            ],
            [
                'start_time','required',  'when' => function($model,$attribute){
                    $time = time();
                    if($model->status == self::COURSE_STATUS_OPEN){
                        if($model->start_time <  $time ){
                            $model->addError($attribute,'课程开始时间不能小于当前时间');
                        }
                        $start_time = $model->start_time - 15*60;
                        $end_time   = $model->end_time + 15*60;
                        $models = self::find()
                        ->where([
                            'school_id'=>$model->school_id,
                            'grade_id'=> $model->grade_id,
                            'status'    => self::COURSE_STATUS_OPEN
                        ])->andWhere(['or',
                            ['between','start_time' ,$start_time,$end_time ],
                            ['between','end_time',$start_time,$end_time]
                        ]);
                        if(!$model->isNewRecord){
                            $models->andWhere(['not','course_id'=>$model->course_id]);
                        }
                        $models = $models->orderBy(['end_time'=>SORT_DESC])->asArray()->one();
                        //var_dump($models);exit;
                        if($models){
                            $model->addError($attribute,'本次排课与上一次排课之间的时间必须大于15分钟');
                        }
                    }
                }
            ],
            // [
            //     'start_time','int','isaa'=>function($model,$attribute){
            //        var_dump($model);exit;
            //     }
            // ]
            [['parent_id','category_id','course_counts','grade_id','start_time','end_time','teacher_id','status'],'integer','on' => self::SCENARIO_GEDU_COURSE],
            [['courseware_id','category_id','original_price','present_price','vip_price','access_domain','status'],'required','on' => self::SCENARIO_GEDU_COURSE],
            [['original_price','present_price','vip_price'],'number','on' => self::SCENARIO_GEDU_COURSE],
            [['banner_src','title'],'string','on' => self::SCENARIO_GEDU_COURSE],
            ['parent_id','default','value' => 0,'on' => self::SCENARIO_GEDU_COURSE],
            ['grade_id','default','value' => 0,'on' => self::SCENARIO_GEDU_COURSE],
            ['start_time','default','value' => 0,'on' => self::SCENARIO_GEDU_COURSE],
            ['end_time','default','value' => 0,'on' => self::SCENARIO_GEDU_COURSE],
            ['teacher_id','default','value' => 0,'on' => self::SCENARIO_GEDU_COURSE],
            ['course_counts','default','value' => function($model){
                if (!isset($model->parent_id) || empty($model->parent_id)) {
                    var_dump(self::find()->where(['parent_id' => $model->course_id])->count());exit;
                    return self::find()->where(['parent_id' => $model->course_id])->count();
                }
                return 0;
            },'on' => self::SCENARIO_GEDU_COURSE],
            ['creater_id','default','value'=>Yii::$app->user->isGuest ? 0 : Yii::$app->user->identity->id,'on' => self::SCENARIO_GEDU_COURSE],
        ];
    }
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'course_id'      => Yii::t('common', '课程ID'),
            'parent_id'      => Yii::t('common', '父课程ID'),
            'category_id'    => Yii::t('common', '课程分类'),
            'teacher_id'     => Yii::t('common', '老师'),
            'school_id'      => Yii::t('common', '学校'),
            'grade_id'       => Yii::t('common', '班级'),
            'title'          => Yii::t('common', '课程名称'),
            'banner_src'     => Yii::t('common', '课程封面'),
            'intro'          => Yii::t('common', '课程介绍'),
            'courseware_id'  => Yii::t('common', '课件'),
            'original_price' => Yii::t('common', '原价'),
            'present_price'  => Yii::t('common', '现价'),
            'vip_price'      => Yii::t('common', '会员价'),
            'creater_id'     => Yii::t('common', '课表创建者'),
            'start_time'     => Yii::t('common', '开始时间'),
            'end_time'       => Yii::t('common', '结束时间'),
            'status'         => Yii::t('common', '状态'),
            'course_counts'  => Yii::t('common', '课程总数'),
            'access_domain'  => Yii::t('common', '权限'),
            'sort'           => Yii::t('common', '排序'),
            'created_at'     => Yii::t('common', '创建时间'),
            'updated_at'     => Yii::t('common', '更新时间'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeHints()
    {
        return array_merge(parent::attributeHints(), [
            'parent_id'      => Yii::t('common', '如果该字段留空，则该课程为父课程'),
            // 'title'         => Yii::t('common', '课程名称'),
            'school_id'     => Yii::t('common', '课程所属学校'),
            'grade_id'      => Yii::t('common', '课程所属班级'),
            'banner_src'     => Yii::t('common', '课程封面图片链接'),
            // 'intro'         => Yii::t('common', '课程介绍'),
            'courseware_id' => Yii::t('common', '课程包含的课件'),
            'creater_id'    => Yii::t('common', '课表创建者'),
            'start_time'    => Yii::t('common', '课程开始时间'),
            'end_time'      => Yii::t('common', '课程结束时间'),
            'access_domain'      => Yii::t('common', '0非免费；1仅会员免费；2免费'),
            // 'status'        => Yii::t('common', '状态'),
        ]);
    }
    /**
     * 获取某老师所上过的课程
     * @param  [type] $user_id   [description]
     * @param  [type] $school_id [description]
     * @param  [type] $drade_id  [description]
     * @return [type]            [description]
     */
    public static function getAboveCourse($teacher_id = 0,$school_id = 0,$grade_id=0,$status = 20){
            return self::find()->where([
                    'teacher_id'=> $teacher_id,
                    'school_id' => $school_id,
                    'grade_id'  => $grade_id,
                    'status'    => $status,
                ])->all();
    }

    public function getSchool(){
        return $this->hasOne(\backend\modules\campus\models\School::className(),['school_id'=>'school_id']);
    }

    public function getCourseCategory(){
        return $this->hasOne(\backend\modules\campus\models\CourseCategory::className(),['category_id'=>'category_id']);
    }

    public function getGrade(){
        return $this->hasOne(\backend\modules\campus\models\Grade::className(),['grade_id'=>'grade_id']);
    }

    public function getCourseware(){
         return $this->hasOne(\backend\modules\campus\models\Courseware::className(),['courseware_id'=>'courseware_id']);
    }
    public function getUser(){
        return $this->hasOne(\common\models\User::className(),['id'=>'creater_id']);
    }

    /**
     * 获取全部班级
     * @return [type] [description]
     */
    public function getUsersToGrades(){
        return $this->hasMany(\backend\modules\campus\models\UserToGrade::className(),['grade_id'=>'grade_id']);
    }
//查询课程
    public function getCourseSchedule(){
        return $this->hasMany(\backend\modules\campus\models\CourseSchedule::className(),['course_id'=>'course_id']);
    }
    // /*****/
    // public function getUsersToGrades(){
    //     return $this->hasMany(\backend\modules\campus\models\UserToGrade::className(),['grade_id'=>'grade_id']);
    // }
    /**
     * @inheritdoc
     * @return \backend\modules\campus\models\query\courseQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \backend\modules\campus\models\query\courseQuery(get_called_class());
    }


}
