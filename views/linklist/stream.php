<?php
/**
 * @var ContentContainerActiveRecord $contentContainer
 * @var StreamLink[] $searchModel
 * @var ActiveDataProvider $dataProvider
 */

use humhub\libs\Html;
use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\linklist\models\StreamLink;
use humhub\modules\user\widgets\Image;
use humhub\widgets\Button;
use humhub\widgets\GridView;
use yii\data\ActiveDataProvider;

humhub\modules\linklist\Assets::register($this);
?>
<div class="panel panel-default">
    <div class="panel-heading">
        <?= Button::back($contentContainer->createUrl('/linklist/linklist/index'))->right()->sm() ?>

        <strong><?= Yii::t('LinklistModule.base', 'Posted links') ?></strong>
        <br>
        <?= Yii::t('LinklistModule.base', 'Links from the stream (posts or comments)') ?>
    </div>

    <div class="panel-body">

        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'columns' => [
                [
                    'attribute' => 'linklist_stream.title',
                    'format' => 'raw',
                    'value' => static function (StreamLink $model) {
                        return
                            Html::a(
                                Html::tag('span', Html::encode($model->title), ['class' => 'title']),
                                $model->href,
                                ['target' => '_blank']
                            );
                    }
                ],
                [
                    'attribute' => 'content.created_at',
                    'label' => Yii::t('LinklistModule.base', 'Updated'),
                    'format' => 'raw',
                    'value' => static function (StreamLink $model) {
                        return Yii::$app->formatter->asDatetime($model->content->created_at, 'short');
                    }
                ],
                [
                    'header' => Yii::t('LinklistModule.base', 'Source'),
                    'format' => 'raw',
                    'value' => static function (StreamLink $model) {
                        return
                            Button::defaultType()->link($model->getSourceUrl())->icon('external-link')->sm();
                    }
                ],
                [
                    'attribute' => 'content.created_by',
                    'label' => Yii::t('LinklistModule.base', 'Creator'),
                    'format' => 'raw',
                    'value' => static function (StreamLink $model) {
                        $user = $model->content->createdBy;
                        if (!$user || !$user->isActive()) {
                            return '';
                        }
                        return
                            Image::widget(['user' => $user, 'width' => 20, 'showTooltip' => true]) .
                            ' ' .
                            Html::containerLink($user);
                    }
                ],
            ],
        ]) ?>
    </div>
</div>
