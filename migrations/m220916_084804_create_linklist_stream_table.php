<?php

use humhub\modules\comment\models\Comment;
use humhub\modules\content\models\ContentContainerModuleState;
use humhub\modules\linklist\models\StreamLink;
use humhub\modules\post\models\Post;
use yii\db\Migration;

/**
 * Handles the creation of table `{{%linklist_stream}}`.
 */
class m220916_084804_create_linklist_stream_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%linklist_stream}}', [
            'id' => $this->primaryKey(),
            'content_id' => $this->integer(11)->notNull(),
            'object_model' => $this->string(100)->notNull(),
            'object_id' => $this->integer(11)->notNull(),
            'href' => $this->string(2047)->notNull(),
            'title' => $this->string(255),
        ]);

        // Grep links from posts and related comments, in all container where the module is activated
        foreach (ContentContainerModuleState::findAll(['module_id' => 'linklist', 'module_state' => 1]) as $contentContainerModule) {
            $posts = Post::find()
                ->joinWith('content')
                ->where(['content.contentcontainer_id' => $contentContainerModule->contentcontainer_id])
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
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220916_084804_create_linklist_stream_table cannot be reverted.\n";

        return false;
    }
}
