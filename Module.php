<?php

namespace humhub\modules\linklist;

use humhub\modules\comment\models\Comment;
use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\content\components\ContentContainerModule;
use humhub\modules\linklist\models\Category;
use humhub\modules\linklist\models\Link;
use humhub\modules\linklist\models\StreamLink;
use humhub\modules\post\models\Post;
use humhub\modules\space\models\Space;
use humhub\modules\user\models\User;
use yii\db\StaleObjectException;

class Module extends ContentContainerModule
{
    /**
     * @inheritdoc
     */
    public function getContentContainerTypes()
    {
        return [
            User::className(),
            Space::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function getContentContainerConfigUrl(ContentContainerActiveRecord $container)
    {
        return $container->createUrl('/linklist/linklist/config');
    }

    /**
     * @inheritdoc
     */
    public function disable()
    {
        foreach (Category::find()->all() as $category) {
            $category->delete();
        }

        parent::disable();
    }

    /**
     * @inheritdoc
     */
    public function enableContentContainer(ContentContainerActiveRecord $container)
    {
        $container->setSetting('enableDeadLinkValidation', 0, 'linklist');
        $container->setSetting('enableWidget', 0, 'linklist');
        parent::enableContentContainer($container);

        // Grep links from posts and related comments
        $posts = Post::find()
            ->joinWith('content')
            ->where(['content.contentcontainer_id' => $container->contentcontainer_id])
            ->all();
        $postIds = [];
        foreach ($posts as $post) {
            if (!empty($post->content->id) && !empty($post->message)) {
                $postIds[] = $post->id;
                StreamLink::updateLinks($post->message, $post->content->id, Post::class, $post->id);
            }
        }
        $comments = Comment::findAll(['object_model' => Post::class, 'object_id' => $postIds]);
        foreach ($comments as $comment) {
            if (!empty($comment->content->id) && !empty($comment->message)) {
                StreamLink::updateLinks($comment->message, $comment->content->id, Comment::class, $comment->id);
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function disableContentContainer(ContentContainerActiveRecord $container)
    {
        parent::disableContentContainer($container);

        foreach (Category::find()->contentContainer($container)->all() as $content) {
            $content->delete();
        }
        foreach (Link::find()->contentContainer($container)->all() as $content) {
            $content->delete();
        }

        $streamLinks = StreamLink::find()
            ->joinWith('content')
            ->where(['content.contentcontainer_id' => $container->contentcontainer_id])
            ->all();
        foreach ($streamLinks as $streamLink) {
            try {
                $streamLink->delete();
            } catch (StaleObjectException|\Exception $e) {
            }
        }
    }
}
