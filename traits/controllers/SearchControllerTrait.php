<?php
namespace nitm\search\traits\controllers;

use yii\helpers\ArrayHelper;

/**
 * SearchController traits
	 */
trait SearchControllerTrait {
	
	public $namespace;
	public $type;
	
	protected $engine;
	protected $nestedPrefix = 'nested:';
	protected $forceType;
	
	/**
	 * Execute an Elastic search using the _search API instead of the ActiveRecord
	 * @param array $options
	 * @return array
	 */
	public function search($options=[], $modelOptions=[])
	{
		$options = array_merge([
			'types' => '_search',
			'q' => \Yii::$app->request->get('q'),
			'params' => \Yii::$app->request->get(),
			'with' => [], 
			'viewOptions' => [], 
			'construct' => [
				'queryOptions' => [
					'orderBy' => ['_score' => SORT_DESC]
				]
			],
			'limit' => \Yii::$app->request->get('limit') ? \Yii::$app->request->get('limit') : 10,
		], $options);
		
		$this->model->load($modelOptions);
		
		$this->type = $options['types'];
		if(count(explode(',', $this->type)) == 1)
			$this->model->setIs($this->type);
			
		//$this->model->setIndexType($this->type);
		
		//We can force types even if the user specified them in teh query string
		if(isset($options['forceType']))
			$this->forceType = $options['forceType'];	
		//Set filtering and url based options
		$params = \Yii::$app->request->get();
		$params['q'] = isset($params['q']) ? $params['q'] : $options['q'];
		
		/**
		 * Setup the query parts
		 */
		list($results, $dataProvider) = $this->model->getDataProvider($params, $options);
		return [$results, $dataProvider];
	}
}
?>