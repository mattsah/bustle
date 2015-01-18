<?php namespace API
{
	use IW\HTTP;
	use Inkwell;
	use Dotink\Flourish;
	use Propel\Runtime\Map\TableMap;

	/**
	 *
	 */
	class ResourceController extends Inkwell\Controller
	{
		/**
		 *
		 */
		private $collection = NULL;


		/**
		 *
		 */
		private $entity = NULL;


		/**
		 *
		 */
		public function __construct(Flourish\Text $inflector)
		{
			$this['inflector'] = $inflector;
		}


		/**
		 *
		 */
		public function prepare($action, $context = array())
		{
			parent::prepare($action, $context);

			$this->collection = $this['request']->params->get('collection');
			$this->entity     = $this['inflector']->create($this->collection)->singularize()->camelize(TRUE);
		}


		/**
		 *
		 */
		public function handleCollection()
		{
			$entity_class = $this->entity->compose();
			$query_class  = $entity_class . 'Query';

			$this['response']->headers->set('Content-Type', 'application/json');

			switch ($this['request']->getMethod()) {
				case HTTP\GET:
					$query    = new $query_class();
					$page     = $this['request']->params->get('page',     1);
					$limit    = $this['request']->params->get('limit',    15);
					$filters  = $this['request']->params->get('filters',  array());
					$ordering = $this['request']->params->get('ordering', array());
					$result   = $query->create();

					foreach ($filters as $field => $value) {
						$result->where($field . ' = ?', $value);
					}

					foreach ($ordering as $field => $order) {
						$result->orderBy($field, $order);
					}

					$result = $result->limit($limit)->offset(($page - 1) * $limit)->find();
					$query  = new $query_class();
					$limit  = $this['request']->params->get('limit', 15);
					$result = $query->create()->limit($limit)->find();

					return $result->toArray(NULL, FALSE, TableMap::TYPE_CAMELNAME, TRUE);

				case HTTP\POST:
					$entity = new $entity_class();
					$values = $this['request']->params->get();

					$entity->fromArray($values, TableMap::TYPE_CAMELNAME)->save();

					return $entity->toArray(TableMap::TYPE_CAMELNAME, TRUE, array(), TRUE);
			}
		}


		/**
		 *
		 */
		public function handleEntity()
		{
			switch ($this['request']->getMethod()) {
				case HTTP\PUT:

				case HTTP\DELETE:


			}
		}
	}
}
