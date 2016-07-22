<?php

namespace ScoLib\Tree\Traits;


use InvalidArgumentException;
use ArrayAccess;
use BadMethodCallException;

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
        return property_exists($this, 'treeSpacer') ? $this->treeSpacer : '&nbsp;&nbsp;&nbsp;';
    }

    protected function getTreeFirstIcon()
    {
        return property_exists($this, 'treeFirstIcon') ? $this->treeFirstIcon : '&nbsp;&nbsp;&nbsp;│ ';
    }

    protected function getTreeMiddleIcon()
    {
        return property_exists($this, 'treeMiddleIcon') ? $this->treeMiddleIcon : '&nbsp;&nbsp;&nbsp;├─ ';
    }

    protected function getTreeLastIcon()
    {
        return property_exists($this, 'treeLastIcon') ? $this->treeLastIcon : '&nbsp;&nbsp;&nbsp;└─ ';
    }

    /**
     * 获取待格式树结构的节点数据
     * @return mixed
     */
    protected function getAllNodes()
    {
        if (!method_exists($this, 'getTreeAllNodes')) {
            throw new BadMethodCallException('Method [getTreeAllNodes] does not exist.');
        }

        $data = $this->getTreeAllNodes(); // 由use的class来实现

        if (!$data instanceof ArrayAccess) {
            throw new InvalidArgumentException('tree data must be a collection');
        }
        return $data;
    }

    /**
     * 获取子级（仅子代一级）
     *
     * @param mixed $parentId
     *
     * @return array
     */
    protected function getSubLevel($parentId)
    {
        $data = $this->getAllNodes();

        $childList = collect([]);
        foreach ($data as $val) {
            if ($val->{$this->getTreeDataParentIdName()} == $parentId) {
                $childList->put($val->{$this->getTreeDataIdName()}, $val);
            }
        }
        return $childList;
    }

    /**
     * 获取指定节点的所有后代
     * @param mixed $parentId
     * @param int $depth
     * @param string $adds
     *
     * @return \Illuminate\Support\Collection
     */
    protected function getDescendants($parentId, $depth = 0, $adds = '')
    {
        static $array;
        if (!$array instanceof ArrayAccess || $depth == 0) {
            $array = collect([]);
        }
        $number = 1;
        $child = $this->getSubLevel($parentId);
        if ($child) {
            $nextDepth = $depth + 1;
            $total = $child->count();
            foreach ($child as $val) {
                $j = $k = '';
                if ($number == $total) {
                    $j .= $this->getTreeLastIcon();
                } else {
                    $j .= $this->getTreeMiddleIcon();
                    $k = $adds ? $this->getTreeFirstIcon() : '';
                }

                $val->spacer = $adds ? ($adds . $j) : '';

                $val->depth = $depth;
                $array->put($val->{$this->getTreeDataIdName()}, $val);
                $this->getDescendants($val->{$this->getTreeDataIdName()}, $nextDepth, $adds . $k . $this->getTreeSpacer());
                $number++;
            }
        }
        return $array;
    }

    /**
     * 获取指定节点的所有后代（分层级）
     * @param mixed $id
     *
     * @return \Illuminate\Support\Collection
     */
    public function getLayerOfDescendants($id)
    {
        $child = $this->getSubLevel($id);
        $data  = collect([]);
        if ($child) {
            foreach ($child as $val) {
                $val->child = $this->getLayerOfDescendants($val->{$this->getTreeDataIdName()});
                $data->put($val->{$this->getTreeDataIdName()}, $val);

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
    /*public function getParent($id)
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
    }*/




    /**
     * 得到当前位置数组（二维数组）
     *
     * @param integer $id
     *
     * @return array
     */
    /*public function getAncestors($id)
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
    }*/

}