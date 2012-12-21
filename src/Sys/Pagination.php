<?php
/**
 * MIT License
 * ===========
 *
 * Copyright (c) 2012 Michael <michaelgranados@gmail.com>
 *
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the
 * "Software"), to deal in the Software without restriction, including
 * without limitation the rights to use, copy, modify, merge, publish,
 * distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 *
 * The above copyright notice and this permission notice shall be included
 * in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
 * OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
 * IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY
 * CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT,
 * TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
 * SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 * PHP Version 5.4
 * 
 * @category   Sys
 * @package    Sys
 * @subpackage Pagination
 * @author     Michael Granados <michaelgranados@gmail.com>
 * @copyright  2012 Michael Granados
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 * @version    GIT: 1.0
 * @link       http://dgmike.com.br
 */

namespace Sys;

/**
 * PHP Pagination Class
 * 
 * @category   Sys
 * @package    Sys
 * @subpackage Pagination
 * @author     Catch My Fame <admin@catchmyfame.com>
 * @author     Michael Granados <michaelgranados@gmail.com>
 * @copyright  2012 Michael Granados
 * @license    http://creativecommons.org/licenses/by-sa/3.0/ CC Attribution-ShareAlike 3.0 Unported (CC BY-SA 3.0)
 * @version    Release: 2.1.0
 * @link       https://github.com/dgmike/forum/blob/1.0/src/Sys/Pagination.php
 */
class Pagination
{
    public $items_per_page = 10;
    public $items_total;
    public $current_page   = 1;
    public $num_pages;
    public $mid_range      = 7;
    public $return;
    public $default_ipp    = 10;
    public $querystring;
    public $base_url       = '/';

    private function _mkPrevious($prev_page)
    {
        if ($this->current_page > 1 && $this->items_total >= 10) {
            return sprintf(
                '<a class="paginate" href="%s/%s">&laquo; Anterior</a> ',
                $this->base_url, $prev_page
            );
        }
        return '<span class="inactive" href="#">&laquo; Anterior</span> ';
    }

    private function _mkNext($next_page)
    {
        if (($this->current_page < $this->num_pages && $this->items_total >= 10)
            && $this->current_page > 0
        ) {
            return sprintf(
                '<a class="paginate" href="%s/%s">Próxima &raquo;</a> ',
                $this->base_url, $next_page
            );
        }
        return '<span class="inactive" href="#">&raquo; Próxima</span> ';
    }

    private function _mkLinkPage($i)
    {
        if ($i == $this->current_page) {
            return sprintf(
                '<a title="Ir para a página %d" class="current" href="#">%d</a> ',
                $i, $i
            );
        }
        return sprintf(
            '<a class="paginate" title="Ir para a página %d" href="%s/%d">%d</a> ',
            $i, $this->base_url, $i, $i
        );
    }

    public function paginate()
    {
        if (!is_numeric($this->items_per_page) || $this->items_per_page <= 0) {
            $this->items_per_page = $this->default_ipp;
        }
        $this->num_pages = ceil($this->items_total / $this->items_per_page);
        $prev_page = $this->current_page-1;
        $next_page = $this->current_page+1;
        if ($this->num_pages > 10) {
            $this->return = $this->_mkPrevious($prev_page);
            $this->start_range = $this->current_page - floor($this->mid_range/2);
            $this->end_range = $this->current_page + floor($this->mid_range/2);
            if ($this->start_range <= 0) {
                $this->end_range += abs($this->start_range)+1;
                $this->start_range = 1;
            }
            if ($this->end_range > $this->num_pages) {
                $this->start_range -= $this->end_range - $this->num_pages;
                $this->end_range = $this->num_pages;
            }
            $this->range = range($this->start_range, $this->end_range);
            for ($i=1;$i<=$this->num_pages;$i++) {
                if ($this->range[0] > 2 && $i == $this->range[0]) {
                    $this->return .= " ... ";
                }
                if (   $i==1 || $i==$this->num_pages
                    || in_array($i, $this->range)
                ) {
                    $this->return .= $this->_mkLinkPage($i);
                }
                if (   $this->range[$this->mid_range-1] < $this->num_pages-1
                    && $i == $this->range[$this->mid_range-1]
                ) { 
                    $this->return .= " ... ";
                }
            }
            $this->return .= $this->_mkNext($next_page);
        } else {
            for ($i=1;$i<=$this->num_pages;$i++) {
                $this->return .= $this->_mkLinkPage($i++);
            }
        }
        if ($this->current_page <= 0) {
            $this->items_per_page = 0;
        }
    }

    public function displayPages()
    {
        return $this->return;
    }
}
