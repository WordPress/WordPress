<?php
/**
 * Copyright (c) 2009 - 2010, RealDolmen
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name of RealDolmen nor the
 *       names of its contributors may be used to endorse or promote products
 *       derived from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY RealDolmen ''AS IS'' AND ANY
 * EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL RealDolmen BE LIABLE FOR ANY
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @category   Microsoft
 * @package    Microsoft_WindowsAzure
 * @subpackage Storage
 * @copyright  Copyright (c) 2009 - 2010, RealDolmen (http://www.realdolmen.com)
 * @license    http://phpazure.codeplex.com/license
 * @version    $Id: Blob.php 14561 2009-05-07 08:05:12Z unknown $
 */

/**
 * @category   Microsoft
 * @package    Microsoft_WindowsAzure
 * @subpackage Storage
 * @copyright  Copyright (c) 2009 - 2010, RealDolmen (http://www.realdolmen.com)
 * @license    http://phpazure.codeplex.com/license
 */
class Microsoft_WindowsAzure_Storage_TableEntityQuery
{
    /**
     * From
     * 
     * @var string
     */
	protected $_from  = '';
	
	/**
	 * Where
	 * 
	 * @var array
	 */
	protected $_where = array();
	
	/**
	 * Order by
	 * 
	 * @var array
	 */
	protected $_orderBy = array();
	
	/**
	 * Top
	 * 
	 * @var int
	 */
	protected $_top = null;
	
	/**
	 * Partition key
	 * 
	 * @var string
	 */
	protected $_partitionKey = null;

	/**
	 * Row key
	 * 
	 * @var string
	 */
	protected $_rowKey = null;
	
	/**
	 * Select clause
	 * 
	 * @return Microsoft_WindowsAzure_Storage_TableEntityQuery
	 */
	public function select()
	{
		return $this;
	}
	
	/**
	 * From clause
	 * 
	 * @param string $name Table name to select entities from
	 * @return Microsoft_WindowsAzure_Storage_TableEntityQuery
	 */
	public function from($name)
	{
		$this->_from = $name;
		return $this;
	}
	
	/**
	 * Specify partition key
	 * 
	 * @param string $value Partition key to query for
	 * @return Microsoft_WindowsAzure_Storage_TableEntityQuery
	 */
	public function wherePartitionKey($value = null)
	{
	    $this->_partitionKey = $value;
	    return $this;
	}
	
	/**
	 * Specify row key
	 * 
	 * @param string $value Row key to query for
	 * @return Microsoft_WindowsAzure_Storage_TableEntityQuery
	 */
	public function whereRowKey($value = null)
	{
	    $this->_rowKey = $value;
	    return $this;
	}
	
	/**
	 * Add where clause
	 * 
	 * @param string       $condition   Condition, can contain question mark(s) (?) for parameter insertion.
	 * @param string|array $value       Value(s) to insert in question mark (?) parameters.
	 * @param string       $cond        Condition for the clause (and/or/not)
	 * @return Microsoft_WindowsAzure_Storage_TableEntityQuery
	 */
	public function where($condition, $value = null, $cond = '')
	{
	    $condition = $this->_replaceOperators($condition);
	    
	    if (!is_null($value)) {
	        $condition = $this->_quoteInto($condition, $value);
	    }
	    
		if (count($this->_where) == 0) {
			$cond = '';
		} else if ($cond !== '') {
			$cond = ' ' . strtolower(trim($cond)) . ' ';
		}
		
		$this->_where[] = $cond . $condition;
		return $this;
	}

	/**
	 * Add where clause with AND condition
	 * 
	 * @param string       $condition   Condition, can contain question mark(s) (?) for parameter insertion.
	 * @param string|array $value       Value(s) to insert in question mark (?) parameters.
	 * @return Microsoft_WindowsAzure_Storage_TableEntityQuery
	 */
	public function andWhere($condition, $value = null)
	{
		return $this->where($condition, $value, 'and');
	}
	
	/**
	 * Add where clause with OR condition
	 * 
	 * @param string       $condition   Condition, can contain question mark(s) (?) for parameter insertion.
	 * @param string|array $value       Value(s) to insert in question mark (?) parameters.
	 * @return Microsoft_WindowsAzure_Storage_TableEntityQuery
	 */
	public function orWhere($condition, $value = null)
	{
		return $this->where($condition, $value, 'or');
	}
	
	/**
	 * OrderBy clause
	 * 
	 * @param string $column    Column to sort by
	 * @param string $direction Direction to sort (asc/desc)
	 * @return Microsoft_WindowsAzure_Storage_TableEntityQuery
	 */
	public function orderBy($column, $direction = 'asc')
	{
		$this->_orderBy[] = $column . ' ' . $direction;
		return $this;
	}
    
