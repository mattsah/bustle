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
		private $collection = NULL;

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

			return $data;
		}


		/**
		 *
		 */
		public function handleEntity()
		{

		}
	}
}
