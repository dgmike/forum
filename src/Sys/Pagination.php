<?php

namespace Sys;

/*
 * PHP Pagination Class
 * @author admin@catchmyfame.com - http://www.catchmyfame.com
 * @version 2.0.0
 * @date October 18, 2011
 * @copyright (c) admin@catchmyfame.com (www.catchmyfame.com)
 * @license CC Attribution-ShareAlike 3.0 Unported (CC BY-SA 3.0) - http://creativecommons.org/licenses/by-sa/3.0/
 */
class Pagination
{
    var $items_per_page = 10;
    var $items_total;
    var $current_page   = 1;
    var $num_pages;
    var $mid_range      = 7;
    var $return;
    var $default_ipp    = 10;
    var $querystring;
    var $base_url       = '/';

    function _mkPrevious($prev_page)
    {
        if ($this->current_page > 1 && $this->items_total >= 10) {
            return sprintf(
                '<a class="paginate" href="%s/%s">&laquo; Anterior</a> ',
                $this->base_url, $prev_page
            );
        }
        return '<span class="inactive" href="#">&laquo; Anterior</span> ';
    }

    function _mkNext($next_page)
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

    function _mkLinkPage($i)
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

    function paginate()
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
                $this->start_range -= $this->end_range-$this->num_pages;
                $this->end_range = $this->num_pages;
            }
            $this->range = range($this->start_range,$this->end_range);
            for ($i=1;$i<=$this->num_pages;$i++) {
                if ($this->range[0] > 2 && $i == $this->range[0]) {
                    $this->return .= " ... ";
                }
                // loop through all pages. if first, last, or in range, display
                if ($i==1 || $i==$this->num_pages || in_array($i,$this->range)) {
                    $this->return .= $this->_mkLinkPage($i);
                }
                if ($this->range[$this->mid_range-1] < $this->num_pages-1 && $i == $this->range[$this->mid_range-1]) { 
                    $this->return .= " ... ";
                }
            }
            $this->return .= $this->_mkNext($next_page);
        } else {
            for ($i=1;$i<=$this->num_pages;$i++) {
                $this->return .= $this->_mkLinkPage($i);
            }
        }
        if ($this->current_page <= 0) {
            $this->items_per_page = 0;
        }
    }

    function display_pages()
    {
        return $this->return;
    }
}
