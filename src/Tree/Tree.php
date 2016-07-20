<?php

namespace ScoLib\Tree;

/**
 * 通用树型类
 */
class Tree
{

    /**
     * 默认配置，自定义配置可在config文件里加TREE_CONFIG
     *
     * @var array
     */
    protected $_config = [
        'TREE_ID'        => 'id',                    // 字段ID
        'TREE_PARENT_ID' => 'pid',                   // 父级字段ID
        'TREE_ICON'      => ['│', '├', '└'],     // 生成树型结构所需修饰符号，可以换成图片
        'TREE_NBSP'      => '&nbsp;&nbsp;',          // 饰符号
    ];

    /**
     * 生成树型结构所需要的2维数组
     *
     * @var array
     */
    public $data = [];

    /**
     * 临时参数
     *
     * @var array
     */
    private $childsTmp = [];
    private $posTmp    = [];
    private $arrTmp    = [];

    /**
     * 构造函数，初始化类
     *
     * @param array 2维数组，例如：
     *              array(
     *              1 => array('id'=>'1','pid'=>0,'name'=>'一级栏目一'),
     *              2 => array('id'=>'2','pid'=>0,'name'=>'一级栏目二'),
     *              3 => array('id'=>'3','pid'=>1,'name'=>'二级栏目一'),
     *              4 => array('id'=>'4','pid'=>1,'name'=>'二级栏目二'),
     *              5 => array('id'=>'5','pid'=>2,'name'=>'二级栏目三'),
     *              6 => array('id'=>'6','pid'=>3,'name'=>'三级栏目一'),
     *              7 => array('id'=>'7','pid'=>3,'name'=>'三级栏目二'),
     *              8 => array('id'=>'8','pid'=>7,'name'=>'四级栏目一'),
     *              )
     */
    public function __construct($data)
    {
        if (C('TREE_CONFIG')) {
            $this->_config = array_merge($this->_config, C('TREE_CONFIG'));
        }
        $this->data = $data;
    }

    public static function getInstance($data)
    {
        $class = new Tree($data);
        return $class;
    }

    /**
     * 得到父级数组（仅父代一级）
     *
     * @param integer $id
     *
     * @return array
     */
    public function getParent($id)
    {
        $data = [];
        if (!isset($this->data[$id])) {
            return false;
        }
        $pid = $this->data[$id][$this->_config['TREE_PARENT_ID']];
        $pid = $this->data[$pid][$this->_config['TREE_PARENT_ID']];
        if (is_array($this->data)) {
            foreach ($this->data as $key => $val) {
                if ($val[$this->_config['TREE_PARENT_ID']] == $pid) {
                    $data[$key] = $val;
                }
            }
        }
        return $data;
    }

    /**
     * 得到子级数组（仅子代一级）
     *
     * @param integer $id
     *
     * @return array
     */
    public function getChild($id)
    {
        $data = [];
        if (is_array($this->data)) {
            foreach ($this->data as $key => $val) {
                if ($val[$this->_config['TREE_PARENT_ID']] == $id) {
                    $data[$key] = $val;
                }
            }
        }
        return $data ?: false;
    }

    /**
     * 得到所有子级数组（二维数组）
     *
     * @param integer $id
     *
     * @return array
     */
    public function getChilds($id)
    {
        $child = $this->getChild($id);
        if ($child) {
            foreach ($child as $key => $val) {
                $this->childsTmp[$key] = $val;
                $this->getChilds($val['id']);
            }
        }
        return $this->childsTmp;
    }

    public function getMultiChild($id)
    {
        $child = $this->getChild($id);
        $data  = [];
        if ($child) {
            foreach ($child as $key => $val) {
                $data[$key]           = $val;
                $data[$key]['_child'] = $this->getMultiChild($val['id']);
            }
        }
        return $data;
    }

    /**
     * 得到当前位置数组（二维数组）
     *
     * @param integer $id
     *
     * @return array
     */
    public function getPos($id)
    {
        $return = [];
        if (!isset($this->data[$id])) {
            return false;
        }
        $this->posTmp[] = $this->data[$id];
        $pid            = $this->data[$id][$this->_config['TREE_PARENT_ID']];
        if (isset($this->data[$pid])) {
            $this->getPos($pid);
        }
        if (is_array($this->posTmp)) {
            krsort($this->posTmp);
            foreach ($this->posTmp as $val) {
                $return[$val[$this->_config['TREE_ID']]] = $val;
            }
        }
        return $return;
    }


    /**
     * 格式化数组(二维数组)
     *
     * @param integer $id
     * @param string  $adds
     *
     * @return array
     */
    function getArray($id = 0, $adds = '')
    {
        $number = 1;
        $child  = $this->getChild($id);
        if (is_array($child)) {
            $total = count($child);
            foreach ($child as $key => $val) {
                $j = $k = '';
                if ($number == $total) {
                    $j .= $this->_config['TREE_ICON'][2];
                } else {
                    $j .= $this->_config['TREE_ICON'][1];
                    $k = $adds ? $this->_config['TREE_ICON'][0] : $this->_config['TREE_ICON'][0];
                }
                $spacer                                        = $adds ? $adds . $j : '';
                $val['spacer']                                 = $spacer;
                $this->arrTmp[$val[$this->_config['TREE_ID']]] = $val;
                $this->getArray($key, $adds . $k . $this->_config['TREE_NBSP']);
                $number++;
            }
        }

        return $this->arrTmp;
    }
}
