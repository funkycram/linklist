<?php

use humhub\modules\comment\models\Comment;
use humhub\modules\linklist\Events;
use humhub\modules\post\models\Post;
use humhub\modules\space\widgets\Menu;
use humhub\modules\space\widgets\Sidebar;
use humhub\modules\user\widgets\ProfileMenu;
use humhub\modules\user\widgets\ProfileSidebar;

return [
    'id' => 'linklist',
    'class' => 'humhub\modules\linklist\Module',
    'namespace' => 'humhub\modules\linklist',
    'events' => [
        ['class' => Menu::className(), 'event' => Menu::EVENT_INIT, 'callback' => [Events::class, 'onSpaceMenuInit']],
        ['class' => ProfileMenu::className(), 'event' => ProfileMenu::EVENT_INIT, 'callback' => [Events::class, 'onProfileMenuInit']],
        ['class' => Sidebar::className(), 'event' => Sidebar::EVENT_INIT, 'callback' => [Events::class, 'onSpaceSidebarInit']],
        ['class' => ProfileSidebar::className(), 'event' => ProfileSidebar::EVENT_INIT, 'callback' => [Events::class, 'onProfileSidebarInit']],
        ['class' => Post::class, 'event' => Post::EVENT_AFTER_INSERT, 'callback' => [Events::class, 'onPostAfterInsert']],
        ['class' => Comment::class, 'event' => Comment::EVENT_AFTER_INSERT, 'callback' => [Events::class, 'onCommentAfterInsert']],
        ['class' => Post::class, 'event' => Post::EVENT_AFTER_UPDATE, 'callback' => [Events::class, 'onPostAfterUpdate']],
        ['class' => Comment::class, 'event' => Comment::EVENT_AFTER_UPDATE, 'callback' => [Events::class, 'onCommentAfterUpdate']],
        ['class' => Post::class, 'event' => Post::EVENT_AFTER_DELETE, 'callback' => [Events::class, 'onPostAfterDelete']],
        ['class' => Comment::class, 'event' => Comment::EVENT_AFTER_DELETE, 'callback' => [Events::class, 'onCommentAfterDelete']],
    ],
];
?>
