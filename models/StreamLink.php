<?php

namespace humhub\modules\linklist\models;

use humhub\components\ActiveRecord;
use humhub\modules\comment\models\Comment;
use humhub\modules\content\models\Content;
use humhub\modules\linklist\helpers\MarkdownHelper;
use Yii;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "linklist_stream".
 *
 * @package humhub.modules.linklist.models
 * The followings are the available columns in table 'linklist_stream':
 * @property integer $id
 * @property integer $content_id
 * @property string $object_model
 * @property integer $object_id
 * @property string $href
 * @property string $title
 *
 * @property Content $content
 */
class StreamLink extends ActiveRecord
{
    /**
     * @ineritdoc
     */
    public static function tableName()
    {
        return 'linklist_stream';
    }

    /**
     * @ineritdoc
     */
    public function rules()
    {
        return [
            [['content_id', 'object_id', 'href'], 'required'],
            [['content_id', 'object_id'], 'integer'],
            [['object_model'], 'string', 'max' => 100],
            [['href'], 'string', 'max' => 2047],
            [['title'], 'string', 'max' => 255],
        ];
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'content_id' => 'Content',
            'object_model' => 'Object model',
            'object_id' => 'Model ID',
            'href' => 'URL',
            'title' => Yii::t('LinklistModule.models_Link', 'Title'),
        );
    }

    /**
     * @return ActiveQuery
     */
    public function getContent()
    {
        return $this
            ->hasOne(Content::class, ['id' => 'content_id']);
    }

    /**
     * @param bool $scheme
     * @return string
     */
    public function getSourceUrl(bool $scheme = false)
    {
        if ($this->object_model === Comment::class) {
            $comment = Comment::findOne($this->object_id);
            if ($comment !== null) {
                return $comment->getUrl($scheme);
            }
        }
        return $this->content ? $this->content->getUrl($scheme) : '';
    }

    /**
     * @param string $markdown
     * @param int $contentId
     * @param string $objectModel
     * @param int $objectId
     * @return void
     */
    public static function updateLinks(string $markdown, int $contentId, string $objectModel, int $objectId)
    {
        static::deleteAll(['object_model' => $objectModel, 'object_id' => $objectId]);
        foreach (MarkdownHelper::grepLinks($markdown) as $href => $title) {
            if (!$href) {
                continue;
            }
            $link = new static();
            $link->content_id = $contentId;
            $link->object_model = $objectModel;
            $link->object_id = $objectId;
            $link->href = $href;
            $link->title = $title ?: $href;
            $link->save();
        }
    }
}
