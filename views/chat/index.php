<?php

use yii\helpers\Html;
use yii\widgets\ListView;
use kartik\icons\Icon;
use nitm\widgets\replies\ChatModal;
use nitm\models\Replies;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var app\models\search\Chat $searchModel
 */

$title = Yii::t('app', 'Chat');
switch(\Yii::$app->request->isAjax)
{
	case true:
	$this->title = $title;
	break;
}
$this->params['breadcrumbs'][] = $title;
if($useModal == true) {
	$modalOptions =[
		'class' => 'btn btn-success',
		'data-toggle' => 'modal',
		'data-target' => '#replies-modal-form'
	];
} else {
	$modalOptions = ['class' => 'btn btn-success'];
}

$withForm = isset($withForm) ? $withForm : \Yii::$app->request->get(Replies::FORM_PARAM);

?>
<?php
	$listOptions = is_array($listOptions) ? $listOptions : [
		'class' => 'chat-messages',
		'role' => 'chatMessages',
	];
	$messages = ListView::widget([
		'layout' => "{items}\n{pager}",
		'options' => $listOptions,
		'dataProvider' => $dataProvider,
		'itemOptions' => ['class' => 'item'],
		'itemView' => function ($model, $key, $index, $widget) use ($primaryModel) {
				return $widget->render('@nitm/views/chat/view',['model' => $model, 'primaryModel' => $primaryModel]);
		},
		'pager' => [
			'class' => \kop\y2sp\ScrollPager::className(),
			'container' => '.chat-messages',
			'eventOnScroll' => 'function() {alert("scrollin")}',
			'negativeMargin' => 5,
			'triggerText' => 'More Replies'
		]
	
	]);
	$form = ($withForm == true) ? \nitm\widgets\replies\ChatForm::widget(['model' => $primaryModel]) : '';
	echo (\Yii::$app->request->isAjax) ? $messages.$form : Html::tag('div', $messages.$form, $options);
?>
<?php
	if(isset($modal))
	{
		echo $modal;
	}
?>
<script type="text/javascript">
$nitm.onModuleLoad('replies', function () {
	$nitm.module('replies').init("chat");
	$nitm.module('replies').initChatTabs("chat-navigation");
	<?php if($updateOptions['enabled']): ?>
	$nitm.module('replies').initChatActivity("chat-navigation", "<?= $updateOptions['url'] ?>", <?= $updateOptions['interval']; ?>);
	<?php endif; ?>
});
</script>
