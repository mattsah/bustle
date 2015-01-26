<?php namespace API
{
	use IW\HTTP;
	use Inkwell;
	use Inkwell\Controller;
	use Propel\Runtime\Map\TableMap;
	use Dotink\Flourish;

	/**
	 *
	 */
	class ResourceController extends Controller\BaseController
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
			$this->inflector = $inflector;
		}


		/**
		 *
		 */
		public function __prepare($action, $context = array())
		{
			parent::__prepare($action, $context);

			$this->params     = $this->request->params;
			$this->collection = $this->inflector->create($this->params->get('collection'));
			$this->entity     = $this->collection->singularize();

			$this->response->headers->set('Content-Type', 'application/json');
		}


		/**
		 *
		 */
		public function handleCollection()
		{
			$entity_class = $this->entity->camelize(TRUE)->compose();
			$query_class  = $entity_class . 'Query';

			switch ($this->authorizeMethod([HTTP\GET, HTTP\POST])) {
				case HTTP\GET:
					$query    = new $query_class();
					$page     = $this->request->params->get('page',     1);
					$limit    = $this->request->params->get('limit',    15);
					$filters  = $this->request->params->get('filters',  array());
					$ordering = $this->request->params->get('ordering', array());
					$result   = $query->create();

					foreach ($filters as $field => $value) {
						$result->where($field . ' = ?', $value);
					}

					foreach ($ordering as $field => $order) {
						$result->orderBy($field, $order);
					}

					$result->limit($limit);
					$result->offset(($page - 1) * $limit);

					$data = $result->find()->toArray(NULL, FALSE, TableMap::TYPE_CAMELNAME, TRUE);
					break;

				case HTTP\POST:
					$entity = new $entity_class();
					$values = $this->request->params->get();

					$entity->fromArray($values, TableMap::TYPE_CAMELNAME)->save();

					$data = $entity->toArray(TableMap::TYPE_CAMELNAME, TRUE, array(), TRUE);
					break;
			}

			return $data;
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
