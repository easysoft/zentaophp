<?php
/**
 * The control file of index module of ZenTaoPHP.
 *
 * When requests the root of a website, this index module will be called.
 *
 * ZenTaoPHP is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.

 * ZenTaoPHP is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public License
 * along with ZenTaoPHP.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @copyright   Copyright 2009-2010 Chunsheng Wang
 * @author      Chunsheng Wang <wwccss@263.net>
 * @package     ZenTaoPHP
 * @version     $Id$
 * @link        http://www.zentao.cn
 */
class index extends control
{
    /* 构造函数。*/
    public function __construct()
    {
        parent::__construct();
    }

    /* index方法，也是默认的方法。*/
    public function index()
    {
        $header['title'] = $this->lang->welcome;
        $this->assign('header',  $header);
        $this->display();
    }
}
