<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2021 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\linklist;

use humhub\modules\comment\models\Comment;
use humhub\modules\linklist\models\StreamLink;
use humhub\modules\post\models\Post;
use Yii;
use yii\db\AfterSaveEvent;

class Events
{
    /**
     * Defines what to do if a spaces sidebar is initialzed.
     *
     * @param $event
     */
    public static function onSpaceSidebarInit($event)
    {

        $space = $event->sender->space;
        if ($space->isModuleEnabled('linklist')) {
            $event->sender->addWidget(widgets\Sidebar::className(), array('contentContainer' => $space), array(
                'sortOrder' => 200,
            ));
        }
    }

    /**
     * On build of a Space Navigation, check if this module is enabled.
     * When enabled add a menu item
     *
     * @param $event
     */
    public static function onSpaceMenuInit($event)
    {

        $space = $event->sender->space;
        if ($space->isModuleEnabled('linklist') && $space->isMember()) {
            $event->sender->addItem(array(
                'label' => Yii::t('LinklistModule.base', 'Linklist'),
                'group' => 'modules',
                'url' => $space->createUrl('/linklist/linklist'),
                'icon' => '<i class="fa fa-link"></i>',
                'isActive' => (Yii::$app->controller->module && Yii::$app->controller->module->id == 'linklist')
            ));
        }
    }

    /**
     * On build of a Profile Navigation, check if this module is enabled.
     * When enabled add a menu item
     *
     * @param $event
     */
    public static function onProfileMenuInit($event)
    {
        $user = $event->sender->user;

        // Is Module enabled on this workspace?
        if ($user->isModuleEnabled('linklist') && !Yii::$app->user->isGuest && $user->id == Yii::$app->user->id) {
            $event->sender->addItem(array(
                'label' => Yii::t('LinklistModule.base', 'Linklist'),
                'url' => $user->createUrl('/linklist/linklist'),
                'icon' => '<i class="fa fa-link"></i>',
                'isActive' => (Yii::$app->controller->module && Yii::$app->controller->module->id == 'linklist'),
            ));
        }
    }

    /**
     * Defines what to do if a spaces sidebar is initialzed.
     *
     * @param $event
     */
    public static function onProfileSidebarInit($event)
    {
        $user = $event->sender->user;

        if ($user->isModuleEnabled('linklist')) {
            $event->sender->addWidget(widgets\Sidebar::className(), array('contentContainer' => $user), array(
                'sortOrder' => 200,
            ));
        }
    }

    /**
     * @param AfterSaveEvent $event
     */
    public static function onPostAfterInsert($event)
    {
        if (!isset($event->sender)) {
            return;
        }

        /** @var Post $post */
        $post = $event->sender;

        if (!empty($post->content->id) && !empty($post->message)) {
            StreamLink::updateLinks($post->message, $post->content->id, Post::class, $post->id);
        }
    }

    /**
     * @param AfterSaveEvent $event
     */
    public static function onCommentAfterInsert($event)
    {
        if (!isset($event->sender)) {
            return;
        }

        /** @var Comment $comment */
        $comment = $event->sender;

        if ($comment->object_model === Post::class && !empty($comment->content->id) && !empty($comment->message)) {
            StreamLink::updateLinks($comment->message, $comment->content->id, Comment::class, $comment->id);
        }
    }

    /**
     * @param AfterSaveEvent $event
     */
    public static function onPostAfterUpdate($event)
    {
        if (!isset($event->sender, $event->changedAttributes)) {
            return;
        }

        /** @var Post $post */
        $post = $event->sender;

        if (!empty($post->content->id) && !empty($post->message)) {
            StreamLink::updateLinks($post->message, $post->content->id, Post::class, $post->id);
        }
    }

    /**
     * @param AfterSaveEvent $event
     */
    public static function onCommentAfterUpdate($event)
    {
        if (!isset($event->sender, $event->changedAttributes)) {
            return;
        }

        /** @var Comment $comment */
        $comment = $event->sender;

        if ($comment->object_model === Post::class && !empty($comment->content->id) && !empty($comment->message)) {
            StreamLink::updateLinks($comment->message, $comment->content->id, Comment::class, $comment->id);
        }
    }

    /**
     * @param AfterSaveEvent $event
     */
    public static function onPostAfterDelete($event)
    {
        if (!isset($event->sender)) {
            return;
        }

        /** @var Post $post */
        $post = $event->sender;

        if ($post) {
            StreamLink::deleteAll(['object_model' => Post::class, 'object_id' => $post->id]);
        }
    }

    /**
     * @param AfterSaveEvent $event
     */
    public static function onCommentAfterDelete($event)
    {
        if (!isset($event->sender)) {
            return;
        }

        /** @var Comment $comment */
        $comment = $event->sender;

        if ($comment) {
            StreamLink::deleteAll(['object_model' => Comment::class, 'object_id' => $comment->id]);
        }
    }
}