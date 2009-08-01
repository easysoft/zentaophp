<?php
/**
 * The model file of compress module of ZenTaoPHP.
 *
 * ZenTaoPHP is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * ZenTaoPHP is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public License
 * along with ZenTaoPHP.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @copyright   Copyright: 2009 Chunsheng Wang
 * @author      Chunsheng Wang <wwccss@263.net>
 * @package     ZenTaoPHP
 * @version     $Id: model.php 1113 2009-03-18 12:00:06Z wwccss $
 */
?>
<?php
class compressModel extends model
{
    /* ¹¹Ôìº¯Êý¡£*/
    public function __construct()
    {
        parent::__construct();
    }

    /* Ñ¹Ëõcss¡£*/
    public function compressCSS($cssContent)
    {
        if(empty($cssContent)) return false;
        $cssContent = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!','',$cssContent);
        $cssContent = str_replace(array("\r\n", "\r", "\n", "\t"), "", $cssContent);
        return $cssContent;
    }
}