	/**
	 * Top clause
	 * 
	 * @param int $top  Top to fetch
	 * @return Microsoft_WindowsAzure_Storage_TableEntityQuery
	 */
    public function top($top = null)
    {
        $this->_top  = (int)$top;
        return $this;
    }
	
    /**
     * Assembles the query string
     * 
     * @param boolean $urlEncode Apply URL encoding to the query string
     * @return string
     */
	public function assembleQueryString($urlEncode = false)
	{
		$query = array();
		if (count($this->_where) != 0) {
		    $filter = implode('', $this->_where);
			$query[] = '$filter=' . ($urlEncode ? self::encodeQuery($filter) : $filter);
		}
		
		if (count($this->_orderBy) != 0) {
		    $orderBy = implode(',', $this->_orderBy);
			$query[] = '$orderby=' . ($urlEncode ? self::encodeQuery($orderBy) : $orderBy);
		}
		
		if (!is_null($this->_top)) {
			$query[] = '$top=' . $this->_top;
		}
		
		if (count($query) != 0) {
			return '?' . implode('&', $query);
		}
		
		return '';
	}
	
	/**
	 * Assemble from
	 * 
	 * @param boolean $includeParentheses Include parentheses? ()
	 * @return string
	 */
	public function assembleFrom($includeParentheses = true)
	{
	    $identifier = '';
	    if ($includeParentheses) {
	        $identifier .= '(';
	        
	        if (!is_null($this->_partitionKey)) {
	            $identifier .= 'PartitionKey=\'' . $this->_partitionKey . '\'';
	        }
	            
	        if (!is_null($this->_partitionKey) && !is_null($this->_rowKey)) {
	            $identifier .= ', ';
	        }
	            
	        if (!is_null($this->_rowKey)) {
	            $identifier .= 'RowKey=\'' . $this->_rowKey . '\'';
	        }
	            
	        $identifier .= ')';
	    }
		return $this->_from . $identifier;
	}
	
	/**
	 * Assemble full query
	 * 
	 * @return string
	 */
	public function assembleQuery()
	{
		$assembledQuery = $this->assembleFrom();
		
		$queryString = $this->assembleQueryString();
		if ($queryString !== '') {
			$assembledQuery .= $queryString;
		}
		
		return $assembledQuery;
	}
	
	/**
	 * Quotes a variable into a condition
	 * 
	 * @param string       $text   Condition, can contain question mark(s) (?) for parameter insertion.
	 * @param string|array $value  Value(s) to insert in question mark (?) parameters.
	 * @return string
	 */
	protected function _quoteInto($text, $value = null)
	{
		if (!is_array($value)) {
	        $text = str_replace('?', '\'' . addslashes($value) . '\'', $text);
	    } else {
	        $i = 0;
	        while(strpos($text, '?') !== false) {
	            if (is_numeric($value[$i])) {
	                $text = substr_replace($text, $value[$i++], strpos($text, '?'), 1);
	            } else {
	                $text = substr_replace($text, '\'' . addslashes($value[$i++]) . '\'', strpos($text, '?'), 1);
	            }
	        }
	    }
	    return $text;
	}
	
	/**
	 * Replace operators
	 * 
	 * @param string $text
	 * @return string
	 */
	protected function _replaceOperators($text)
	{
	    $text = str_replace('==', 'eq',  $text);
	    $text = str_replace('>',  'gt',  $text);
	    $text = str_replace('<',  'lt',  $text);
	    $text = str_replace('>=', 'ge',  $text);
	    $text = str_replace('<=', 'le',  $text);
	    $text = str_replace('!=', 'ne',  $text);
	    
	    $text = str_replace('&&', 'and', $text);
	    $text = str_replace('||', 'or',  $text);
	    $text = str_replace('!',  'not', $text);
	    
	    return $text;
	}
	
	/**
	 * urlencode a query
	 * 
	 * @param string $query Query to encode
	 * @return string Encoded query
	 */
	public static function encodeQuery($query)
	{
		$query = str_replace('/', '%2F', $query);
		$query = str_replace('?', '%3F', $query);
		$query = str_replace(':', '%3A', $query);
		$query = str_replace('@', '%40', $query);
		$query = str_replace('&', '%26', $query);
		$query = str_replace('=', '%3D', $query);
		$query = str_replace('+', '%2B', $query);
		$query = str_replace(',', '%2C', $query);
		$query = str_replace('$', '%24', $query);
		
		
		$query = str_replace(' ', '%20', $query);
		
		return $query;
	}
	
	/**
	 * __toString overload
	 * 
	 * @return string
	 */
	public function __toString()
	{
		return $this->assembleQuery();
	}
}