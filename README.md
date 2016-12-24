# ScoLib/tree
是一个将数据格式化为树形结构的类库

[![StyleCI](https://styleci.io/repos/63778855/shield?branch=master)](https://styleci.io/repos/63778855)
[![Latest Stable Version](https://poser.pugx.org/scolib/tree/v/stable)](https://packagist.org/packages/scolib/tree)
[![Total Downloads](https://poser.pugx.org/scolib/tree/downloads)](https://packagist.org/packages/scolib/tree)
[![Latest Unstable Version](https://poser.pugx.org/scolib/tree/v/unstable)](https://packagist.org/packages/scolib/tree)
[![License](https://poser.pugx.org/scolib/tree/license)](https://packagist.org/packages/scolib/tree)

## 安装

执行命令:

 ```bash
 composer require scolib/tree
 ```

或 在 composer.json 文件中:

 ```
 "require": {
     "scolib/tree": "^1.0"
 }
 ```
并执行 ```composer update```

## 使用

```php
<?php 
namespace App\Repositories;

use Sco\Tree\Traits\TreeTrait;

class MyClass
{
    use TreeTrait;
    
    // 自定义属性（可选）
    protected $treeNodeIdName = 'id';
    protected $treeNodeParentIdName = 'parent_id';
    protected $treeSpacer = '&nbsp;&nbsp;&nbsp;';
    protected $treeFirstIcon = '&nbsp;&nbsp;&nbsp;│ ';
    protected $treeMiddleIcon = '&nbsp;&nbsp;&nbsp;├─ ';
    protected $treeLastIcon = '&nbsp;&nbsp;&nbsp;└─ ';
    
    /**
     * 获取待处理的原始节点数据
     * 
     * 必须实现
     * 
     * return \Illuminate\Support\Collection
     */
    public function getTreeAllNodes()
    {
        
    }
}
```

## 可用方法

```php
public function setAllNodes(Collection $nodes)
public function getSubLevel($parentId)
public function getDescendants($parentId, $depth = 0, $adds = '')
public function getLayerOfDescendants($id)
public function getSelf($id)
public function getParent($id)
public function getAncestors($id, $depth = 0)
```
