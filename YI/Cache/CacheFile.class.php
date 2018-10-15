<?php 
// +----------------------------------------------------------------------
// | YiFramework
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://yisong.sinaapp.com
// +----------------------------------------------------------------------
// | Licensed
// +----------------------------------------------------------------------
// | Author: Devin.yang<yi.pluto@163.com>
// +----------------------------------------------------------------------

/**
 +------------------------------------------------------------------------------
 * 文件缓存类
 +------------------------------------------------------------------------------
 * @category   Yi
 * @package  Yi
 * @subpackage  Cache
 * @author    Devin.yang<yi.pluto@163.com>
 * @version   $Id$
 +------------------------------------------------------------------------------
 */
 Class CacheFile extends Cache{
	/**
	 *目录
	 *
	 *@var string
	 *@access private
	 */
	private $_cacheDir;
	private $_cacheFileSuffix;
	private $_cacheFileName;
	
	/**
	 * 初始化
	 *
	 */
	public function __construct(){
		$cacheDir = Yi::app()->config('Cache.dir');
		$this->_cacheDir = !empty($cacheDir) ? $cacheDir : Yi::app()->getModulePath() . '/Runtime/Cache/Data/';
		@chmod($this->_cacheDir,0777);
		if(!is_writable($this->_cacheDir)){
			throw new YiException('缓存文件夹' . $this->_cacheDir . '不可写！');
		}
		$cacheFileSuffix = Yi::app()->config('Cache.suffix');
		$this->_cacheFileSuffix = !empty($cacheFileSuffix) ? $cacheFileSuffix : '.cache';
	}
	
	/**
	 * 添加一个缓存
	 *
	 * @param String $key    缓存Key
	 * @param mixed $value   缓存内容
	 * @param int $expire    缓存时间(秒)
	 * @return boolean       是否添加成功
	 * @access public
	 */
	public function add($key,$value,$expire=1800){
         if(is_file($this->getCacheFileName($key))){
            return false;
         }
         $cacheData['value'] = $value;
         $cacheData['expire'] = $expire;
         //以JSON格式写入文件
         if(file_put_contents($this->getCacheFileName($key), json_encode($cacheData))){
            return true;
         }else{
            return false;
         }
         
    }
	
	/**
	 * 添加一个缓存值，如果已经存在，则覆写
	 *
	 * @param String $key    缓存Key
	 * @param mixed $value   缓存内容
	 * @param int $expire    缓存时间(秒)
	 * @return boolean       是否缓存成功
	 * @access public
	 */
	public function set($key,$value,$expire = 60){
		$cacheData['value'] = $value;
        $cacheData['expire'] = $expire;
        if(file_put_contents($this->getCacheFileName($key), json_encode($cacheData),LOCK_EX)){
            return true;
         }else{
            return false;
         }
	}
	
	/**
	 * 获取一个已经缓存的变量
	 *
	 * @param string $key   缓存Key
	 * @return string       缓存内容
	 * @access public
	 */
	public function get($key){
		if(!is_file($this->getCacheFileName($key))){
            return false;
        }
        $data = $this->objectToArray(json_decode(file_get_contents($this->_cacheFileName)));
         
        if($this->checkIsvalid($data['expire'])){
            unset($data['expire']);
            return $data['value'];
        }else{
            unlink($this->_cacheFileName);
            return false;
        } 
	}
	
	/**
	 * 删除一个已经缓存的变量
	 *
	 * @param  $key
	 * @return boolean       是否删除成功
	 * @access public
	 */	
	public function del($key){
        if(is_file($this->getCacheFileName($key))){
            if(unlink($this->getCacheFileName($key)))
                return true;
            else
                return false;
        }else{
            return true;
        }
    }
	
	/**
	 * 删除全部缓存变量
	 *
	 * @return boolean       是否删除成功
	 * @access public
	 */
	public function delAll(){
        $this->deleteFile($this->_cacheDir);
    }
	
	/**
	 * 检测是否存在对应的缓存
	 *
	 * @param string $key   缓存Key
	 * @return boolean      是否存在key
	 * @access public
	 */
	public function has($key) {
		return (is_file($this->getCacheFileName($key)) === NULL ? false : true);
	}
	
	/**
	 * 检查有效时间
	 *
	 * @param int $expire   缓存时间
	 * @return boolean      是否有效
	 * @access private
	 */
    private function checkIsvalid($expire = 0) {
        if (!(@$mtime = filemtime($this->_cacheFileName))){
			return false;
		}
        if (time() - $mtime > $expire) return false;
        return true;
    }
	
	/**
	 * 获取缓存文件包含路径
	 *
	 * @param string $key   缓存key
	 * @return string       缓存文件包含路径
	 * @access private
	 */
	private function getCacheFileName($key){
		$this->_cacheFileName =  $this->_cacheDir . DIRECTORY_SEPARATOR . md5($key).$this->_cacheFileSuffix;
        return $this->_cacheFileName;
	}
	
	/**
	 * 删除目录下的所有文件
	 *
	 * @param string $dir   缓存路径
	 * @param boolean $mode  true删除所有 false删除过期
	 * @access private
	 */
    private function deleteFile($dir,$mode=true) { 
		$files = scandir($dir);
		$files = array_diff($files, array('.', '..'));
		foreach($files as $file) {
			if(!is_dir($dir . $file)){
				if($mode){
                    unlink($dir . $file); 
                }else{
                    $this->_cacheFileName = $dir . $file;
                    if(!$this->getIsvalidByPath($dir . $file)){
						unlink($dir . $file);
					}
                }
			}else{
				$this->deleteFile($dir . $file . '/',$mode);
			}
		}
    }
	
	/**
	 * 检查路径下文件是否有效
	 *
	 * @param string $path   缓存路径
	 * @return boolean       是否有效
	 * @access private
	 */
	private function getIsvalidByPath($path){
        $data = $this->objectToArray(json_decode(file_get_contents($path)));
        return $this->checkIsvalid($data['expire']);
    }
	
	/**
	 * object对象转换为数组
	 *
	 * @param object $obj   对象
	 * @return array        数组
	 * @access private
	 */
    private function objectToArray($obj){
        $_arr = is_object($obj) ? get_object_vars($obj) : $obj; 
        foreach ($_arr as $key => $val) { 
            $val = (is_array($val) || is_object($val)) ? $this->objectToArray($val) : $val; 
            $arr[$key] = $val; 
        } 
        return $arr; 
    }
 }