<?php

namespace ScoLib\Tree\Traits;


use InvalidArgumentException, ArrayAccess, BadMethodCallException;

trait TreeTrait
{

    /**
     * 数据主ID名
     * @return string
     */
    protected function getTreeDataIdName()
    {
        return property_exists($this, 'treeDataIdName') ? $this->treeDataIdName : 'id';
    }

    /**
     * 数据父ID名
     * @return string
     */
    protected function getTreeDataParentIdName()
    {
        return property_exists($this, 'treeDataParentIdName') ? $this->treeDataParentIdName
            : 'parent_id';
    }

    protected function getTreeSpacer()
    {
        return property_exists($this, 'treeSpacer') ? $this->treeSpacer : '&nbsp;&nbsp;';
    }

    protected function getTreeFirstIcon()
    {
        return property_exists($this, 'treeFirstIcon') ? $this->treeFirstIcon : '│';
    }

    protected function getTreeMiddleIcon()
    {
        return property_exists($this, 'treeMiddleIcon') ? $this->treeMiddleIcon : '├';
    }

    protected function getTreeLastIcon()
    {
        return property_exists($this, 'treeLastIcon') ? $this->treeLastIcon : '└';
    }

    /**
     * 获取待格式树结构的数据
     * @return mixed
     */
    protected function getData()
    {
        $data = $this->getTreeData(); // 由use的class来实现
        if (!method_exists($this, 'getTreeData')) {
            throw new BadMethodCallException('Method [getTreeData] does not exist.');
        }

        if (!$data instanceof ArrayAccess) {
            throw new InvalidArgumentException('tree data must is collection');
        }
        return $data;
    }

    /**
     * 得到子级数组（仅子代一级）
     *
     * @param mixed $parentId
     *
     * @return array
     */
    protected function getSiblingsChildren($parentId)
    {
        $data = $this->getData();

        $childList = collect([]);
        foreach ($data as $val) {
            if ($val->{$this->getTreeDataParentIdName()} == $parentId) {
                $childList->put($val->{$this->getTreeDataIdName()}, $val);
            }
        }
        return $childList;
    }

    /**
     * 获取指定节点的所有子级
     * @param mixed $parentId
     * @param int $depth
     *
     * @return \Illuminate\Support\Collection
     */
    protected function getAllChildren($parentId, $depth = 0)
    {
        static $array;
        if (!$array instanceof ArrayAccess || $depth == 0) {
            $array = collect([]);
        }
        $child = $this->getSiblingsChildren($parentId);
        if ($child) {
            $depth++;
            foreach ($child as $val) {
                //$val->depth = $depth;
                $array->put($val->{$this->getTreeDataIdName()}, $val);
                $this->getAllChildren($val->{$this->getTreeDataIdName()}, $depth);
            }
        }
        return $array;
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