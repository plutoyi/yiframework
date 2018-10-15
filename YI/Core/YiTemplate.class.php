<?php 
	class YiTemplate{
		/**
		 * 模板后缀名
		 *
		 * @var string
		 */
		private $suffix = '.php';
		/**
		 * 模板变量
		 *
		 * @var array
		 */
		private $value;
		/**
		 * 模板引擎实例
		 *
		 * @var object
		 */
		private static $instance = null;

		/**
		 * 取得模板引擎的实例
		 *
		 * @return objeact
		 * @access public
		 * @static
		 */
		public static function getInstance() {
			if (is_null(self::$instance)) {
				self::$instance = new YiTemplate();
			}
			return self::$instance;
		}

		public function show($file) {
			//模板文件名
			$file = str_replace('.', '/', $file) . $this->suffix;
			//模板目录
			$templateDir = Yi::app()->getModulePath() . '/View/';
			//模板文件目录
			$tplFile = $templateDir . $file;

			if(is_file($tplFile)) {
				@extract($this->value);
				ob_start();
				include_once($tplFile);
				$content = ob_get_contents();
				ob_end_clean();
				exit($content);
			} else {
				throw new YiException('找不到模板文件:' . $tplFile);
			}
		}

		/**
		 * 设置视图变量
		 *
		 * @access public
		 * @param mixed $key       视图变量名
		 * @param string $value    视图变量数值
		 * @return mixed
		 */
		public function assign($key, $value = null) {

			//参数分析
			if(!$key) {
				return false;
			}

			//当$key为数组时
			if(is_array($key)) {
				foreach ($key as $k=>$v) {
					$this->value[$k] = $v;
				}
			} else {
				$this->value[$key] = $value;
			}

			return true;
		}

		/**
		 * 获取已经分配的模板值
		 *
		 * @param string $key
		 * @return mixed
		 */
		public function getValue($key) {
			return isset($this->value[$key]) ? $this->value[$key] : null;
		}

		/**
		 * 设置模板后缀名
		 *
		 * @param string $suffix 后缀名
		 * @return void
		 */
		public function setSuffix($suffix = '.html') {
			$this->suffix = ($suffix{0} == '.' ? $suffix : '.' . $suffix);
		}

	}